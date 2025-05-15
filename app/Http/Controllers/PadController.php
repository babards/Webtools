<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pad;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\PadApplication;
use Illuminate\Support\Facades\Auth;

class PadController extends Controller
{
    // Show the landlord's pad management page
    public function index(Request $request)
    {
        $query = Pad::where('userID', auth()->id());

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                  ->orWhere('padLocation', 'like', "%{$search}%")
                  ->orWhere('padDescription', 'like', "%{$search}%")
                  ->orWhere('padRent', 'like', "%{$search}%")
                  ->orWhere('padStatus', 'like', "%{$search}%");
            });
        }

        $pads = $query->paginate(8);

        return view('landlord.pads.index', compact('pads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'padName' => 'required',
            'padLocation' => 'required',
            'padRent' => 'required|numeric',
            'padStatus' => 'required|in:available,occupied,maintenance',
            'padImage' => 'nullable|image',
        ]);

        $data = $request->only(['padName', 'padDescription', 'padLocation', 'padRent', 'padStatus']);
        $data['userID'] = auth()->id();
        $data['padCreatedAt'] = now();
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        \App\Models\Pad::create($data);

        // Redirect back with a success message
        return redirect()->route('landlord.pads.index')->with('success', 'Pad created successfully!');
    }

    public function update(Request $request, $id)
    {
        $pad = \App\Models\Pad::findOrFail($id);

        $request->validate([
            'padName' => 'required',
            'padLocation' => 'required',
            'padRent' => 'required|numeric',
            'padStatus' => 'required|in:available,occupied,maintenance',
            'padImage' => 'nullable|image',
        ]);

        $data = $request->only(['padName', 'padDescription', 'padLocation', 'padRent', 'padStatus']);
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        $pad->update($data);

        return redirect()->route('landlord.pads.index')->with('success', 'Pad updated successfully!');
    }

    public function destroy($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        $pad->delete();
        return redirect()->route('landlord.pads.index')->with('success', 'Pad deleted successfully!');
    }

    public function show($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        return view('landlord.pads.show', compact('pad'));
    }

    // Admin view of all pads
    public function adminIndex(Request $request)
    {
        $query = Pad::with('landlord');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                  ->orWhere('padLocation', 'like', "%{$search}%")
                  ->orWhere('padDescription', 'like', "%{$search}%")
                  ->orWhere('padRent', 'like', "%{$search}%")
                  ->orWhere('padStatus', 'like', "%{$search}%");
            })->orWhereHas('landlord', function($q) use ($search) { // Search by landlord name
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $pads = $query->orderBy('padCreatedAt', 'desc')->paginate(8);
        $landlords = User::where('role', 'landlord')->orderBy('first_name')->get(); // Fetch landlords for dropdowns

        return view('admin.pads.index', compact('pads', 'landlords')); // Pass landlords to the index view
    }

    public function adminstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'padName' => 'required|string|max:255',
            'padLocation' => 'required|string|max:255',
            'padRent' => 'required|numeric|min:0',
            'padStatus' => 'required|in:available,occupied,maintenance',
            'userID' => 'required|exists:users,id',
            'padImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.pads.index')
                ->withErrors($validator)
                ->withInput()
                ->with('form_type', 'create');
        }

        $data = $request->only(['padName', 'padDescription', 'padLocation', 'padRent', 'padStatus', 'userID']);
        $data['padCreatedAt'] = now();
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        Pad::create($data);
        return redirect()->route('admin.pads.index')->with('success', 'Pad created successfully!');
    }

    public function adminupdate(Request $request, $id)
    {
        $pad = Pad::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'padName' => 'required|string|max:255',
            'padLocation' => 'required|string|max:255',
            'padRent' => 'required|numeric|min:0',
            'padStatus' => 'required|in:available,occupied,maintenance',
            'userID' => 'required|exists:users,id',
            'padImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.pads.index')
                ->withErrors($validator)
                ->withInput()
                ->with('form_type', 'edit')
                ->with('failed_pad_id', $id);
        }

        $data = $request->only(['padName', 'padDescription', 'padLocation', 'padRent', 'padStatus', 'userID']);
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        $pad->update($data);
        return redirect()->route('admin.pads.index')->with('success', 'Pad updated successfully!');
    }

    public function admindestroy($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        $pad->delete();
        return redirect()->route('admin.pads.index')->with('success', 'Pad deleted successfully!');
    }

    public function adminShow($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        return view('admin.pads.show', compact('pad'));
    }

    // Tenant view of available pads
    public function tenantIndex(Request $request)
    {
        $query = Pad::where('padStatus', 'available');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                  ->orWhere('padLocation', 'like', "%{$search}%")
                  ->orWhere('padDescription', 'like', "%{$search}%")
                  ->orWhere('padRent', 'like', "%{$search}%");
            });
        }

        $pads = $query->paginate(8);
        return view('tenant.pads.index', compact('pads'));
    }

    public function tenantShow($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        return view('tenant.pads.show', compact('pad'));
    }


    // Tenant applies for a pad
    public function tenantApply(Request $request, $padId)
    {
        $pad = Pad::where('padID', $padId)->where('padStatus', 'available')->firstOrFail();
        $tenant = Auth::user();

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $existingApplication = PadApplication::where('pad_id', $pad->padID)
            ->where('user_id', $tenant->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You already have an active application for this pad.');
        }

        PadApplication::create([
            'pad_id' => $pad->padID,
            'user_id' => $tenant->id,
            'status' => 'pending',
            'application_date' => now(),
            'message' => $request->input('message'),
        ]);

        return redirect()->route('tenant.pads.index')->with('success', 'Application submitted successfully!');
    }

    // Tenant views their applications
    public function tenantMyApplications()
    {
        $applications = PadApplication::with('pad')
            ->where('user_id', Auth::id())
            ->orderBy('application_date', 'desc')
            ->paginate(10);
        
        return view('tenant.applications.index', compact('applications'));
    }

    // Landlord views applications for a specific pad
    public function landlordViewApplications($padId)
    {
        $pad = Pad::where('padID', $padId)->where('userID', Auth::id())->firstOrFail();
        $applications = PadApplication::with('tenant')
            ->where('pad_id', $pad->padID)
            ->orderBy('application_date', 'desc')
            ->paginate(10);

        return view('landlord.pads.application', compact('pad', 'applications'));
    }

    // Landlord approves an application
    public function landlordApproveApplication(Request $request, $applicationId)
    {
        $application = PadApplication::with('pad')->findOrFail($applicationId);
        $pad = $application->pad;

        // Ensure the authenticated user is the landlord of the pad
        if ($pad->userID !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($application->status === 'pending') {
            $application->status = 'approved';
            $application->save();

            // Increment number of boarders
            $pad->increment('number_of_boarders');
            
            // Optionally, you might want to change pad status if it reaches capacity,
            // or reject other pending applications for this pad. For now, just approve.

            return redirect()->back()->with('success', 'Application approved successfully.');
        }
        return redirect()->back()->with('error', 'Application cannot be approved.');
    }

    // Landlord rejects an application
    public function landlordRejectApplication(Request $request, $applicationId)
    {
        $application = PadApplication::with('pad')->findOrFail($applicationId);
        $pad = $application->pad;

        // Ensure the authenticated user is the landlord of the pad
        if ($pad->userID !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($application->status === 'pending') {
            $application->status = 'rejected';
            $application->save();
            return redirect()->back()->with('success', 'Application rejected successfully.');
        }
        return redirect()->back()->with('error', 'Application cannot be rejected.');
    }

    public function landlordAllApplications()
    {
        // Get all applications for pads owned by the current landlord
        $applications = \App\Models\PadApplication::with(['pad', 'tenant'])
            ->whereHas('pad', function($query) {
                $query->where('userID', auth()->id());
            })
            ->orderBy('application_date', 'desc')
            ->paginate(15);

        return view('landlord.applications.index', compact('applications'));
    }

    public function tenantCancelApplication($applicationId)
    {
        $application = \App\Models\PadApplication::where('id', $applicationId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->firstOrFail();

        $application->status = 'cancelled';
        $application->save();

        return redirect()->back()->with('success', 'Application cancelled successfully.');
    }
}

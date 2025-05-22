<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pad;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\PadApplication;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class PadController extends Controller
{
    use LogsActivity;

    // Show the landlord's pad management page
    public function index(Request $request)
    {
        $query = Pad::where('userID', auth()->id());

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                    ->orWhere('padLocation', 'like', "%{$search}%")
                    ->orWhere('padDescription', 'like', "%{$search}%")
                    ->orWhere('padRent', 'like', "%{$search}%")
                    ->orWhere('padStatus', 'like', "%{$search}%");
            });
        }

        // Add location filter
        if ($request->filled('location_filter')) {
            $city = $request->input('location_filter');
            $query->whereRaw("TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(padLocation, ',', 3), ',', -1)) LIKE ?", ["%$city%"]);
        }

        // Add price range filter
        if ($request->filled('price_filter')) {
            switch ($request->input('price_filter')) {
                case 'below_1000':
                    $query->where('padRent', '<', 1000);
                    break;
                case '1000_2000':
                    $query->whereBetween('padRent', [1000, 2000]);
                    break;
                case '2000_3000':
                    $query->whereBetween('padRent', [2000, 3000]);
                    break;
                case 'above_3000':
                    $query->where('padRent', '>', 3000);
                    break;
            }
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data = $request->only([
            'padName',
            'padDescription',
            'padLocation',
            'padRent',
            'padStatus',
            'latitude',
            'longitude'
        ]);

        $data['userID'] = auth()->id();
        $data['padCreatedAt'] = now();
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        $pad = \App\Models\Pad::create($data);

        $this->logActivity('create_pad', "Created new pad: {$pad->padName}");

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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data = $request->only([
            'padName',
            'padDescription',
            'padLocation',
            'padRent',
            'padStatus',
            'latitude',
            'longitude',
        ]);

        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        $pad->update($data);

        $this->logActivity('update_pad', "Updated pad: {$pad->padName}");

        return redirect()->route('landlord.pads.index')->with('success', 'Pad updated successfully!');
    }


    public function destroy($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        $padName = $pad->padName;
        $pad->delete();

        $this->logActivity('delete_pad', "Deleted pad: {$padName}");

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
            $query->where(function ($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                    ->orWhere('padLocation', 'like', "%{$search}%")
                    ->orWhere('padDescription', 'like', "%{$search}%")
                    ->orWhere('padRent', 'like', "%{$search}%")
                    ->orWhere('padStatus', 'like', "%{$search}%")
                    ->orWhereHas('landlord', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })->orWhereHas('landlord', function ($q) use ($search) { // Search by landlord name
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Add landlord filter
        if ($request->filled('landlord_filter')) {
            $query->where('userID', $request->input('landlord_filter'));
        }

        // Add location filter
        if ($request->filled('location_filter')) {
            $city = $request->input('location_filter');
            $query->whereRaw("TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(padLocation, ',', 3), ',', -1)) LIKE ?", ["%$city%"]);
        }

        // Add price range filter
        if ($request->filled('price_filter')) {
            switch ($request->input('price_filter')) {
                case 'below_1000':
                    $query->where('padRent', '<', 1000);
                    break;
                case '1000_2000':
                    $query->whereBetween('padRent', [1000, 2000]);
                    break;
                case '2000_3000':
                    $query->whereBetween('padRent', [2000, 3000]);
                    break;
                case 'above_3000':
                    $query->where('padRent', '>', 3000);
                    break;
            }
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.pads.index')
                ->withErrors($validator)
                ->withInput()
                ->with('form_type', 'create');
        }

        $data = $request->only([
            'padName',
            'padDescription',
            'padLocation',
            'padRent',
            'padStatus',
            'userID',
            'latitude',
            'longitude'
        ]);

        $data['padCreatedAt'] = now();
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        $pad = Pad::create($data);

        $this->logActivity('admin_create_pad', "Admin created pad: {$pad->padName}");

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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.pads.index')
                ->withErrors($validator)
                ->withInput()
                ->with('form_type', 'edit')
                ->with('failed_pad_id', $id);
        }

        $data = $request->only([
            'padName',
            'padDescription',
            'padLocation',
            'padRent',
            'padStatus',
            'userID',
            'latitude',
            'longitude'
        ]);
        $data['padUpdatedAt'] = now();

        if ($request->hasFile('padImage')) {
            $data['padImage'] = $request->file('padImage')->store('pads', 'public');
        }

        $pad->update($data);

        $this->logActivity('admin_update_pad', "Admin updated pad: {$pad->padName}");

        return redirect()->route('admin.pads.index')->with('success', 'Pad updated successfully!');
    }


    public function admindestroy($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        $padName = $pad->padName;
        $pad->delete();

        $this->logActivity('admin_delete_pad', "Admin deleted pad: {$padName}");

        return redirect()->route('admin.pads.index')->with('success', 'Pad deleted successfully!');
    }

    public function adminShow($id)
    {
        $pad = \App\Models\Pad::findOrFail($id);
        $landlords = \App\Models\User::where('role', 'landlord')->orderBy('first_name')->get();
        return view('admin.pads.show', compact('pad', 'landlords'));
    }

    // Tenant view of available pads
    public function tenantIndex(Request $request)
    {
        $query = Pad::where('padStatus', 'available');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                    ->orWhere('padLocation', 'like', "%{$search}%")
                    ->orWhere('padDescription', 'like', "%{$search}%")
                    ->orWhere('padRent', 'like', "%{$search}%")
                    ->orWhere('padStatus', 'like', "%{$search}%")
                    ->orWhereHas('landlord', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Landlord filter
        if ($request->filled('landlord_filter')) {
            $query->where('userID', $request->input('landlord_filter'));
        }

        // Location filter
        if ($request->filled('location_filter')) {
            $city = $request->input('location_filter');
            $query->whereRaw("TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(padLocation, ',', 3), ',', -1)) LIKE ?", ["%$city%"]);
        }

        // Price range filter
        if ($request->filled('price_filter')) {
            switch ($request->input('price_filter')) {
                case 'below_1000':
                    $query->where('padRent', '<', 1000);
                    break;
                case '1000_2000':
                    $query->whereBetween('padRent', [1000, 2000]);
                    break;
                case '2000_3000':
                    $query->whereBetween('padRent', [2000, 3000]);
                    break;
                case 'above_3000':
                    $query->where('padRent', '>', 3000);
                    break;
            }
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

        $application = PadApplication::create([
            'pad_id' => $pad->padID,
            'user_id' => $tenant->id,
            'status' => 'pending',
            'application_date' => now(),
            'message' => $request->input('message'),
        ]);

        $this->logActivity('apply_pad', "Applied for pad: {$pad->padName}");

        return redirect()->route('tenant.pads.index')->with('success', 'Application submitted successfully!');
    }

    // Tenant views their applications
    public function tenantMyApplications(Request $request)
    {
        $applications = PadApplication::with('pad')
            ->where('user_id', Auth::id());

        // Search filter (searches pad name, location, message, and status)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $applications = $applications->where(function ($q) use ($search) {
                $q->whereHas('pad', function ($q2) use ($search) {
                    $q2->where('padName', 'like', "%{$search}%")
                        ->orWhere('padLocation', 'like', "%{$search}%");
                })
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Pad name filter
        if ($request->filled('pad_filter')) {
            $applications = $applications->whereHas('pad', function ($q) use ($request) {
                $q->where('padName', $request->input('pad_filter'));
            });
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $applications = $applications->where('status', $request->input('status_filter'));
        }

        $applications = $applications->orderBy('application_date', 'desc')->paginate(10);
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
        $application = PadApplication::with(['pad', 'tenant'])->findOrFail($applicationId);
        $pad = $application->pad;
        $tenant = $application->tenant;

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

            $tenantInfo = $tenant ? $tenant->first_name . ' ' . $tenant->last_name . ' (' . $tenant->email . ')' : 'Unknown Tenant';
            $this->logActivity('approve_application', "Approved application for pad: {$pad->padName} | Tenant: {$tenantInfo}");

            return redirect()->back()->with('success', 'Application approved successfully.');
        }
        return redirect()->back()->with('error', 'Application cannot be approved.');
    }

    // Landlord rejects an application
    public function landlordRejectApplication(Request $request, $applicationId)
    {
        $application = PadApplication::with(['pad', 'tenant'])->findOrFail($applicationId);
        $pad = $application->pad;
        $tenant = $application->tenant;

        // Ensure the authenticated user is the landlord of the pad
        if ($pad->userID !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($application->status === 'pending') {
            $application->status = 'rejected';
            $application->save();

            $tenantInfo = $tenant ? $tenant->first_name . ' ' . $tenant->last_name . ' (' . $tenant->email . ')' : 'Unknown Tenant';
            $this->logActivity('reject_application', "Rejected application for pad: {$pad->padName} | Tenant: {$tenantInfo}");

            return redirect()->back()->with('success', 'Application rejected successfully.');
        }
        return redirect()->back()->with('error', 'Application cannot be rejected.');
    }

    public function landlordAllApplications(Request $request)
    {
        $applications = \App\Models\PadApplication::with(['pad', 'tenant'])
            ->whereHas('pad', function ($query) {
                $query->where('userID', auth()->id());
            });

        // Search filter (searches pad name, tenant name, and message)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $applications = $applications->where(function ($q) use ($search) {
                $q->whereHas('pad', function ($q2) use ($search) {
                    $q2->where('padName', 'like', "%{$search}%");
                })
                    ->orWhereHas('tenant', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Pad name filter
        if ($request->filled('pad_filter')) {
            $applications = $applications->whereHas('pad', function ($q) use ($request) {
                $q->where('padName', $request->input('pad_filter'));
            });
        }

        // Tenant filter
        if ($request->filled('tenant_filter')) {
            $applications = $applications->where('user_id', $request->input('tenant_filter'));
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $applications = $applications->where('status', $request->input('status_filter'));
        }

        $applications = $applications->orderBy('application_date', 'desc')->paginate(15);

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

        $this->logActivity('cancel_application', "Cancelled application for pad: {$application->pad->padName}");

        return redirect()->back()->with('success', 'Application cancelled successfully.');
    }
}

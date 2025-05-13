<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pad;
use Illuminate\Support\Carbon;

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
            });
        }

        $pads = $query->paginate(8);
        return view('Admin.pads.index', compact('pads'));
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
        return view('Tenant.pads.index', compact('pads'));
    }
}

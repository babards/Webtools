<?php

namespace App\Http\Controllers;

use App\Models\Pad;
use Illuminate\Http\Request;

class PadController extends Controller
{
    // Show all pads for admin
    public function adminIndex(Request $request)
    {
        $query = \App\Models\Pad::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                  ->orWhere('padLocation', 'like', "%{$search}%")
                  ->orWhere('padDescription', 'like', "%{$search}%");
            });
        }

        $pads = $query->with('landlord')->paginate(10); // eager load landlord

        return view('Admin.pads', compact('pads'));
    }
}

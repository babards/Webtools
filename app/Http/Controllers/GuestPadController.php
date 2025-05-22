<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pad;
use App\Models\PadApplication;
use Illuminate\Support\Facades\Mail;

class GuestPadController extends Controller
{
    // Show pad details to guests
    public function show($pad)
    {
        $pad = Pad::with('landlord')->findOrFail($pad);
        return view('Guest.pads.show', compact('pad'));
    }

    // Handle guest application
    public function apply(Request $request, $padId)
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $pad = Pad::findOrFail($padId);

        // Optionally, you can save to PadApplication or send an email to landlord
        // Here, we'll save as a PadApplication with guest info in the message
        PadApplication::create([
            'pad_id' => $pad->padID,
            'tenant_id' => null, // guest
            'message' => "Guest Name: {$request->guest_name}\nGuest Email: {$request->guest_email}\nMessage: {$request->message}",
            'status' => 'pending',
        ]);

        // Optionally, notify landlord via email (uncomment if needed)
        // Mail::to($pad->landlord->email)->send(new GuestPadApplicationMail($pad, $request->guest_name, $request->guest_email, $request->message));

        return back()->with('success', 'Your application has been submitted!');
    }

    // Landing page with filters
    public function index(Request $request)
    {
        $query = Pad::where('padStatus', 'available');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('padName', 'like', "%{$search}%")
                    ->orWhere('padLocation', 'like', "%{$search}%")
                    ->orWhere('padDescription', 'like', "%{$search}%")
                    ->orWhere('padRent', 'like', "%{$search}%");
            });
        }

        // Location filter
        if ($request->filled('location_filter')) {
            $city = $request->input('location_filter');
            $query->where('padLocation', 'like', "%$city%");
        }

        // Price filter
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

        $pads = $query->orderBy('padCreatedAt', 'desc')->paginate(9);
        return view('welcome', compact('pads'));
    }
} 
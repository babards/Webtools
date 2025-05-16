<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pad;
use App\Models\PadApplication;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        switch($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'landlord':
                return redirect()->route('landlord.pads.index');
            case 'tenant':
                return redirect()->route('tenant.pads.index');
            default:
                return redirect()->route('login');
        }
    }

    public function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_landlords' => User::where('role', 'landlord')->count(),
            'total_tenants' => User::where('role', 'tenant')->count(),
            'total_pads' => Pad::count(),
            'available_pads' => Pad::where('padStatus', 'available')->count(),
            'total_applications' => PadApplication::count(),
            'pending_applications' => PadApplication::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
} 
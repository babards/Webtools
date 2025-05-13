<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                return view('Admin.dashboard');
            case 'landlord':
                return view('Landlord.dashboard');
            case 'tenant':
                return view('Tenant.dashboard');
            default:
                abort(403, 'Unauthorized role.');
        }
    }
} 
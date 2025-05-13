<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PadController;

Route::get('/verify-email/{token}', [EmailVerificationController::class,'verifyEmail'])
    ->name('verify.email');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');


Route::get('/2fa/verify', [TwoFactorAuthController::class, 'verifyForm'])->name('2fa.verify.form');
Route::post('/2fa/verify', [AuthController::class, 'verify2FA'])->name('2fa.verify');
Route::post('/2fa/resend', [AuthController::class, 'resend2FACode'])->name('2fa.resend');


// Public routes
Route::get('/', [AuthController::class, 'showRegisterForm'])->name('register');

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/admin/pads', [PadController::class, 'adminIndex'])->name('admin.pads.index');
    });

    // Landlord routes
    Route::middleware(['role:landlord'])->group(function () {
        Route::get('/pads', [PadController::class, 'index'])->name('landlord.pads.index');
        Route::get('/pads/{pad}', [PadController::class, 'show'])->name('pads.show');
        Route::resource('pads', PadController::class)->except(['index', 'show']);
    });

    // Tenant routes
    Route::middleware(['role:tenant'])->group(function () {
        Route::get('/tenant/pads', [PadController::class, 'tenantIndex'])->name('tenant.pads.index');
        Route::get('/tenant/pads/{pad}', [PadController::class, 'show'])->name('tenant.pads.show');
    });
});

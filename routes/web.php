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
use App\Http\Controllers\GuestPadController;

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
Route::get('/', [App\Http\Controllers\GuestPadController::class, 'index'])->name('welcome');

// Guest pad details and application
Route::get('/pads/{pad}', [App\Http\Controllers\GuestPadController::class, 'show'])->name('guest.pads.show');
Route::post('/pads/{padId}/apply', [App\Http\Controllers\GuestPadController::class, 'apply'])->name('guest.pads.apply');

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
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::resource('users', UserController::class);

        // Admin Pad Management Routes
        Route::get('pads', [PadController::class, 'adminIndex'])->name('pads.index');
        Route::post('pads', [PadController::class, 'adminstore'])->name('pads.store');
        Route::get('pads/create', [PadController::class, 'adminCreate'])->name('pads.create');
        Route::get('pads/{pad}', [PadController::class, 'adminShow'])->name('pads.show');
        Route::get('pads/{pad}/edit', [PadController::class, 'adminEdit'])->name('pads.edit');
        Route::put('pads/{pad}', [PadController::class, 'adminupdate'])->name('pads.update');
        Route::delete('pads/{pad}', [PadController::class, 'admindestroy'])->name('pads.destroy');

        // Logs
        Route::get('/logs', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs.index');
        Route::get('/logs/export', [App\Http\Controllers\Admin\LogController::class, 'export'])->name('logs.export');
    });

    // Landlord routes
    Route::middleware(['role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
        Route::get('/pads', [PadController::class, 'index'])->name('pads.index');
        Route::get('/pads/create', [PadController::class, 'create'])->name('pads.create');
        Route::post('/pads', [PadController::class, 'store'])->name('pads.store');
        Route::get('/pads/{pad}', [PadController::class, 'show'])->name('pads.show');
        Route::get('/pads/{pad}/edit', [PadController::class, 'edit'])->name('pads.edit');
        Route::put('/pads/{pad}', [PadController::class, 'update'])->name('pads.update');
        Route::delete('/pads/{pad}', [PadController::class, 'destroy'])->name('pads.destroy');
        
        // Application management for landlords
        Route::get('/pads/{padId}/applications', [PadController::class, 'landlordViewApplications'])->name('pads.applications');
        Route::post('/applications/{applicationId}/approve', [PadController::class, 'landlordApproveApplication'])->name('applications.approve');
        Route::post('/applications/{applicationId}/reject', [PadController::class, 'landlordRejectApplication'])->name('applications.reject');
        Route::get('/applications', [PadController::class, 'landlordAllApplications'])->name('applications.all');
        Route::get('/pads/{padId}/boarders', [PadController::class, 'landlordViewBoarders'])->name('pads.boarders');
        Route::get('/boarders', [PadController::class, 'landlordAllBoarders'])->name('boarders.all');
        Route::post('/boarders/{boardersId}/kicked', [PadController::class, 'landlordKickBoarders'])->name('boarders.kicked');
        Route::get('/applications/export', [PadController::class, 'landlordExportApplications'])->name('applications.export');
        Route::get('/pads/{padId}/applications/export', [PadController::class, 'landlordExportApplications'])->name('pads.applications.export');
        Route::get('/boarders/export', [PadController::class, 'landlordExportBoarders'])->name('boarders.export');
    });

    // Tenant routes
    Route::middleware(['role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
        Route::get('/pads', [PadController::class, 'tenantIndex'])->name('pads.index');
        Route::get('/pads/{pad}', [PadController::class, 'tenantShow'])->name('pads.show');

        // Pad application for tenants
        Route::post('/pads/{padId}/apply', [PadController::class, 'tenantApply'])->name('pads.apply');
        Route::get('/my-applications', [PadController::class, 'tenantMyApplications'])->name('applications.index');
        Route::post('/applications/{applicationId}/cancel', [PadController::class, 'tenantCancelApplication'])->name('applications.cancel');
    });
});

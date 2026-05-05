<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| SPA Web Routes - All routes go through Vue Router
|--------------------------------------------------------------------------
*/

// SPA entry point - single blade view for all routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|storage|favicon\.ico).*$');

// Auth routes (Breeze - form submissions, not SPA)
Route::middleware('guest')->group(function () {
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login');
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/api/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/api/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/api/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Contact form (no ContactController — handled inline)
Route::post('/contact', function () {
    return back()->with('success', 'Mensaje enviado correctamente');
})->name('contact.submit');

// User API endpoint (includes roles with permissions for frontend role-based UI)
Route::middleware('auth')->get('/api/user', function () {
    return response()->json(Auth::user()->load('roles:id,name,permissions:id,name'));
});

// Dashboard API
Route::middleware('auth')->get('/api/dashboard/datos', [DashboardController::class, 'obtenerDatosDashboard'])->name('dashboard.datos');

// API routes for all CRUD resources
require __DIR__ . '/api.php';

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VillaController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Public Pages
Route::get('/villas', [PagesController::class, 'villas'])->name('pages.villas');
Route::get('/villas/{slug}', [PagesController::class, 'villaDetail'])->name('pages.villa-detail');

Route::middleware(['auth', 'role:admin', 'admin', 'log.admin'])->prefix('admin')->group(function () {
    Route::get('/', [AuthController::class, 'admin'])->name('admin.dashboard');

    // Villa Management
    Route::resource('villas', VillaController::class);
    Route::post('villas/reorder', [VillaController::class, 'reorder'])->name('villas.reorder');
    Route::delete('villas/{villa}/force-delete', [VillaController::class, 'forceDelete'])->name('villas.forceDelete');
});

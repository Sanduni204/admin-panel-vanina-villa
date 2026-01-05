<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DineRelaxController;
use App\Http\Controllers\DineRelaxMenuController;
use App\Http\Controllers\DineRelaxPageController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\VillaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')
        ->middleware('throttle:5,1'); // 5 attempts per minute
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')
        ->middleware('throttle:5,1'); // 5 attempts per minute
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Public Pages
Route::get('/villas', [PagesController::class, 'villas'])->name('pages.villas');
Route::get('/villas/{slug}', [PagesController::class, 'villaDetail'])->name('pages.villa-detail');
Route::get('/dine-relax', [DineRelaxPageController::class, 'show'])->name('dine-relax.show');
Route::get('/dine-relax/menu/{type}/download', [DineRelaxMenuController::class, 'download'])
    ->whereIn('type', ['beverage', 'snacking', 'today', 'breakfast'])
    ->middleware('signed')
    ->name('dine-relax.menu.download');

Route::middleware(['auth', 'role:admin', 'admin', 'log.admin'])->prefix('admin')->group(function () {
    Route::get('/', [AuthController::class, 'admin'])->name('admin.dashboard');

    // Villa Management
    // Note: Specific routes must come BEFORE resource routes to avoid conflicts
    Route::post('villas/reorder', [VillaController::class, 'reorder'])->name('villas.reorder');
    Route::delete('villas/{villa}/media/{media}', [VillaController::class, 'deleteMedia'])->name('villas.media.delete');
    Route::delete('villas/{villa}/force-delete', [VillaController::class, 'forceDelete'])->name('villas.forceDelete');
    Route::resource('villas', VillaController::class);

    // Dine & Relax
    Route::get('dine-relax', [DineRelaxController::class, 'edit'])->name('dine-relax.edit');
    Route::post('dine-relax', [DineRelaxController::class, 'update'])->name('dine-relax.update');

    // Dine & Relax Hero
    Route::get('dine-relax/hero/edit', [DineRelaxController::class, 'heroEdit'])->name('dine-relax.hero.edit');
    Route::put('dine-relax/hero', [DineRelaxController::class, 'heroUpdate'])->name('dine-relax.hero.update');

    // Dine & Relax Menus
    Route::get('dine-relax/menus', [DineRelaxMenuController::class, 'index'])->name('dine-relax.menus.index');
    Route::post('dine-relax/menus/{type}', [DineRelaxMenuController::class, 'storeOrUpdate'])->name('dine-relax.menus.save');
    Route::post('dine-relax/menus/{type}/toggle', [DineRelaxMenuController::class, 'toggle'])->name('dine-relax.menus.toggle');

    // Dine & Relax Blocks
    Route::get('dine-relax/blocks/create', [DineRelaxController::class, 'blockCreate'])->name('dine-relax.blocks.create');
    Route::post('dine-relax/blocks', [DineRelaxController::class, 'blockStore'])->name('dine-relax.blocks.store');
    Route::get('dine-relax/blocks/{block}/edit', [DineRelaxController::class, 'blockEdit'])->name('dine-relax.blocks.edit');
    Route::put('dine-relax/blocks/{block}', [DineRelaxController::class, 'blockStore'])->whereNumber('block')->name('dine-relax.blocks.update');
    Route::delete('dine-relax/blocks/{block}', [DineRelaxController::class, 'blockDelete'])->name('dine-relax.blocks.delete');
});

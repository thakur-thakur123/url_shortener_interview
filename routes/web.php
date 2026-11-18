<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

// Public: short URL resolver
Route::get('/s/{code}', RedirectController::class)->name('short.resolve');

// Public: accept an invitation
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');

// Logged-in users
Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/invitations', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');

    Route::get('/urls', [ShortUrlController::class, 'index'])->name('urls.index');
    Route::get('/urls/create', [ShortUrlController::class, 'create'])->name('urls.create');
    Route::post('/urls', [ShortUrlController::class, 'store'])->name('urls.store');
});

// Laravel Breeze: login, logout, forgot/reset password (no registration)
require __DIR__.'/auth.php';

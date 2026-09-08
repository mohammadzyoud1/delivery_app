<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified.email'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('home');
    })->name('home');
});

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

Route::get('/verify-email', [EmailVerificationController::class, 'show'])
    ->name('verify.email');

Route::post('/verify-email', [EmailVerificationController::class, 'verify'])
    ->name('verify.email.verify');

<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;


// Guest routes (accessible only when NOT logged in)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'handleLogin'])->name('auth.handleLogin');
    Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('handleRegister', [AuthController::class, 'handleRegister'])->name('auth.handleRegister');
});

// Protected routes (accessible only when logged in)
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Payment Routes
    Route::get('/topup', [PaymentController::class, 'showTopUpForm'])->name('payment.topup');
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
});

// SePay Callback (Webhook) 
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

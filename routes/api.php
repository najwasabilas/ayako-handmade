<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokuPaymentController;
use App\Http\Controllers\AuthController;

// CALLBACK (POST dari DOKU)
Route::post('/payment/callback', [DokuPaymentController::class, 'callback'])->name('doku.callback');
Route::get('/payment/finish', [DokuPaymentController::class, 'afterPayment'])->name('doku.finish');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

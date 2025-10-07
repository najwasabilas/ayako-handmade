<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryController;

Route::get('/', function () {
    return view('home');
})->name('home');


// Fitur Auth
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// customer Profile (hanya untuk customer yg login)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::post('/profile', [CustomerController::class, 'updateProfile']);
});

// Profil UMKM
Route::get('/profile-umkm', [ProfileController::class, 'index'])->name('profile-umkm');

// Gallery
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery');
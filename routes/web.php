<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;

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

// Catalog and Product
Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog');
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');

// Checkout and Cart
Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::get('/checkout-now', [OrderController::class, 'checkoutNow'])->name('checkout.now');
    Route::get('/checkout', [OrderController::class, 'showCheckoutPage'])->name('checkout.page');
});

// CartController
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/update', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/keranjang/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/keranjang/checkout', [CartController::class, 'checkoutSelected'])->name('cart.checkout');
});
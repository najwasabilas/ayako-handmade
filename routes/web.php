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
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderListController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DokuPaymentController;

use App\Models\Product;
Route::get('/', function () {
    $products = Product::latest()->take(6)->get();
    return view('home', compact('products'));
})->name('home');


// Fitur Auth
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/resend-otp/{email}', [AuthController::class, 'resendOtp'])->name('resend.otp');
Route::get('/email-verified-success', function () {
    return view('auth.email-verified-success');
})->name('email.verified.success');


// Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/export-pdf', [AdminController::class, 'exportPdf'])->name('admin.export.pdf');
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/orders/update-status', [AdminController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products.index');
    Route::get('/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::get('/admin/fabric', [AdminController::class, 'fabrics'])->name('admin.fabric.index');
    Route::post('/admin/fabric', [AdminController::class, 'storeFabric'])->name('admin.fabric.store');
    Route::get('/admin/fabric/{fabric}/edit', [AdminController::class, 'editFabric'])->name('admin.fabric.edit');
    Route::put('/admin/fabric/{fabric}', [AdminController::class, 'updateFabric'])->name('admin.fabric.update');
    Route::delete('/admin/fabric/{fabric}', [AdminController::class, 'deleteFabric'])->name('admin.fabric.destroy');
    Route::delete('/admin/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.products.destroy');
    Route::delete('/admin/products/delete-image/{id}', [AdminController::class, 'deleteImage'])
    ->name('admin.products.delete-image');
});


// customer Profile (hanya untuk customer yg login)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::post('/profile', [CustomerController::class, 'updateProfile']);
    Route::post('/profile/address', [ProfileController::class, 'updateAddress'])->middleware('auth');
    Route::delete('/checkout/delete-address/{id}', [ProfileController::class, 'deleteAddress'])->name('checkout.deleteAddress');
});

// Profil UMKM
Route::get('/profile-umkm', [ProfileController::class, 'index'])->name('profile-umkm');

// Gallery
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery');

// Catalog and Product
Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog');
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');
Route::get('/katalog/load-more', [CatalogController::class, 'loadMore']);


// CALLBACK (POST dari DOKU)
Route::post('/payment/callback', [DokuPaymentController::class, 'callback'])->name('doku.callback');
Route::get('/payment/finish', [DokuPaymentController::class, 'afterPayment'])->name('doku.finish');

// Checkout and Cart
Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::get('/checkout-now', [OrderController::class, 'checkoutNow'])->name('checkout.now');
    Route::get('/checkout', [OrderController::class, 'showCheckoutPage'])->name('checkout.page');
    // BUAT PEMBAYARAN
    Route::get('/payment/doku/{orderId}', [DokuPaymentController::class, 'create'])
        ->name('doku.create');
});

// CartController
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/update', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/keranjang/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/keranjang/checkout', [CartController::class, 'checkoutSelected'])->name('cart.checkout');
});

// CheckoutController
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.page');
    Route::post('/checkout/place', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/checkout/payment/{id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
});

// OrderListController
Route::middleware(['auth'])->group(function () {
    Route::get('/pesanan-saya', [OrderListController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [OrderListController::class, 'show'])->name('orders.show');
    Route::delete('/pesanan/{id}', [OrderListController::class, 'destroy'])->name('orders.destroy');
});

// FabricControler
Route::get('/fabric', [FabricController::class, 'index'])->name('fabric.index');

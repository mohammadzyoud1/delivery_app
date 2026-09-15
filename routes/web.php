<?php

use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([])->group(function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');
});

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->middleware('guest')->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

Route::get('/verify-email', [EmailVerificationController::class, 'show'])
    ->name('verify.email');

Route::post('/verify-email', [EmailVerificationController::class, 'verify'])
    ->name('verify.email.verify');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->name('categories.show');
Route::middleware(['auth', 'verified.email', 'seller.access'])->group(function () {
    // ==================== Seller Routes ====================

    Route::get('/seller/products', [
        \App\Http\Controllers\Seller\ProductController::class,
        'index',
    ])->name('seller.products.index');

    Route::get('/seller/products/create', [
        \App\Http\Controllers\Seller\ProductController::class,
        'create',
    ])->name('seller.products.create');

    Route::post('/seller/products', [
        \App\Http\Controllers\Seller\ProductController::class,
        'store',
    ])->name('seller.products.store');
    Route::get('/seller', [
        ProductController::class,
        'dashboard',
    ])->name('seller.dashboard');

    Route::get('/seller/orders', [
        ProductController::class,
        'orders',
    ])->name('seller.orders');

    Route::get('/seller/products-sold', [
        ProductController::class,
        'productsSold',
    ])->name('seller.products.sold');

    Route::get('/seller/revenue', [
        ProductController::class,
        'revenue',
    ])->name('seller.revenue');

    // ==================== Edit Product ====================

    Route::get('/seller/products/{product}/edit', [
        ProductController::class,
        'edit',
    ])->name('seller.products.edit');

    Route::put('/seller/products/{product}', [
        ProductController::class,
        'update',
    ])->name('seller.products.update');

    Route::patch('/seller/products/{product}/toggle-status', [
        ProductController::class,
        'toggleStatus',
    ])->name('seller.products.toggle-status');
});
Route::get('/products/{product}', [
    CatalogController::class,
    'show',
])->name('products.show');

<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDeliveryController;
use App\Http\Controllers\Admin\AdminDeliveryPageController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminOrderDetailsController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSellerApplicationController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerApplicationController;
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

// ==================== Email Verification ====================

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [EmailVerificationController::class, 'show'])
        ->name('verify.email');

    Route::post('/verify-email', [EmailVerificationController::class, 'verify'])
        ->name('verify.email.verify');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/forgot-password', function () {
    return Inertia::render('Auth/ForgotPassword');
})->name('password.request');

Route::post('/forgot-password', [
    AuthController::class,
    'forgotPassword',
])->name('password.email');

Route::get('/reset-password/{token}', [
    AuthController::class,
    'showResetPassword',
])->name('password.reset');

Route::post('/reset-password', [
    AuthController::class,
    'resetPassword',
])->name('password.update');

Route::middleware(['auth', 'verified.email'])->group(function () {
    // ==================== Cart ====================
    Route::get('/cart', [
        CartController::class,
        'index',
    ])->name('cart.index');
    Route::post('/cart/items', [
        CartController::class,
        'store',
    ])->name('cart.items.store');

    Route::patch('/cart/items/{cartItem}', [
        CartController::class,
        'update',
    ])->name('cart.items.update');

    Route::delete('/cart/items/{cartItem}', [
        CartController::class,
        'destroy',
    ])->name('cart.items.destroy');

    // ==================== Orders ====================
    Route::get('/orders', [
        OrderController::class,
        'index',
    ])->name('orders.index');

    Route::get('/checkout', [
        CheckoutController::class,
        'index',
    ])->name('checkout.index');

    // ==================== Place Order ====================

    Route::post('/checkout', [
        CheckoutController::class,
        'store',
    ])->name('checkout.store');

    // ==================== Order Details ====================

    Route::get('/orders/{order}', [
        OrderController::class,
        'show',
    ])->name('orders.show');

    Route::get('/seller/apply', [
        SellerApplicationController::class,
        'create',
    ])->name('seller.apply');

    Route::post('/seller/apply', [
        SellerApplicationController::class,
        'store',
    ])->name('seller.apply.store');

    Route::get('/messages', [
        ConversationController::class,
        'index',
    ])->name('messages.index');

    Route::get('/messages/support', [
        ConversationController::class,
        'startSupport',
    ])->name('messages.support');

    Route::get('/messages/{conversation}', [
        ConversationController::class,
        'show',
    ])->name('messages.show');

    Route::post('/messages/{conversation}', [
        ConversationController::class,
        'send',
    ])->name('messages.send');
    Route::get('/profile', function () {
        return inertia::render('Profile/Profile');
    })->name('profile');
});

Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->name('categories.show');

// ==================== Delivery Routes====================

Route::middleware(['auth', 'verified.email', 'delivery.access'])->group(function () {
    Route::get('/delivery/orders', [
        DeliveryController::class,
        'index',
    ])->name('delivery.orders');
    Route::post('/delivery/orders/{order}/take', [
        DeliveryController::class,
        'take',
    ])->name('delivery.orders.take');
    // ==================== Delivery Status ====================

    Route::patch('/delivery/orders/{order}/status', [
        DeliveryController::class,
        'updateStatus',
    ])->name('delivery.orders.status');
});

Route::middleware(['auth', 'verified.email', 'seller.access'])->group(function () {
    // ==================== Seller Routes ====================

    Route::get('/seller/products', [
        ProductController::class,
        'index',
    ])->name('seller.products.index');

    Route::get('/seller/products/create', [
        ProductController::class,
        'create',
    ])->name('seller.products.create');

    Route::post('/seller/products', [
        ProductController::class,
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

    // ==================== Checkout ====================
});

Route::get('/products/{product}', [
    CatalogController::class,
    'show',
])->name('products.show');

// ==================== Admin ====================

Route::middleware(['auth', 'verified.email', 'admin'])->group(function () {
    Route::get('/admin', [
        AdminDashboardController::class,
        'index',
    ])->name('admin.dashboard');

    Route::get('/admin/orders', [
        AdminOrderController::class,
        'index',
    ])->name('admin.orders.index');

    Route::get('/admin/orders/{order}', [
        AdminOrderDetailsController::class,
        'show',
    ])->name('admin.orders.show');

    Route::patch('/admin/orders/{order}/reject', [
        AdminOrderDetailsController::class,
        'reject',
    ])->name('admin.orders.reject');

    Route::post('/admin/orders/{order}/delivery', [
        AdminDeliveryController::class,
        'assign',
    ])->name('admin.orders.delivery.assign');

    Route::get('/admin/delivery', [
        AdminDeliveryPageController::class,
        'index',
    ])->name('admin.delivery.index');

    Route::patch('/admin/orders/{order}/delivery', [
        AdminDeliveryController::class,
        'reassign',
    ])->name('admin.orders.delivery.reassign');

    Route::get('/admin/users', [
        AdminUserController::class,
        'index',
    ])->name('admin.users.index');

    Route::get('/admin/users/{user}', [
        AdminUserController::class,
        'show',
    ])->name('admin.users.show');

    Route::get('/admin/seller-applications', [
        AdminSellerApplicationController::class,
        'index',
    ])->name('admin.seller-applications.index');

    Route::get('/admin/seller-applications/{sellerApplication}', [
        AdminSellerApplicationController::class,
        'show',
    ])->name('admin.seller-applications.show');

    Route::patch('/admin/seller-applications/{sellerApplication}/approve', [
        AdminSellerApplicationController::class,
        'approve',
    ])->name('admin.seller-applications.approve');
    Route::patch('/admin/seller-applications/{sellerApplication}/reject', [
        AdminSellerApplicationController::class,
        'reject',
    ])->name('admin.seller-applications.reject');

    Route::get('/admin/sellers', [
        AdminSellerController::class,
        'index',
    ])->name('admin.sellers.index');
    Route::get('/admin/products', [
        AdminProductController::class,
        'index',
    ])->name('admin.products.index');

    Route::get('/admin/products/{product}', [
        AdminProductController::class,
        'show',
    ])->name('admin.products.show');

    Route::get('/admin/categories', [
        AdminCategoryController::class,
        'index',
    ])->name('admin.categories.index');

    Route::get('/admin/categories/create', [
        AdminCategoryController::class,
        'create',
    ])->name('admin.categories.create');

    Route::post('/admin/categories', [
        AdminCategoryController::class,
        'store',
    ])->name('admin.categories.store');

    Route::get('/admin/categories/{category}', [
        AdminCategoryController::class,
        'show',
    ])->name('admin.categories.show');

    Route::get('/admin/messages', [
        ConversationController::class,
        'adminIndex',
    ])->name('admin.messages');
});

<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products/by-brand', [HomeController::class, 'getProductsByBrand'])->name('products.by-brand');

Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware(['auth:web,admin'])
    ->name('dashboard');

Route::get('/api/products', [HomeController::class, 'getApiProducts'])->name('api.products');
Route::get('/api/brands/search', [HomeController::class, 'searchBrands'])->name('api.brands.search');
Route::get('/api/payment-methods', [HomeController::class, 'getPaymentMethods'])->name('api.payment-methods');
Route::get('/api/orders/check', [HomeController::class, 'checkOrder'])->name('api.orders.check');
Route::get('/games/{brand:name}', [HomeController::class, 'gameDetail'])->name('games.show');
Route::get('/cek-transaksi', [HomeController::class, 'checkTransaction'])->name('check.transaction');
Route::get('/leaderboard', [HomeController::class, 'leaderboard'])->name('leaderboard');
Route::get('/leaderboard/{period}', [HomeController::class, 'leaderboardDetail'])->name('leaderboard.detail');
Route::get('/api/leaderboard', [HomeController::class, 'leaderboardApi'])->name('api.leaderboard');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/sync', [ProductController::class, 'sync'])->name('products.sync');

Route::middleware('auth:web,admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::get('/orders/create/{product}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/payment/detail/{order}', [PaymentController::class, 'detail'])->name('payment.detail');
    Route::get('/payment/success/{order}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/status/{order}', [PaymentController::class, 'status'])->name('payment.status');
});

Route::post('/payment/notification', [PaymentController::class, 'notificationHandler'])->name('payment.notification');

Route::get('/admin', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('admin.auth.login');
})->name('admin.index');

Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'productsCreate'])->name('products.create');
    Route::post('/products', [AdminController::class, 'productsStore'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'productsEdit'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'productsUpdate'])->name('products.update');
    Route::patch('/products/{product}/toggle', [AdminController::class, 'productsToggle'])->name('products.toggle');
    Route::delete('/products/{product}', [AdminController::class, 'productsDestroy'])->name('products.destroy');
    Route::post('/products/sync', [AdminController::class, 'productsSync'])->name('products.sync');

    Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
    Route::get('/brands/create', [AdminController::class, 'brandsCreate'])->name('brands.create');
    Route::post('/brands', [AdminController::class, 'brandsStore'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [AdminController::class, 'brandsEdit'])->name('brands.edit');
    Route::put('/brands/{brand}', [AdminController::class, 'brandsUpdate'])->name('brands.update');
    Route::patch('/brands/{brand}/toggle', [AdminController::class, 'brandsToggle'])->name('brands.toggle');
    Route::delete('/brands/{brand}', [AdminController::class, 'brandsDestroy'])->name('brands.destroy');

    Route::get('/payment-methods', [AdminController::class, 'paymentMethods'])->name('payment-methods');
    Route::get('/payment-methods/create', [AdminController::class, 'paymentMethodsCreate'])->name('payment-methods.create');
    Route::post('/payment-methods', [AdminController::class, 'paymentMethodsStore'])->name('payment-methods.store');
    Route::get('/payment-methods/{paymentMethod}/edit', [AdminController::class, 'paymentMethodsEdit'])->name('payment-methods.edit');
    Route::put('/payment-methods/{paymentMethod}', [AdminController::class, 'paymentMethodsUpdate'])->name('payment-methods.update');
    Route::patch('/payment-methods/{paymentMethod}/toggle', [AdminController::class, 'paymentMethodsToggle'])->name('payment-methods.toggle');
    Route::delete('/payment-methods/{paymentMethod}', [AdminController::class, 'paymentMethodsDestroy'])->name('payment-methods.destroy');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'ordersShow'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'ordersUpdateStatus'])->name('orders.status');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('users.update');

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');

    Route::get('/digiflazz/test', [AdminController::class, 'digiflazzTest'])->name('digiflazz.test');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])
    ->middleware('guest:admin')
    ->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('guest:admin');

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

require __DIR__.'/auth.php';

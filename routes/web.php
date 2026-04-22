<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ServiceManagementController;
use App\Http\Controllers\Admin\ContactManagementController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
Route::get('/layanan/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/portofolio', [PortfolioController::class, 'index'])->name('portfolios.index');
Route::get('/portofolio/{portfolio:slug}', [PortfolioController::class, 'show'])->name('portfolios.show');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');

// Order Routes
Route::get('/pesan/{service:slug}', [OrderController::class, 'create'])->name('order.create');
Route::post('/pesan', [OrderController::class, 'store'])->name('order.store');
Route::get('/pesan/sukses/{order}', [OrderController::class, 'success'])->name('order.success');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister']);

    // Socialite Routes
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->middleware(['auth', AdminMiddleware::class])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::resource('/services', ServiceManagementController::class)->names('services');
    Route::get('/contacts', [ContactManagementController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{contact}', [ContactManagementController::class, 'show'])->name('contacts.show');
    Route::delete('/contacts/{contact}', [ContactManagementController::class, 'destroy'])->name('contacts.destroy');
});

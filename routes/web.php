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
use App\Http\Controllers\Admin\ForemanManagementController;
use App\Http\Controllers\Admin\PortfolioManagementController;
use App\Http\Controllers\Admin\OrderMaterialController;
use App\Http\Controllers\Admin\OrderReferencePhotoController;
use App\Http\Controllers\Foreman\DashboardController as ForemanDashboardController;
use App\Http\Controllers\Foreman\OrderUpdateController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ForemanMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
Route::get('/layanan/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/portofolio', [PortfolioController::class, 'index'])->name('portfolios.index');
Route::get('/portofolio/{portfolio:slug}', [PortfolioController::class, 'show'])->name('portfolios.show');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'store'])->name('contact.store');

// Order Routes — rute spesifik harus di atas rute parameter agar tidak bentrok
Route::middleware('auth')->group(function () {
    Route::get('/pesan/sukses/{order}', [OrderController::class, 'success'])->name('order.success');
    Route::post('/pesan', [OrderController::class, 'store'])->name('order.store');
    Route::get('/pesanan-saya/data', [OrderController::class, 'data'])->name('order.data');
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('order.index');
    Route::get('/pesanan-saya/{order}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/pesanan-saya/{order}', [OrderController::class, 'update'])->name('order.update');
    Route::post('/pesanan-saya/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::post('/pesanan-saya/{order}/setujui-jadwal', [OrderController::class, 'acceptAlternativeSchedule'])->name('order.accept_alternative');
    Route::get('/pesanan-saya/{order}/konsultasi', [OrderController::class, 'consultation'])->name('order.consultation');
    Route::get('/pesanan-saya/{order}/progres', [OrderController::class, 'progress'])->name('order.progress');
    Route::get('/pesan/{service:slug}', [OrderController::class, 'create'])->name('order.create');

    // Profile Settings
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/debug-order', function() {
        $order = \App\Models\Order::find(4);
        $currentUser = auth()->user();
        return response()->json([
            'order_id' => 4,
            'order_exists' => $order ? true : false,
            'foreman_id_in_order' => $order ? $order->foreman_id : null,
            'auth_user_id' => $currentUser ? $currentUser->id : null,
            'auth_user_role' => $currentUser ? $currentUser->role : null,
            'all_users' => \App\Models\User::all(['id', 'name', 'email', 'role']),
        ]);
    });
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
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
    Route::get('/orders/selesai', [OrderManagementController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/riwayat', [OrderManagementController::class, 'history'])->name('orders.history');
    Route::get('/orders/check-new', [OrderManagementController::class, 'checkNewOrders'])->name('orders.checkNew');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/reject', [OrderManagementController::class, 'reject'])->name('orders.reject');
    Route::delete('/orders/{order}', [OrderManagementController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}/consultation', [OrderManagementController::class, 'consultation'])->name('orders.consultation');
    Route::get('/orders/{order}/scheduling', [OrderManagementController::class, 'scheduling'])->name('orders.scheduling');
    Route::post('/orders/{order}/scheduling', [OrderManagementController::class, 'saveScheduling'])->name('orders.saveScheduling');
    
    // Materials
    Route::post('/orders/{order}/materials', [OrderMaterialController::class, 'store'])->name('orders.materials.store');
    Route::delete('/orders/materials/{material}', [OrderMaterialController::class, 'destroy'])->name('orders.materials.destroy');
    // Requirements (kebutuhan proyek — text biasa)
    Route::put('/orders/{order}/requirements', [OrderManagementController::class, 'updateRequirements'])->name('orders.updateRequirements');

    // Reference Photos (foto model/referensi dari pelanggan)
    Route::post('/orders/{order}/photos', [OrderReferencePhotoController::class, 'store'])->name('orders.photos.store');
    Route::delete('/orders/photos/{photo}', [OrderReferencePhotoController::class, 'destroy'])->name('orders.photos.destroy');
    Route::get('/foremen', [ForemanManagementController::class, 'index'])->name('foremen.index');
    Route::post('/foremen', [ForemanManagementController::class, 'store'])->name('foremen.store');
    Route::resource('/services', ServiceManagementController::class)->names('services');
    Route::resource('/portfolios', PortfolioManagementController::class)->except('show')->names('portfolios');
    Route::get('/contacts', [ContactManagementController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{contact}', [ContactManagementController::class, 'show'])->name('contacts.show');
    Route::delete('/contacts/{contact}', [ContactManagementController::class, 'destroy'])->name('contacts.destroy');
});

Route::prefix('mandor')->middleware(['auth', ForemanMiddleware::class])->name('foreman.')->group(function () {
    Route::get('/', [ForemanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/{order}', [ForemanDashboardController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/updates', [OrderUpdateController::class, 'store'])->name('orders.updates.store');
});

// WhatsApp Bot Webhook
Route::post('/webhook/whatsapp', [App\Http\Controllers\Webhook\WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp');

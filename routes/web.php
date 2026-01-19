<?php

use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\InstanceController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UsageController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    $packages = \App\Models\Package::where('is_active', true)->orderBy('sort_order')->get();
    return view('landing', compact('packages'));
})->name('landing');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $packages = \App\Models\Package::where('is_active', true)->orderBy('sort_order')->get();
        return view('dashboard.index', compact('packages'));
    })->name('dashboard');

    // Dashboard pages
    Route::prefix('dashboard')->group(function () {
        Route::get('/instances', [InstanceController::class, 'index'])->name('dashboard.instances');
        Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('dashboard.api-keys');
        Route::get('/messages', [MessageController::class, 'index'])->name('dashboard.messages');
        Route::get('/webhooks', [WebhookController::class, 'index'])->name('dashboard.webhooks');
        Route::get('/usage', [UsageController::class, 'index'])->name('dashboard.usage');
        Route::get('/orders', [\App\Http\Controllers\PaymentController::class, 'orders'])->name('dashboard.orders');
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('dashboard.notifications.index');
        Route::get('/support', [\App\Http\Controllers\SupportController::class, 'index'])->name('dashboard.support.index');
        Route::get('/support/create', [\App\Http\Controllers\SupportController::class, 'create'])->name('dashboard.support.create');
        Route::post('/support', [\App\Http\Controllers\SupportController::class, 'store'])->name('dashboard.support.store');
        Route::get('/support/{ticket}', [\App\Http\Controllers\SupportController::class, 'show'])->name('dashboard.support.show');
        Route::post('/support/{ticket}/message', [\App\Http\Controllers\SupportController::class, 'sendMessage'])->name('dashboard.support.message');
        Route::post('/support/{ticket}/close', [\App\Http\Controllers\SupportController::class, 'close'])->name('dashboard.support.close');
        Route::get('/settings', [SettingsController::class, 'index'])->name('dashboard.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('dashboard.settings.update');
    Route::post('/packages/{package}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribeToPackage'])->name('dashboard.packages.subscribe');

        // Payment routes
        Route::get('/payment/select/{subscription}', [\App\Http\Controllers\PaymentController::class, 'select'])->name('dashboard.payment.select');
        Route::get('/payment/zenopay/card/{subscription}', [\App\Http\Controllers\PaymentController::class, 'showZenoPayCardForm'])->name('dashboard.payment.zenopay.card.form');
        // Status page route (must come before form route to avoid conflicts)
        Route::get('/payment/zenopay/mobile/show/{payment}', [\App\Http\Controllers\PaymentController::class, 'showZenoPayMobile'])->name('dashboard.payment.zenopay.mobile.show');
        // Fallback route to handle old URLs - checks if ID is payment or subscription
        Route::get('/payment/zenopay/mobile/{id}', [\App\Http\Controllers\PaymentController::class, 'handleMobilePaymentRoute'])->name('dashboard.payment.zenopay.mobile.legacy');
        Route::get('/payment/zenopay/mobile/form/{subscription}', [\App\Http\Controllers\PaymentController::class, 'showZenoPayMobileForm'])->name('dashboard.payment.zenopay.mobile.form');
        Route::get('/payment/paypal/{subscription}', [\App\Http\Controllers\PaymentController::class, 'showPayPalForm'])->name('dashboard.payment.paypal.form');
        Route::get('/payment/trc20/{subscription}', [\App\Http\Controllers\PaymentController::class, 'showTrc20Form'])->name('dashboard.payment.trc20.form');
        Route::post('/payment/zenopay/card', [\App\Http\Controllers\PaymentController::class, 'initiateZenoPayCard'])->name('dashboard.payment.zenopay.card');
        Route::post('/payment/zenopay/mobile', [\App\Http\Controllers\PaymentController::class, 'initiateZenoPayMobile'])->name('dashboard.payment.zenopay.mobile');
        Route::post('/payment/paypal', [\App\Http\Controllers\PaymentController::class, 'initiatePayPal'])->name('dashboard.payment.paypal');
        Route::get('/payment/paypal/show/{payment}', [\App\Http\Controllers\PaymentController::class, 'showPayPal'])->name('dashboard.payment.paypal.show');
        Route::post('/payment/trc20', [\App\Http\Controllers\PaymentController::class, 'initiateTrc20'])->name('dashboard.payment.trc20');
        Route::get('/payment/trc20/show/{payment}', [\App\Http\Controllers\PaymentController::class, 'showTrc20'])->name('dashboard.payment.trc20.show');
        Route::get('/payment/zenopay/callback/{subscription}', [\App\Http\Controllers\PaymentController::class, 'zenopayCallback'])->name('dashboard.payment.zenopay.callback');
        Route::get('/api/payments/{payment}/status', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('dashboard.payment.status');
    });

    // API routes for web forms (using same controllers but for web)
    Route::post('/api/instances', [InstanceController::class, 'store'])->name('api.instances.store');
    Route::get('/api/instances/{instance}', [InstanceController::class, 'show'])->name('api.instances.show');
    Route::delete('/api/instances/{instance}', [InstanceController::class, 'destroy'])->name('api.instances.destroy');
    Route::post('/api/instances/{instance}/connect', [InstanceController::class, 'connect'])->name('api.instances.connect');
    Route::post('/api/instances/{instance}/stop', [InstanceController::class, 'stop'])->name('api.instances.stop');
    Route::post('/api/instances/{instance}/start', [InstanceController::class, 'start'])->name('api.instances.start');
    Route::post('/api/instances/{instance}/restart', [InstanceController::class, 'restart'])->name('api.instances.restart');
    Route::get('/api/instances/{instance}/qr', [InstanceController::class, 'qr'])->name('api.instances.qr');
    
    Route::post('/api/api-keys', [ApiKeyController::class, 'store'])->name('api.api-keys.store');
    Route::delete('/api/api-keys/{api_key}', [ApiKeyController::class, 'destroy'])->name('api.api-keys.destroy');
    
    Route::post('/api/v1/messages/send', [MessageController::class, 'send'])->name('api.messages.send');
    Route::get('/api/messages', [MessageController::class, 'index'])->name('api.messages.index');
    Route::get('/api/messages/{message}', [MessageController::class, 'show'])->name('api.messages.show');
    
    Route::post('/api/webhooks', [WebhookController::class, 'store'])->name('api.webhooks.store');
    Route::put('/api/webhooks/{webhook}', [WebhookController::class, 'update'])->name('api.webhooks.update');
    Route::delete('/api/webhooks/{webhook}', [WebhookController::class, 'destroy'])->name('api.webhooks.destroy');
    
        Route::post('/api/subscriptions/{subscription}/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('api.subscriptions.cancel');
        
        // Payment status check for polling (web route with session auth)
        Route::get('/api/payments/{payment}/status', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('dashboard.payment.status');
        
        // Notification routes
        Route::get('/api/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('api.notifications.unread');
        Route::post('/api/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
        Route::post('/api/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
        Route::delete('/api/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('api.notifications.destroy');
});

// Admin routes (protected with admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/analytics', [\App\Http\Controllers\Admin\DashboardController::class, 'analytics'])->name('admin.analytics');
});

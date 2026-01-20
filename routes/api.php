<?php

use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\InstanceController;
use App\Http\Controllers\Api\InternalController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UsageController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Public routes
// GET routes redirect to web login/register pages for better UX
Route::get('/login', function () {
    return redirect()->route('login');
})->name('api.login.redirect');

Route::get('/register', function () {
    return redirect()->route('register');
})->name('api.register.redirect');

// POST routes for API authentication
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Authenticated web routes (session-based)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('user');
    
    // API key management (web authenticated)
    Route::apiResource('api-keys', ApiKeyController::class);
    Route::post('api-keys/{api_key}/rotate', [ApiKeyController::class, 'rotate'])->name('api-keys.rotate');
    
    // Subscription management
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/active', [SubscriptionController::class, 'active'])->name('subscriptions.active');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    
    // Payment management
    Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/status', [PaymentController::class, 'checkStatus'])->name('api.payments.status');
    
    // Payment webhooks (public, no auth required)
    Route::post('/payments/webhook/{provider}', [PaymentController::class, 'webhook'])->name('payments.webhook');
});

// API v1 routes (API key authenticated)
Route::prefix('v1')->group(function () {
    // Auth routes for API
    Route::post('/login', [AuthController::class, 'login'])->name('api.v1.login');
    
    // Protected API routes
    Route::middleware([\App\Http\Middleware\AuthenticateApiKey::class])->group(function () {
        // API key management
        Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api.v1.api-keys.index');
        Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api.v1.api-keys.store');
        Route::post('/api-keys/{api_key}/rotate', [ApiKeyController::class, 'rotate'])->name('api.v1.api-keys.rotate');
        Route::delete('/api-keys/{api_key}', [ApiKeyController::class, 'destroy'])->name('api.v1.api-keys.destroy');
        
        // Instance management
        Route::apiResource('instances', InstanceController::class);
        Route::get('/instances/{instance}/qr', [InstanceController::class, 'qr'])->name('api.v1.instances.qr');
        Route::post('/instances/{instance}/connect', [InstanceController::class, 'connect'])->name('api.v1.instances.connect');
        
        // Message management
        Route::post('/messages/send', [MessageController::class, 'send'])->name('api.v1.messages.send');
        Route::get('/messages', [MessageController::class, 'index'])->name('api.v1.messages.index');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('api.v1.messages.show');
        
        // Webhook management
        Route::apiResource('webhooks', WebhookController::class);
        
        // Usage tracking
        Route::get('/usage', [UsageController::class, 'index'])->name('api.v1.usage.index');
        Route::get('/usage/logs', [UsageController::class, 'logs'])->name('api.v1.usage.logs');
        
        // Subscription management (API key authenticated)
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('api.v1.subscriptions.index');
        Route::get('/subscriptions/active', [SubscriptionController::class, 'active'])->name('api.v1.subscriptions.active');
        Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('api.v1.subscriptions.show');
    });
});

// Internal API routes (for Node.js service)
Route::prefix('internal')->middleware([\App\Http\Middleware\AuthenticateInternalApi::class])->group(function () {
    // Instance management
    Route::get('/instances', [InternalController::class, 'getAllInstances'])->name('api.internal.instances.index');
    Route::get('/instances/pending', [InternalController::class, 'getPendingConnections'])->name('api.internal.instances.pending');
    Route::get('/instances/{instance}/connect', [InternalController::class, 'getInstanceForConnection'])->name('api.internal.instances.connect');
    Route::post('/instances/{instance}/qr', [InternalController::class, 'storeQrCode'])->name('api.internal.instances.qr');
    Route::delete('/instances/{instance}/qr', [InternalController::class, 'clearQrCode'])->name('api.internal.instances.qr.clear');
    Route::post('/instances/{instance}/status', [InternalController::class, 'updateStatus'])->name('api.internal.instances.status');
    Route::post('/instances/{instance}/session', [InternalController::class, 'storeSession'])->name('api.internal.instances.session');
    
    // Message management
    Route::get('/messages/pending', [InternalController::class, 'getPendingMessages'])->name('api.internal.messages.pending');
    Route::post('/messages', [InternalController::class, 'storeMessage'])->name('api.internal.messages.store');
    Route::post('/messages/{message}/status', [InternalController::class, 'updateMessageStatus'])->name('api.internal.messages.status');
    
    // Bot Rules for Node.js Service
    Route::get('/instances/{instance}/bot-replies', [InternalController::class, 'getBotReplies'])->name('api.internal.instances.bot-replies');
});

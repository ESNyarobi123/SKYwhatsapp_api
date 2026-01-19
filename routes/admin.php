<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ApiKeyController as AdminApiKeyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstanceController as AdminInstanceController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin routes (require admin authentication)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('admin.analytics');

    // Users Management
    Route::resource('users', UserController::class)->names('admin.users');
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('admin.users.unsuspend');
    Route::post('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('admin.users.impersonate');
    Route::post('/users/logout-impersonate', [UserController::class, 'logoutImpersonate'])->name('admin.users.logout-impersonate');

    // Packages Management
    Route::resource('packages', PackageController::class)->names('admin.packages');

    // Subscriptions Management
    Route::resource('subscriptions', AdminSubscriptionController::class)->names('admin.subscriptions');
    Route::post('/subscriptions/{subscription}/renew', [AdminSubscriptionController::class, 'renew'])->name('admin.subscriptions.renew');

    // Notifications Management
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('admin.notifications.store');

    // Support Tickets Management
    Route::get('/support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support.index');
    Route::get('/support/{ticket}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('admin.support.show');
    Route::post('/support/{ticket}/assign', [\App\Http\Controllers\Admin\SupportController::class, 'assign'])->name('admin.support.assign');
    Route::post('/support/{ticket}/status', [\App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('admin.support.status');
    Route::post('/support/{ticket}/message', [\App\Http\Controllers\Admin\SupportController::class, 'sendMessage'])->name('admin.support.message');
    Route::post('/support/{ticket}/close', [\App\Http\Controllers\Admin\SupportController::class, 'close'])->name('admin.support.close');

    // API Keys Management
    Route::get('/api-keys', [AdminApiKeyController::class, 'index'])->name('admin.api-keys.index');
    Route::get('/api-keys/{api_key}', [AdminApiKeyController::class, 'show'])->name('admin.api-keys.show');
    Route::post('/api-keys/{api_key}/revoke', [AdminApiKeyController::class, 'revoke'])->name('admin.api-keys.revoke');
    Route::post('/api-keys/{api_key}/reactivate', [AdminApiKeyController::class, 'reactivate'])->name('admin.api-keys.reactivate');

    // Instances Management
    Route::get('/instances', [AdminInstanceController::class, 'index'])->name('admin.instances.index');
    Route::get('/instances/{instance}', [AdminInstanceController::class, 'show'])->name('admin.instances.show');
    Route::post('/instances/{instance}/reset', [AdminInstanceController::class, 'reset'])->name('admin.instances.reset');
    Route::delete('/instances/{instance}', [AdminInstanceController::class, 'destroy'])->name('admin.instances.destroy');

    // Revenue Management
    Route::get('/revenue', [RevenueController::class, 'index'])->name('admin.revenue.index');
    Route::get('/revenue/{payment}', [RevenueController::class, 'show'])->name('admin.revenue.show');
    Route::get('/revenue/export', [RevenueController::class, 'export'])->name('admin.revenue.export');
    Route::post('/revenue/{payment}/verify', [RevenueController::class, 'verify'])->name('admin.revenue.verify');

    // Activity Logs
    Route::get('/activity', [ActivityController::class, 'index'])->name('admin.activity.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings/payment', [SettingsController::class, 'updatePaymentSettings'])->name('admin.settings.payment.update');
    Route::post('/settings/payment-method/toggle', [SettingsController::class, 'togglePaymentMethod'])->name('admin.settings.payment-method.toggle');
    Route::post('/settings/contact', [SettingsController::class, 'updateContactSettings'])->name('admin.settings.contact.update');
    Route::post('/settings/cache/clear', [SettingsController::class, 'clearCache'])->name('admin.settings.cache.clear');
    Route::post('/settings/system/refresh', [SettingsController::class, 'refreshSystem'])->name('admin.settings.system.refresh');
    Route::post('/settings/trc20/screenshot', [SettingsController::class, 'uploadTrc20Screenshot'])->name('admin.settings.trc20.screenshot');
});

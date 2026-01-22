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

// Password Reset Routes (Code-based)
use App\Http\Controllers\Auth\PasswordResetController;
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode'])->name('password.email');
Route::get('/reset-password/verify', [PasswordResetController::class, 'showCodeForm'])->name('password.code.form');
Route::post('/reset-password/verify', [PasswordResetController::class, 'verifyCode'])->name('password.code.verify');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Public invitation route
Route::get('/team/invitation/{invitation}', [\App\Http\Controllers\TeamController::class, 'showInvitation'])->name('team.invitation.show');

use App\Http\Controllers\Auth\VerificationController;

// ... (existing routes)

// Email Verification Routes (Code-based)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verify', [VerificationController::class, 'verifyCode'])->middleware(['throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware(['throttle:6,1'])->name('verification.send');
});

// Dashboard routes (protected & verified)
Route::middleware(['auth', 'verified'])->group(function () {
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
        Route::get('/packages', function () {
            $packages = \App\Models\Package::where('is_active', true)->orderBy('sort_order')->get();
            return view('dashboard.packages.index', compact('packages'));
        })->name('dashboard.packages');
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('dashboard.notifications.index');
        
        // Bot Builder Routes
        Route::get('/bot', [\App\Http\Controllers\BotReplyController::class, 'index'])->name('dashboard.bot.index');
        Route::post('/bot', [\App\Http\Controllers\BotReplyController::class, 'store'])->name('dashboard.bot.store');
        Route::put('/bot/{botReply}', [\App\Http\Controllers\BotReplyController::class, 'update'])->name('dashboard.bot.update');
        Route::delete('/bot/{botReply}', [\App\Http\Controllers\BotReplyController::class, 'destroy'])->name('dashboard.bot.destroy');

        // Analytics Routes
        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('dashboard.analytics');
        Route::get('/analytics/export', [\App\Http\Controllers\AnalyticsController::class, 'exportCsv'])->name('dashboard.analytics.export');

        // Message Templates Routes
        Route::get('/templates', [\App\Http\Controllers\MessageTemplateController::class, 'index'])->name('dashboard.templates.index');
        Route::post('/templates', [\App\Http\Controllers\MessageTemplateController::class, 'store'])->name('dashboard.templates.store');
        Route::put('/templates/{template}', [\App\Http\Controllers\MessageTemplateController::class, 'update'])->name('dashboard.templates.update');
        Route::delete('/templates/{template}', [\App\Http\Controllers\MessageTemplateController::class, 'destroy'])->name('dashboard.templates.destroy');
        Route::post('/templates/{template}/use', [\App\Http\Controllers\MessageTemplateController::class, 'use'])->name('dashboard.templates.use');
        Route::post('/templates/{template}/duplicate', [\App\Http\Controllers\MessageTemplateController::class, 'duplicate'])->name('dashboard.templates.duplicate');

        // Message Scheduler Routes
        Route::get('/scheduler', [\App\Http\Controllers\ScheduledMessageController::class, 'index'])->name('dashboard.scheduler.index');
        Route::post('/scheduler', [\App\Http\Controllers\ScheduledMessageController::class, 'store'])->name('dashboard.scheduler.store');
        Route::put('/scheduler/{scheduledMessage}', [\App\Http\Controllers\ScheduledMessageController::class, 'update'])->name('dashboard.scheduler.update');
        Route::post('/scheduler/{scheduledMessage}/cancel', [\App\Http\Controllers\ScheduledMessageController::class, 'cancel'])->name('dashboard.scheduler.cancel');
        Route::post('/scheduler/{scheduledMessage}/retry', [\App\Http\Controllers\ScheduledMessageController::class, 'retry'])->name('dashboard.scheduler.retry');
        Route::delete('/scheduler/{scheduledMessage}', [\App\Http\Controllers\ScheduledMessageController::class, 'destroy'])->name('dashboard.scheduler.destroy');

        // Bot Templates Routes
        Route::get('/bot-templates', [\App\Http\Controllers\BotTemplateController::class, 'index'])->name('dashboard.bot-templates.index');
        Route::get('/bot-templates/{botTemplate}', [\App\Http\Controllers\BotTemplateController::class, 'show'])->name('dashboard.bot-templates.show');
        Route::post('/bot-templates/{botTemplate}/import', [\App\Http\Controllers\BotTemplateController::class, 'import'])->name('dashboard.bot-templates.import');
        Route::get('/bot-templates/{botTemplate}/preview', [\App\Http\Controllers\BotTemplateController::class, 'preview'])->name('dashboard.bot-templates.preview');

        // Webhook Logs Routes
        Route::get('/webhook-logs', [\App\Http\Controllers\WebhookLogController::class, 'index'])->name('dashboard.webhook-logs.index');
        Route::get('/webhook-logs/{webhookLog}', [\App\Http\Controllers\WebhookLogController::class, 'show'])->name('dashboard.webhook-logs.show');
        Route::get('/webhook-logs/{webhookLog}/details', [\App\Http\Controllers\WebhookLogController::class, 'details'])->name('dashboard.webhook-logs.details');
        Route::post('/webhook-logs/{webhookLog}/retry', [\App\Http\Controllers\WebhookLogController::class, 'retry'])->name('dashboard.webhook-logs.retry');
        Route::post('/webhook-logs/clear', [\App\Http\Controllers\WebhookLogController::class, 'clear'])->name('dashboard.webhook-logs.clear');

        // Team Management Routes
        Route::get('/team', [\App\Http\Controllers\TeamController::class, 'index'])->name('dashboard.team.index');
        Route::post('/team', [\App\Http\Controllers\TeamController::class, 'store'])->name('dashboard.team.store');
        Route::put('/team/{team}', [\App\Http\Controllers\TeamController::class, 'update'])->name('dashboard.team.update');
        Route::delete('/team/{team}', [\App\Http\Controllers\TeamController::class, 'destroy'])->name('dashboard.team.destroy');
        Route::post('/team/{team}/invite', [\App\Http\Controllers\TeamController::class, 'invite'])->name('dashboard.team.invite');

        Route::post('/team/invitation/{invitation}/accept', [\App\Http\Controllers\TeamController::class, 'acceptInvitation'])->name('dashboard.team.invitation.accept');
        Route::post('/team/invitation/{invitation}/decline', [\App\Http\Controllers\TeamController::class, 'declineInvitation'])->name('dashboard.team.invitation.decline');
        Route::delete('/team/{team}/invitation/{invitation}', [\App\Http\Controllers\TeamController::class, 'cancelInvitation'])->name('dashboard.team.invitation.cancel');
        Route::put('/team/{team}/member/{member}', [\App\Http\Controllers\TeamController::class, 'updateMember'])->name('dashboard.team.member.update');
        Route::delete('/team/{team}/member/{member}', [\App\Http\Controllers\TeamController::class, 'removeMember'])->name('dashboard.team.member.remove');
        Route::delete('/team/{team}/leave', [\App\Http\Controllers\TeamController::class, 'leave'])->name('dashboard.team.leave');

        Route::get('/support', [\App\Http\Controllers\SupportController::class, 'index'])->name('dashboard.support.index');
        Route::get('/support/create', [\App\Http\Controllers\SupportController::class, 'create'])->name('dashboard.support.create');
        Route::post('/support', [\App\Http\Controllers\SupportController::class, 'store'])->name('dashboard.support.store');
        Route::get('/support/{ticket}', [\App\Http\Controllers\SupportController::class, 'show'])->name('dashboard.support.show');
        Route::post('/support/{ticket}/message', [\App\Http\Controllers\SupportController::class, 'sendMessage'])->name('dashboard.support.message');
        Route::post('/support/{ticket}/close', [\App\Http\Controllers\SupportController::class, 'close'])->name('dashboard.support.close');
        Route::get('/settings', [SettingsController::class, 'index'])->name('dashboard.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('dashboard.settings.update');
    Route::post('/packages/{package}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribeToPackage'])->name('dashboard.packages.subscribe');
    Route::get('/documentation', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('dashboard.documentation');

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
    
    // Admin Settings Routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings/payment', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePaymentSettings'])->name('admin.settings.payment.update');
    Route::post('/settings/payment-method/toggle', [\App\Http\Controllers\Admin\SettingsController::class, 'togglePaymentMethod'])->name('admin.settings.payment-method.toggle');
    Route::post('/settings/contact', [\App\Http\Controllers\Admin\SettingsController::class, 'updateContactSettings'])->name('admin.settings.contact.update');
    Route::post('/settings/cache', [\App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('admin.settings.cache.clear');
    Route::post('/settings/system/refresh', [\App\Http\Controllers\Admin\SettingsController::class, 'refreshSystem'])->name('admin.settings.system.refresh');
    Route::post('/settings/trc20/screenshot', [\App\Http\Controllers\Admin\SettingsController::class, 'uploadTrc20Screenshot'])->name('admin.settings.trc20.screenshot');
    Route::post('/settings/documentation/upload', [\App\Http\Controllers\Admin\SettingsController::class, 'uploadApiDocumentation'])->name('admin.settings.documentation.upload');
});

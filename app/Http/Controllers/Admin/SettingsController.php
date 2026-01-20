<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactSettingsRequest;
use App\Http\Requests\Admin\UpdatePaymentSettingsRequest;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display settings page with tabs.
     */
    public function index(Request $request)
    {
        $paymentSettings = Setting::getGroup('payment');
        $contactSettings = Setting::getGroup('contact');
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();

        $zenopayApiKey = Setting::getValue('zenopay_api_key');
        $zenopayTestMode = Setting::getValue('zenopay_test_mode', false);
        $paypalEmail = Setting::getValue('paypal_email');
        $paypalMeUsername = Setting::getValue('paypal_me_username');
        $trc20WalletAddress = Setting::getValue('trc20_wallet_address');
        $trc20Screenshot = Setting::getValue('trc20_screenshot');
        $whatsappNumber = Setting::getValue('whatsapp_number');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'payment_settings' => $paymentSettings,
                    'contact_settings' => $contactSettings,
                    'payment_methods' => $paymentMethods,
                ],
            ]);
        }

        return view('admin.settings.index', compact(
            'zenopayApiKey',
            'zenopayTestMode',
            'paypalEmail',
            'paypalMeUsername',
            'trc20WalletAddress',
            'trc20Screenshot',
            'whatsappNumber',
            'paymentMethods'
        ));
    }

    /**
     * Update payment settings.
     */
    public function updatePaymentSettings(UpdatePaymentSettingsRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        // Only update API key if a new non-empty value is provided
        // This prevents overwriting with empty string when password field is left unchanged
        if (isset($validated['zenopay_api_key']) && ! empty(trim($validated['zenopay_api_key']))) {
            Setting::setValue('zenopay_api_key', trim($validated['zenopay_api_key']), 'string', 'payment');
        }

        // Update test mode setting
        // Always update because unchecked checkboxes don't send the field in the request
        Setting::setValue('zenopay_test_mode', $request->boolean('zenopay_test_mode') ? '1' : '0', 'string', 'payment');

        if (isset($validated['paypal_email'])) {
            Setting::setValue('paypal_email', $validated['paypal_email'], 'string', 'payment');
        }

        if (isset($validated['paypal_me_username'])) {
            Setting::setValue('paypal_me_username', $validated['paypal_me_username'], 'string', 'payment');
        }

        if (isset($validated['trc20_wallet_address'])) {
            Setting::setValue('trc20_wallet_address', $validated['trc20_wallet_address'], 'string', 'payment');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment settings updated successfully.',
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Toggle payment method enable/disable.
     */
    public function togglePaymentMethod(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'method' => ['required', 'string', 'in:zenopay_card,zenopay_mobile,paypal,trc20'],
            'is_enabled' => ['required', 'boolean'],
        ]);

        $paymentMethod = PaymentMethod::where('method', $request->method)->first();

        if (! $paymentMethod) {
            $names = [
                'zenopay_card' => 'ZenoPay Card Payments',
                'zenopay_mobile' => 'ZenoPay Mobile Money',
                'paypal' => 'PayPal',
                'trc20' => 'TRC20 Crypto',
            ];

            $paymentMethod = PaymentMethod::create([
                'method' => $request->method,
                'name' => $names[$request->method] ?? ucfirst($request->method),
                'is_enabled' => $request->boolean('is_enabled'),
                'sort_order' => PaymentMethod::count(),
            ]);
        } else {
            $paymentMethod->update(['is_enabled' => $request->boolean('is_enabled')]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully.',
                'data' => ['payment_method' => $paymentMethod],
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'Payment method updated successfully.');
    }

    /**
     * Update WhatsApp contact number.
     */
    public function updateContactSettings(UpdateContactSettingsRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        if (isset($validated['whatsapp_number'])) {
            Setting::setValue('whatsapp_number', $validated['whatsapp_number'], 'string', 'contact');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact settings updated successfully.',
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'Contact settings updated successfully.');
    }

    /**
     * Clear application cache.
     */
    public function clearCache(Request $request): JsonResponse|RedirectResponse
    {
        $cacheType = $request->get('type', 'all');

        match ($cacheType) {
            'config' => Artisan::call('config:clear'),
            'route' => Artisan::call('route:clear'),
            'view' => Artisan::call('view:clear'),
            'cache' => Artisan::call('cache:clear'),
            'all' => $this->clearAllCaches(),
        };

        $message = match ($cacheType) {
            'config' => 'Config cache cleared successfully.',
            'route' => 'Route cache cleared successfully.',
            'view' => 'View cache cleared successfully.',
            'cache' => 'Application cache cleared successfully.',
            'all' => 'All caches cleared successfully.',
        };

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.settings')->with('success', $message);
    }

    /**
     * Refresh system (clear all caches and optimize).
     */
    public function refreshSystem(Request $request): JsonResponse|RedirectResponse
    {
        $this->clearAllCaches();

        // Optimize application
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'System refreshed successfully.',
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'System refreshed successfully.');
    }

    /**
     * Upload TRC20 payment screenshot.
     */
    public function uploadTrc20Screenshot(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'screenshot' => ['required', 'image', 'mimes:jpeg,jpg,png,gif', 'max:5120'],
        ]);

        if ($request->hasFile('screenshot')) {
            // Delete old screenshot if exists
            $oldScreenshot = Setting::getValue('trc20_screenshot');
            if ($oldScreenshot && Storage::disk('public')->exists($oldScreenshot)) {
                Storage::disk('public')->delete($oldScreenshot);
            }

            // Store new screenshot
            $path = $request->file('screenshot')->store('trc20', 'public');

            Setting::setValue('trc20_screenshot', $path, 'file', 'payment');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'TRC20 screenshot uploaded successfully.',
                    'data' => ['path' => $path],
                ]);
            }

            return redirect()->route('admin.settings')->with('success', 'TRC20 screenshot uploaded successfully.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No screenshot file provided.',
            ], 400);
        }

        return redirect()->route('admin.settings')->with('error', 'No screenshot file provided.');
    }

    /**
     * Upload API Documentation PDF.
     */
    public function uploadApiDocumentation(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf', 'max:10240'], // Max 10MB
        ]);

        if ($request->hasFile('document')) {
            // Delete old document if exists
            $oldDoc = Setting::getValue('api_documentation_pdf');
            if ($oldDoc && Storage::disk('public')->exists($oldDoc)) {
                Storage::disk('public')->delete($oldDoc);
            }

            // Store new document
            $path = $request->file('document')->store('documentation', 'public');

            Setting::setValue('api_documentation_pdf', $path, 'file', 'system');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documentation uploaded successfully.',
                    'data' => ['path' => $path],
                ]);
            }

            return redirect()->route('admin.settings')->with('success', 'Documentation uploaded successfully.');
        }

        return redirect()->route('admin.settings')->with('error', 'No document file provided.');
    }

    /**
     * Clear all caches.
     */
    private function clearAllCaches(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }
}

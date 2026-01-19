<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * List all payments/revenue with filters.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'subscription']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $payments = $query->latest()->paginate($request->get('per_page', 15));

        // Calculate summary stats
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_payments' => Payment::where('status', 'completed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'revenue_by_provider' => Payment::where('status', 'completed')
                ->select('provider', DB::raw('SUM(amount) as total'))
                ->groupBy('provider')
                ->pluck('total', 'provider')
                ->toArray(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'payments' => $payments->items(),
                    'stats' => $stats,
                    'pagination' => [
                        'current_page' => $payments->currentPage(),
                        'last_page' => $payments->lastPage(),
                        'per_page' => $payments->perPage(),
                        'total' => $payments->total(),
                    ],
                ],
            ]);
        }

        return view('admin.revenue.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details.
     */
    public function show(Request $request, Payment $payment)
    {
        $payment->load(['user', 'subscription']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => ['payment' => $payment],
            ]);
        }

        return view('admin.revenue.show', compact('payment'));
    }

    /**
     * Export revenue data (CSV format).
     */
    public function export(Request $request)
    {
        $query = Payment::with(['user', 'subscription']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->get();

        $filename = 'revenue_export_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Email',
                'Subscription',
                'Amount',
                'Currency',
                'Provider',
                'Reference',
                'Status',
                'Date',
            ]);

            // CSV rows
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->user->name ?? 'N/A',
                    $payment->user->email ?? 'N/A',
                    $payment->subscription->plan_name ?? 'N/A',
                    $payment->amount,
                    $payment->currency,
                    $payment->provider,
                    $payment->reference,
                    $payment->status,
                    $payment->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Verify a payment manually (for PayPal, TRC20).
     */
    public function verify(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending' || $payment->verification_status !== 'pending') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment is not pending verification.',
                ], 400);
            }

            return redirect()->route('admin.revenue.index')->with('error', 'Payment is not pending verification.');
        }

        // Update payment status
        $payment->update([
            'status' => 'completed',
            'verification_status' => 'verified',
        ]);

        // Activate subscription
        if ($payment->subscription) {
            $subscription = $payment->subscription;
            $durationDays = $subscription->package->duration_days ?? 30;
            
            $subscription->update([
                'status' => 'active',
                'expires_at' => now()->addDays($durationDays),
                'payment_provider' => $payment->provider,
                'payment_reference' => $payment->reference,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment verified and subscription activated successfully.',
            ]);
        }

        return redirect()->route('admin.revenue.index', ['status' => 'pending'])
            ->with('success', 'Payment verified and subscription activated successfully.');
    }
}

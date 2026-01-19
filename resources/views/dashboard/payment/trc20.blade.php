@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">TRC20 Crypto Payment</h1>
        <p class="text-white/70">Send cryptocurrency payment to activate your subscription</p>
    </div>

    <x-card>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Payment Details</h3>
            <div class="space-y-3 text-white/70">
                <div class="flex justify-between">
                    <span>Payment ID:</span>
                    <span class="text-white">#{{ $payment->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Amount:</span>
                    <span class="text-white font-semibold">{{ number_format($instructions['amount'], 2) }} {{ $instructions['currency'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Network:</span>
                    <span class="text-white">{{ $instructions['network'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Status:</span>
                    <x-badge variant="{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'error') }}">
                        {{ ucfirst($payment->status) }}
                    </x-badge>
                </div>
                <div class="flex justify-between">
                    <span>Subscription:</span>
                    <span class="text-white">{{ $payment->subscription->plan_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        @if($payment->status === 'pending')
            <div class="border-t border-white/5 pt-6">
                <div class="mb-6">
                    <h4 class="text-white font-semibold mb-3">Payment Instructions</h4>
                    <div class="bg-[#1A1A1A] border border-white/10 rounded-lg p-6 mb-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-white/70 mb-2">Wallet Address (TRC20)</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" value="{{ $instructions['wallet_address'] }}" readonly id="wallet_address" class="flex-1 px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white font-mono text-sm">
                                <button onclick="copyWalletAddress()" class="px-4 py-3 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-white/70 mb-2">Amount to Send</label>
                            <div class="px-4 py-3 bg-[#252525] border border-white/10 rounded-lg">
                                <p class="text-white font-semibold text-lg">{{ number_format($instructions['amount'], 2) }} {{ $instructions['currency'] }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-white/70 mb-2">Payment Reference</label>
                            <div class="px-4 py-3 bg-[#252525] border border-white/10 rounded-lg">
                                <p class="text-white font-mono text-sm">{{ $instructions['payment_reference'] }}</p>
                            </div>
                            <p class="text-white/50 text-xs mt-1">Include this in the memo/note when sending (optional)</p>
                        </div>

                        <div class="border-t border-white/5 pt-4">
                            <h5 class="text-white font-medium mb-2">Instructions:</h5>
                            <ul class="list-disc list-inside space-y-1 text-white/70 text-sm">
                                @foreach($instructions['instructions'] as $instruction)
                                    <li>{{ $instruction }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-white font-semibold mb-2">Manual Verification Required</h4>
                            <p class="text-white/70 text-sm">After sending the payment, our admin will verify the transaction and activate your subscription. This usually takes 24-48 hours.</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div id="polling-status" class="mb-4">
                        <div class="inline-flex items-center space-x-2 text-white/70 text-sm">
                            <svg class="animate-spin h-4 w-4 text-[#FCD535]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Checking payment status...</span>
                        </div>
                    </div>
                    <p class="text-white/70 text-sm mb-4">Payment status will be updated automatically once verified by admin.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-white/70 hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @elseif($payment->status === 'completed')
            <div class="border-t border-white/5 pt-6">
                <div class="bg-[#00D9A5]/10 border border-[#00D9A5]/30 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-[#00D9A5] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="text-white font-semibold mb-2">Payment Verified!</h4>
                    <p class="text-white/70 text-sm mb-4">Your subscription has been activated successfully.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </x-card>
</div>

@push('scripts')
@if($payment->status === 'pending')
<script>
(function() {
    const paymentId = {{ $payment->id }};
    let pollInterval = null;
    let pollCount = 0;
    const maxPolls = 120; // Poll for up to 10 minutes (120 * 5 seconds)
    const pollIntervalMs = 5000; // Poll every 5 seconds

    function checkPaymentStatus() {
        pollCount++;
        
        if (pollCount > maxPolls) {
            stopPolling();
            updateStatusMessage('Polling stopped. Please refresh the page to check status manually.');
            return;
        }

        fetch(`/api/payments/${paymentId}/status`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const status = data.data.payment.status;
                
                if (status === 'completed') {
                    stopPolling();
                    // Reload page to show success message
                    window.location.reload();
                } else if (status === 'failed' || status === 'cancelled') {
                    stopPolling();
                    // Reload page to show updated status
                    window.location.reload();
                }
                // If still pending, continue polling
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            // Continue polling even on error
        });
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    function updateStatusMessage(message) {
        const statusEl = document.getElementById('polling-status');
        if (statusEl) {
            statusEl.innerHTML = `<p class="text-white/70 text-sm">${message}</p>`;
        }
    }

    // Start polling
    pollInterval = setInterval(checkPaymentStatus, pollIntervalMs);
    
    // Initial check after 2 seconds
    setTimeout(checkPaymentStatus, 2000);

    // Stop polling when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            // Restart polling when page becomes visible again
            if (!pollInterval && pollCount < maxPolls) {
                pollInterval = setInterval(checkPaymentStatus, pollIntervalMs);
            }
        }
    });
})();
</script>
@endif

<script>
function copyWalletAddress() {
    const walletAddress = document.getElementById('wallet_address');
    walletAddress.select();
    walletAddress.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        alert('Wallet address copied to clipboard!');
    } catch (err) {
        // Fallback for modern browsers
        navigator.clipboard.writeText(walletAddress.value).then(function() {
            alert('Wallet address copied to clipboard!');
        });
    }
}
</script>
@endpush
@endsection

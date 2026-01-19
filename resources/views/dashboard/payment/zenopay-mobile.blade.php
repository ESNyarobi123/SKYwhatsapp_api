@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Mobile Money Payment</h1>
        <p class="text-white/70">Payment status for your subscription</p>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

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
                    <span class="text-white font-semibold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
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
                <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-6 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-[#FCD535] flex-shrink-0 mt-0.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-white font-semibold mb-2">Payment in Progress</h4>
                            <p class="text-white/70 text-sm">Please check your phone for the mobile money payment prompt. Complete the payment on your phone to activate your subscription.</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div id="polling-status" class="mb-4">
                        <div class="inline-flex items-center space-x-3 text-white/90">
                            <svg class="animate-spin h-6 w-6 text-[#FCD535]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-medium">Checking payment status...</span>
                        </div>
                    </div>
                    <p class="text-white/70 text-sm mb-4">Payment status will be updated automatically once confirmed.</p>
                    <div id="success-message" class="hidden mb-4">
                        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4">
                            <p class="text-[#00D9A5] text-sm font-medium">Payment successful! Redirecting to packages...</p>
                        </div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-[#FCD535] hover:text-[#F0C420] transition-colors">
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
                    <h4 class="text-white font-semibold mb-2">Payment Completed!</h4>
                    <p class="text-white/70 text-sm mb-4">Your subscription has been activated successfully.</p>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-[#FCD535] text-[#1A1A1A] font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                            Go to Dashboard
                        </a>
                        <a href="{{ route('dashboard.orders') }}" class="inline-flex items-center px-4 py-2 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">
                            View Order History
                        </a>
                    </div>
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
    let isRedirecting = false;

    function checkPaymentStatus() {
        pollCount++;
        
        if (pollCount > maxPolls) {
            stopPolling();
            updateStatusMessage('Polling stopped. Please refresh the page to check status manually.');
            return;
        }

        // Build the status check URL
        const statusUrl = '{{ route("dashboard.payment.status", ["payment" => ":id"]) }}'.replace(':id', paymentId);
        
        fetch(statusUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin',
            cache: 'no-cache'
        })
        .then(async response => {
            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP error response:', response.status, errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const status = data.data.payment.status;
                
                if (status === 'completed') {
                    stopPolling();
                    showSuccessMessage();
                    // Redirect to dashboard (package page) after showing success message
                    setTimeout(() => {
                        if (!isRedirecting) {
                            isRedirecting = true;
                            window.location.href = '{{ route("dashboard") }}';
                        }
                    }, 2000);
                } else if (status === 'failed' || status === 'cancelled') {
                    stopPolling();
                    // Reload page to show updated status
                    window.location.reload();
                }
                // If still pending, continue polling
            } else {
                console.error('Payment status check failed:', data.error || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            // Continue polling even on error, but update status message
            if (pollCount <= 3) {
                updateStatusMessage('Connecting...');
            } else if (pollCount <= 10) {
                updateStatusMessage('Checking payment status...');
            } else {
                updateStatusMessage('Still checking... Please wait.');
            }
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
            statusEl.innerHTML = `
                <div class="inline-flex items-center space-x-3 text-white/90">
                    <svg class="animate-spin h-6 w-6 text-[#FCD535]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-medium">${message}</span>
                </div>
            `;
        }
    }

    function showSuccessMessage() {
        const successEl = document.getElementById('success-message');
        const statusEl = document.getElementById('polling-status');
        if (successEl) {
            successEl.classList.remove('hidden');
        }
        if (statusEl) {
            statusEl.innerHTML = `
                <div class="inline-flex items-center space-x-3 text-[#00D9A5]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Payment completed successfully!</span>
                </div>
            `;
        }
    }

    // Start polling immediately
    pollInterval = setInterval(checkPaymentStatus, pollIntervalMs);
    
    // Initial check immediately (no delay)
    checkPaymentStatus();

    // Stop polling when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            // Restart polling when page becomes visible again
            if (!pollInterval && pollCount < maxPolls && !isRedirecting) {
                pollInterval = setInterval(checkPaymentStatus, pollIntervalMs);
                checkPaymentStatus();
            }
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        stopPolling();
    });
})();
</script>
@endif
@endpush
@endsection

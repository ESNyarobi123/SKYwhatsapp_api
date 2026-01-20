@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Settings</h1>
        <p class="text-white/70">Configure payment options, contact information, and system utilities</p>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 text-[#00D9A5]">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 text-[#EA3943]">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4">
            <ul class="text-sm text-[#EA3943] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-white/10 mb-6">
        <nav class="flex space-x-6 -mb-px">
            <button onclick="switchTab('payment')" id="tab-payment" class="tab-button active py-3 px-1 border-b-2 border-[#FCD535] font-medium text-sm text-[#FCD535] transition-colors">
                Payment Settings
            </button>
            <button onclick="switchTab('contact')" id="tab-contact" class="tab-button py-3 px-1 border-b-2 border-transparent font-medium text-sm text-white/70 hover:text-white hover:border-white/20 transition-colors">
                Contact Settings
            </button>
            <button onclick="switchTab('system')" id="tab-system" class="tab-button py-3 px-1 border-b-2 border-transparent font-medium text-sm text-white/70 hover:text-white hover:border-white/20 transition-colors">
                System Settings
            </button>
        </nav>
    </div>

    <!-- Payment Settings Tab -->
    <div id="tab-content-payment" class="tab-content">
        <x-card>
            <h2 class="text-xl font-semibold text-white mb-6">Payment Configuration</h2>
            
            <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="space-y-6" id="payment-settings-form" onsubmit="return true;">
                @csrf

                <!-- ZenoPay API Key -->
                <div>
                    <label for="zenopay_api_key" class="block text-sm font-medium text-white/90 mb-2">
                        ZenoPay API Key
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="zenopay_api_key" 
                            name="zenopay_api_key" 
                            value="" 
                            class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] pr-10"
                            placeholder="{{ $zenopayApiKey ? 'API key is set (enter new key to change)' : 'Enter your ZenoPay API key' }}"
                        >
                        @if($zenopayApiKey)
                            <p class="mt-1 text-xs text-[#00D9A5]">✓ API key is configured (leave blank to keep current key)</p>
                        @endif
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('zenopay_api_key', this)" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition-colors"
                        >
                            <svg id="zenopay_toggle_icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-white/50">Your API key for ZenoPay payment processing</p>
                </div>

                <!-- ZenoPay Test Mode (Local Development Only) -->
                @if(config('app.env') === 'local')
                <div>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="zenopay_test_mode" 
                            value="1"
                            {{ ($zenopayTestMode ?? false) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-white/20 bg-[#1A1A1A] text-[#FCD535] focus:ring-2 focus:ring-[#FCD535] focus:ring-offset-0"
                        >
                        <div>
                            <span class="block text-sm font-medium text-white/90">Enable Test Mode</span>
                            <span class="text-xs text-white/50">Bypass ZenoPay API for local testing (payments will auto-succeed)</span>
                        </div>
                    </label>
                </div>
                @endif

                <!-- PayPal Email -->
                <div>
                    <label for="paypal_email" class="block text-sm font-medium text-white/90 mb-2">
                        PayPal Email
                    </label>
                    <input 
                        type="email" 
                        id="paypal_email" 
                        name="paypal_email" 
                        value="{{ $paypalEmail ?? '' }}" 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                        placeholder="your-email@paypal.com"
                    >
                    <p class="mt-1 text-xs text-white/50">Email address associated with your PayPal account</p>
                </div>

                <!-- PayPal.me Username -->
                <div>
                    <label for="paypal_me_username" class="block text-sm font-medium text-white/90 mb-2">
                        PayPal.me Username
                    </label>
                    <input 
                        type="text" 
                        id="paypal_me_username" 
                        name="paypal_me_username" 
                        value="{{ $paypalMeUsername ?? '' }}" 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                        placeholder="your-paypal-me-username"
                    >
                    <p class="mt-1 text-xs text-white/50">Your PayPal.me username (e.g., if your link is paypal.me/john, enter "john"). Leave empty to use email prefix.</p>
                </div>

                <!-- TRC20 Settings -->
                <div class="border-t border-white/5 pt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">TRC20 Crypto Payment</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="trc20_wallet_address" class="block text-sm font-medium text-white/90 mb-2">
                                TRC20 Wallet Address
                            </label>
                            <input 
                                type="text" 
                                id="trc20_wallet_address" 
                                name="trc20_wallet_address" 
                                value="{{ $trc20WalletAddress ?? '' }}" 
                                class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] font-mono text-sm"
                                placeholder="TXXXXXXXXXXXXXXXXXXXXXXXX"
                            >
                            <p class="mt-1 text-xs text-white/50">Your TRC20 wallet address for receiving payments</p>
                        </div>

                        <div>
                            <label for="trc20_screenshot" class="block text-sm font-medium text-white/90 mb-2">
                                TRC20 Payment Screenshot
                            </label>
                            @if($trc20Screenshot)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $trc20Screenshot) }}" alt="TRC20 Screenshot" class="max-w-xs rounded-lg border border-white/10">
                                </div>
                            @endif
                            <input 
                                type="file" 
                                id="trc20_screenshot" 
                                name="screenshot" 
                                accept="image/*"
                                class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FCD535] file:text-[#1A1A1A] hover:file:bg-[#F0C420] cursor-pointer"
                            >
                            <p class="mt-1 text-xs text-white/50">Upload a screenshot showing your TRC20 payment instructions (optional)</p>
                        </div>

                        <form method="POST" action="{{ route('admin.settings.trc20.screenshot') }}" enctype="multipart/form-data" class="hidden" id="trc20-screenshot-form">
                            @csrf
                            <input type="file" name="screenshot" id="trc20_screenshot_file" accept="image/*">
                        </form>
                    </div>
                </div>

                <!-- Payment Methods Toggle -->
                <div class="border-t border-white/5 pt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Enable/Disable Payment Methods</h3>
                    
                    <div class="space-y-4">
                        @php
                            $methodNames = [
                                'zenopay_card' => 'ZenoPay Card Payments',
                                'zenopay_mobile' => 'ZenoPay Mobile Money (Tanzania)',
                                'paypal' => 'PayPal',
                                'trc20' => 'TRC20 Crypto',
                            ];
                        @endphp

                        @foreach(['zenopay_card', 'zenopay_mobile', 'paypal', 'trc20'] as $method)
                            @php
                                $paymentMethod = $paymentMethods->firstWhere('method', $method);
                                $isEnabled = $paymentMethod ? $paymentMethod->is_enabled : false;
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-[#1A1A1A] rounded-lg border border-white/5">
                                <div>
                                    <h4 class="text-white font-medium">{{ $methodNames[$method] ?? ucfirst($method) }}</h4>
                                    <p class="text-xs text-white/50 mt-1">
                                        @if($method === 'zenopay_card')
                                            Accept Visa and MasterCard payments
                                        @elseif($method === 'zenopay_mobile')
                                            Accept mobile money payments (Tanzania)
                                        @elseif($method === 'paypal')
                                            Accept PayPal payments (manual verification)
                                        @elseif($method === 'trc20')
                                            Accept TRC20 cryptocurrency payments
                                        @endif
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only peer" 
                                        {{ $isEnabled ? 'checked' : '' }}
                                        onchange="togglePaymentMethod('{{ $method }}', this.checked)"
                                    >
                                    <div class="w-11 h-6 bg-white/10 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#FCD535] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FCD535]"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                    <button type="submit" form="payment-settings-form" class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#1A1A1A] disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3 text-base bg-[#FCD535] text-[#1A1A1A] hover:bg-[#F0C420] focus:ring-[#FCD535]">
                        Save Payment Settings
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Contact Settings Tab -->
    <div id="tab-content-contact" class="tab-content hidden">
        <x-card>
            <h2 class="text-xl font-semibold text-white mb-6">Contact Configuration</h2>
            
            <form method="POST" action="{{ route('admin.settings.contact.update') }}" class="space-y-6">
                @csrf

                <!-- WhatsApp Number -->
                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-white/90 mb-2">
                        WhatsApp Contact Number
                    </label>
                    <input 
                        type="text" 
                        id="whatsapp_number" 
                        name="whatsapp_number" 
                        value="{{ $whatsappNumber ?? '' }}" 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]"
                        placeholder="+255123456789"
                    >
                    <p class="mt-1 text-xs text-white/50">This number will be displayed on the welcome page for contact purposes</p>
                </div>

                <!-- Preview -->
                @if($whatsappNumber)
                    <div class="p-4 bg-[#1A1A1A] rounded-lg border border-white/5">
                        <p class="text-sm text-white/70 mb-2">Preview on landing page:</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" target="_blank" class="inline-flex items-center space-x-2 text-[#FCD535] hover:text-[#F0C420] transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            <span>{{ $whatsappNumber }}</span>
                        </a>
                    </div>
                @endif

                <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                    <button type="submit" class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#1A1A1A] disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3 text-base bg-[#FCD535] text-[#1A1A1A] hover:bg-[#F0C420] focus:ring-[#FCD535]">
                        Save Contact Settings
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    <!-- System Settings Tab -->
    <div id="tab-content-system" class="tab-content hidden">
        <x-card>
            <h2 class="text-xl font-semibold text-white mb-6">System Utilities</h2>
            
            <div class="space-y-4">
                <p class="text-white/70 text-sm">Clear caches and optimize your application for better performance.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}" class="inline">
                        @csrf
                        <input type="hidden" name="type" value="cache">
                        <x-button type="submit" variant="outline" size="md" class="w-full">Clear Application Cache</x-button>
                    </form>

                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}" class="inline">
                        @csrf
                        <input type="hidden" name="type" value="config">
                        <x-button type="submit" variant="outline" size="md" class="w-full">Clear Config Cache</x-button>
                    </form>

                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}" class="inline">
                        @csrf
                        <input type="hidden" name="type" value="route">
                        <x-button type="submit" variant="outline" size="md" class="w-full">Clear Route Cache</x-button>
                    </form>

                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}" class="inline">
                        @csrf
                        <input type="hidden" name="type" value="view">
                        <x-button type="submit" variant="outline" size="md" class="w-full">Clear View Cache</x-button>
                    </form>
                </div>

                <div class="pt-4 border-t border-white/5">
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}" class="inline mb-4">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <x-button type="submit" variant="outline" size="md" class="w-full">Clear All Caches</x-button>
                    </form>

                    <form method="POST" action="{{ route('admin.settings.system.refresh') }}" class="inline">
                        @csrf
                        <x-button type="submit" variant="primary" size="md" class="w-full">Refresh System (Clear All + Optimize)</x-button>
                    </form>
                    <p class="mt-2 text-xs text-white/50">This will clear all caches and rebuild optimized versions</p>
                </div>
            </div>

            <div class="pt-6 border-t border-white/5">
                <h3 class="text-lg font-semibold text-white mb-4">API Documentation</h3>
                <p class="text-white/70 text-sm mb-4">Upload the PDF documentation that will be visible to users.</p>
                
                <form method="POST" action="{{ route('admin.settings.documentation.upload') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="api_documentation" class="block text-sm font-medium text-white/90 mb-2">
                            Upload PDF Document
                        </label>
                        @php
                            $currentDoc = \App\Models\Setting::getValue('api_documentation_pdf');
                        @endphp
                        
                        @if($currentDoc)
                            <div class="mb-3 flex items-center space-x-3 p-3 bg-[#1A1A1A] rounded-lg border border-white/10">
                                <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-white/70">Current file: {{ basename($currentDoc) }}</span>
                                <a href="{{ asset('storage/' . $currentDoc) }}" target="_blank" class="text-xs text-[#FCD535] hover:underline">View</a>
                            </div>
                        @endif

                        <input 
                            type="file" 
                            id="api_documentation" 
                            name="document" 
                            accept="application/pdf"
                            class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#FCD535] file:text-[#1A1A1A] hover:file:bg-[#F0C420] cursor-pointer"
                        >
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#1A1A1A] disabled:opacity-50 disabled:cursor-not-allowed px-6 py-3 text-base bg-[#FCD535] text-[#1A1A1A] hover:bg-[#F0C420] focus:ring-[#FCD535]">
                        Upload Documentation
                    </button>
                </form>
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-[#FCD535]', 'text-[#FCD535]');
        button.classList.add('border-transparent', 'text-white/70');
    });

    // Show selected tab content
    document.getElementById(`tab-content-${tabName}`).classList.remove('hidden');

    // Add active class to selected tab
    const activeTab = document.getElementById(`tab-${tabName}`);
    activeTab.classList.add('active', 'border-[#FCD535]', 'text-[#FCD535]');
    activeTab.classList.remove('border-transparent', 'text-white/70');
}

function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('svg');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
    } else {
        input.type = 'password';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
    }
}

function togglePaymentMethod(method, isEnabled) {
    fetch('{{ route("admin.settings.payment-method.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            method: method,
            is_enabled: isEnabled
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optionally show a success message
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush
@endsection
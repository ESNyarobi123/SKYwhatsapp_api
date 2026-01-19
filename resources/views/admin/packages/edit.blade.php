@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Edit Package</h1>
        <p class="text-white/70">Update package details</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4">
                    <ul class="text-sm text-[#EA3943] space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Package Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $package->name) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-white/90 mb-2">Price (USD) *</label>
                    <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $package->price_usd ?? $package->price) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    <p class="mt-1 text-xs text-white/50">💡 Base price in USD. System converts automatically to TZS/USDT for different payment methods.</p>
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-white/90 mb-2">Currency (Default: USD)</label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" readonly>
                    <p class="mt-1 text-xs text-white/50">Base currency is USD. Prices convert automatically to TZS (mobile) and USDT (crypto).</p>
                </div>

                <div>
                    <label for="duration_days" class="block text-sm font-medium text-white/90 mb-2">Duration (Days) *</label>
                    <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" required min="1" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-white/90 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">{{ old('description', $package->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-white/90 mb-3">Feature Limits</label>
                    <p class="text-white/60 text-xs mb-4">Set limits for each feature. Leave limit empty or set to -1 for unlimited.</p>
                    
                    @php
                        $features = $package->features ?? [];
                        $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
                        
                        // Extract feature values
                        $instancesLimit = $isOldFormat ? '' : ($features['instances']['limit'] ?? old('feature_instances_limit', ''));
                        $instancesPeriod = $isOldFormat ? 'lifetime' : ($features['instances']['period'] ?? old('feature_instances_period', 'lifetime'));
                        $messagesLimit = $isOldFormat ? '' : ($features['messages']['limit'] ?? old('feature_messages_limit', ''));
                        $messagesPeriod = $isOldFormat ? 'day' : ($features['messages']['period'] ?? old('feature_messages_period', 'day'));
                        $apiCallsLimit = $isOldFormat ? '' : ($features['api_calls']['limit'] ?? old('feature_api_calls_limit', ''));
                        $apiCallsPeriod = $isOldFormat ? 'month' : ($features['api_calls']['period'] ?? old('feature_api_calls_period', 'month'));
                        $apiKeysLimit = $isOldFormat ? '' : ($features['api_keys']['limit'] ?? old('feature_api_keys_limit', ''));
                        $apiKeysPeriod = $isOldFormat ? 'lifetime' : ($features['api_keys']['period'] ?? old('feature_api_keys_period', 'lifetime'));
                    @endphp
                    
                    <div class="space-y-4 border border-white/10 rounded-lg p-4">
                        <!-- Instances Limit -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="feature_instances_limit" class="block text-sm font-medium text-white/80 mb-2">Max Instances</label>
                                <input type="number" id="feature_instances_limit" name="feature_instances_limit" value="{{ old('feature_instances_limit', $instancesLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1 for unlimited">
                                <p class="text-white/50 text-xs mt-1">Total instances allowed (lifetime)</p>
                            </div>
                            <div>
                                <label for="feature_instances_period" class="block text-sm font-medium text-white/80 mb-2">Period</label>
                                <select id="feature_instances_period" name="feature_instances_period" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                                    <option value="lifetime" {{ $instancesPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                </select>
                            </div>
                        </div>

                        <!-- Messages Limit -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="feature_messages_limit" class="block text-sm font-medium text-white/80 mb-2">Max Messages</label>
                                <input type="number" id="feature_messages_limit" name="feature_messages_limit" value="{{ old('feature_messages_limit', $messagesLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1 for unlimited">
                                <p class="text-white/50 text-xs mt-1">Messages allowed per period</p>
                            </div>
                            <div>
                                <label for="feature_messages_period" class="block text-sm font-medium text-white/80 mb-2">Period</label>
                                <select id="feature_messages_period" name="feature_messages_period" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                                    <option value="day" {{ $messagesPeriod === 'day' ? 'selected' : '' }}>Per Day</option>
                                    <option value="month" {{ $messagesPeriod === 'month' ? 'selected' : '' }}>Per Month</option>
                                    <option value="year" {{ $messagesPeriod === 'year' ? 'selected' : '' }}>Per Year</option>
                                    <option value="lifetime" {{ $messagesPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                </select>
                            </div>
                        </div>

                        <!-- API Calls Limit -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="feature_api_calls_limit" class="block text-sm font-medium text-white/80 mb-2">Max API Calls</label>
                                <input type="number" id="feature_api_calls_limit" name="feature_api_calls_limit" value="{{ old('feature_api_calls_limit', $apiCallsLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1 for unlimited">
                                <p class="text-white/50 text-xs mt-1">API calls allowed per period</p>
                            </div>
                            <div>
                                <label for="feature_api_calls_period" class="block text-sm font-medium text-white/80 mb-2">Period</label>
                                <select id="feature_api_calls_period" name="feature_api_calls_period" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                                    <option value="day" {{ $apiCallsPeriod === 'day' ? 'selected' : '' }}>Per Day</option>
                                    <option value="month" {{ $apiCallsPeriod === 'month' ? 'selected' : '' }}>Per Month</option>
                                    <option value="year" {{ $apiCallsPeriod === 'year' ? 'selected' : '' }}>Per Year</option>
                                    <option value="lifetime" {{ $apiCallsPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                </select>
                            </div>
                        </div>

                        <!-- API Keys Limit -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="feature_api_keys_limit" class="block text-sm font-medium text-white/80 mb-2">Max API Keys</label>
                                <input type="number" id="feature_api_keys_limit" name="feature_api_keys_limit" value="{{ old('feature_api_keys_limit', $apiKeysLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1 for unlimited">
                                <p class="text-white/50 text-xs mt-1">Total API keys allowed (lifetime)</p>
                            </div>
                            <div>
                                <label for="feature_api_keys_period" class="block text-sm font-medium text-white/80 mb-2">Period</label>
                                <select id="feature_api_keys_period" name="feature_api_keys_period" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                                    <option value="lifetime" {{ $apiKeysPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="h-4 w-4 text-[#FCD535] focus:ring-[#FCD535] border-white/20 rounded bg-[#1A1A1A]">
                    <label for="is_active" class="ml-2 block text-sm text-white/70">Active</label>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-white/90 mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <x-button type="submit" variant="primary" size="md">Update Package</x-button>
                <a href="{{ route('admin.packages.index') }}">
                    <x-button type="button" variant="outline" size="md">Cancel</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>

@endsection

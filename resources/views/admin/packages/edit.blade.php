@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#F59E0B] via-[#D97706] to-[#B45309] p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10">
            <nav class="flex items-center text-sm text-white/70 mb-4">
                <a href="{{ route('admin.packages.index') }}" class="hover:text-white transition-colors">Packages</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-white">Edit</span>
            </nav>
            <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Package: {{ $package->name }}
            </h1>
            <p class="text-white/90 text-lg">Configure package details, API limits, and Bot Builder features</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-xl p-4 flex items-start space-x-3">
            <svg class="w-6 h-6 text-[#EA3943] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="text-[#EA3943] font-medium">Please fix the following errors:</p>
                <ul class="text-sm text-[#EA3943]/80 mt-1 space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @php
            $features = $package->features ?? [];
            $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
            
            // Extract API feature values
            $instancesLimit = $isOldFormat ? '' : ($features['instances']['limit'] ?? old('feature_instances_limit', ''));
            $instancesPeriod = $isOldFormat ? 'lifetime' : ($features['instances']['period'] ?? old('feature_instances_period', 'lifetime'));
            $messagesLimit = $isOldFormat ? '' : ($features['messages']['limit'] ?? old('feature_messages_limit', ''));
            $messagesPeriod = $isOldFormat ? 'day' : ($features['messages']['period'] ?? old('feature_messages_period', 'day'));
            $apiCallsLimit = $isOldFormat ? '' : ($features['api_calls']['limit'] ?? old('feature_api_calls_limit', ''));
            $apiCallsPeriod = $isOldFormat ? 'month' : ($features['api_calls']['period'] ?? old('feature_api_calls_period', 'month'));
            $apiKeysLimit = $isOldFormat ? '' : ($features['api_keys']['limit'] ?? old('feature_api_keys_limit', ''));
            $apiKeysPeriod = $isOldFormat ? 'lifetime' : ($features['api_keys']['period'] ?? old('feature_api_keys_period', 'lifetime'));
            
            // Extract Bot feature values
            $botType = $isOldFormat ? 'simple' : ($features['bot_type'] ?? old('bot_type', 'simple'));
            $botRulesLimit = $isOldFormat ? '' : ($features['bot_rules']['limit'] ?? old('bot_rules_limit', ''));
            $botRulesPeriod = $isOldFormat ? 'lifetime' : ($features['bot_rules']['period'] ?? old('bot_rules_period', 'lifetime'));
            $botMenus = $isOldFormat ? false : ($features['bot_menus'] ?? old('bot_menus', false));
            $botButtons = $isOldFormat ? false : ($features['bot_buttons'] ?? old('bot_buttons', false));
            $botAnalytics = $isOldFormat ? false : ($features['bot_analytics'] ?? old('bot_analytics', false));
            $prioritySupport = $isOldFormat ? false : ($features['priority_support'] ?? old('priority_support', false));
        @endphp

        <!-- Basic Information Card -->
        <x-card>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-[#FCD535]/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Basic Information</h2>
                    <p class="text-white/60 text-sm">Package name, pricing, and duration</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Package Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $package->name) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-white/90 mb-2">Price (USD) *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/50">$</span>
                        <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $package->price_usd ?? $package->price) }}" required class="w-full pl-8 pr-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    </div>
                    <p class="mt-1 text-xs text-white/50">💡 Auto-converts to TZS/USDT</p>
                </div>

                <div>
                    <label for="duration_days" class="block text-sm font-medium text-white/90 mb-2">Duration (Days) *</label>
                    <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" required min="1" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-white/90 mb-2">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-white/90 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">{{ old('description', $package->description) }}</textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="h-5 w-5 text-[#FCD535] focus:ring-[#FCD535] border-white/20 rounded bg-[#1A1A1A]">
                    <label for="is_active" class="ml-3 text-sm text-white/70">
                        <span class="font-medium text-white">Active</span> - Package is available for purchase
                    </label>
                </div>
            </div>
        </x-card>

        <!-- API Features Card -->
        <x-card>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-[#3B82F6]/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">API Features</h2>
                    <p class="text-white/60 text-sm">Configure API limits and quotas</p>
                </div>
            </div>
            
            <p class="text-white/60 text-xs mb-4 bg-[#1A1A1A] p-3 rounded-lg">
                💡 <strong>Tip:</strong> Leave limit empty or set to <code class="text-[#FCD535]">-1</code> for unlimited.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Instances Limit -->
                <div class="bg-[#1A1A1A]/50 p-4 rounded-xl border border-white/5">
                    <label class="block text-sm font-medium text-white/90 mb-3 flex items-center gap-2">
                        <span class="text-lg">📱</span> Max Instances
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="feature_instances_limit" value="{{ old('feature_instances_limit', $instancesLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1">
                        <select name="feature_instances_period" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                            <option value="lifetime" {{ $instancesPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                        </select>
                    </div>
                </div>

                <!-- Messages Limit -->
                <div class="bg-[#1A1A1A]/50 p-4 rounded-xl border border-white/5">
                    <label class="block text-sm font-medium text-white/90 mb-3 flex items-center gap-2">
                        <span class="text-lg">💬</span> Max Messages
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="feature_messages_limit" value="{{ old('feature_messages_limit', $messagesLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1">
                        <select name="feature_messages_period" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                            <option value="day" {{ $messagesPeriod === 'day' ? 'selected' : '' }}>Per Day</option>
                            <option value="month" {{ $messagesPeriod === 'month' ? 'selected' : '' }}>Per Month</option>
                            <option value="year" {{ $messagesPeriod === 'year' ? 'selected' : '' }}>Per Year</option>
                            <option value="lifetime" {{ $messagesPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                        </select>
                    </div>
                </div>

                <!-- API Calls Limit -->
                <div class="bg-[#1A1A1A]/50 p-4 rounded-xl border border-white/5">
                    <label class="block text-sm font-medium text-white/90 mb-3 flex items-center gap-2">
                        <span class="text-lg">🔄</span> Max API Calls
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="feature_api_calls_limit" value="{{ old('feature_api_calls_limit', $apiCallsLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1">
                        <select name="feature_api_calls_period" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                            <option value="day" {{ $apiCallsPeriod === 'day' ? 'selected' : '' }}>Per Day</option>
                            <option value="month" {{ $apiCallsPeriod === 'month' ? 'selected' : '' }}>Per Month</option>
                            <option value="year" {{ $apiCallsPeriod === 'year' ? 'selected' : '' }}>Per Year</option>
                            <option value="lifetime" {{ $apiCallsPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                        </select>
                    </div>
                </div>

                <!-- API Keys Limit -->
                <div class="bg-[#1A1A1A]/50 p-4 rounded-xl border border-white/5">
                    <label class="block text-sm font-medium text-white/90 mb-3 flex items-center gap-2">
                        <span class="text-lg">🔑</span> Max API Keys
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="feature_api_keys_limit" value="{{ old('feature_api_keys_limit', $apiKeysLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]" placeholder="-1">
                        <select name="feature_api_keys_period" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                            <option value="lifetime" {{ $apiKeysPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                        </select>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Bot Builder Features Card -->
        <x-card>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-[#8B5CF6]/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Bot Builder Features</h2>
                    <p class="text-white/60 text-sm">Configure automation and chatbot capabilities</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Bot Type Selection -->
                <div class="bg-[#1A1A1A]/50 p-5 rounded-xl border border-white/5">
                    <label class="block text-sm font-medium text-white/90 mb-4">Bot Type</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all {{ $botType === 'simple' ? 'border-[#FCD535] bg-[#FCD535]/10' : 'border-white/10 hover:border-white/30' }}">
                            <input type="radio" name="bot_type" value="simple" {{ $botType === 'simple' ? 'checked' : '' }} class="sr-only peer">
                            <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 {{ $botType === 'simple' ? 'border-[#FCD535] bg-[#FCD535]' : 'border-white/30' }} mr-3 mt-0.5 flex items-center justify-center">
                                @if($botType === 'simple')
                                    <svg class="w-3 h-3 text-[#1A1A1A]" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                @endif
                            </div>
                            <div>
                                <span class="block font-semibold text-white">💬 Simple Bot</span>
                                <span class="block text-sm text-white/60 mt-1">Basic keyword triggers and auto-replies</span>
                            </div>
                        </label>
                        
                        <label class="relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all {{ $botType === 'advanced' ? 'border-[#8B5CF6] bg-[#8B5CF6]/10' : 'border-white/10 hover:border-white/30' }}">
                            <input type="radio" name="bot_type" value="advanced" {{ $botType === 'advanced' ? 'checked' : '' }} class="sr-only peer">
                            <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 {{ $botType === 'advanced' ? 'border-[#8B5CF6] bg-[#8B5CF6]' : 'border-white/30' }} mr-3 mt-0.5 flex items-center justify-center">
                                @if($botType === 'advanced')
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                @endif
                            </div>
                            <div>
                                <span class="block font-semibold text-white">🤖 Advanced Bot</span>
                                <span class="block text-sm text-white/60 mt-1">Multi-step flows, menus, buttons & analytics</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Bot Rules Limit -->
                <div class="bg-[#1A1A1A]/50 p-5 rounded-xl border border-white/5">
                    <label class="block text-sm font-medium text-white/90 mb-3 flex items-center gap-2">
                        <span class="text-lg">📋</span> Auto-Reply Rules Limit
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="number" name="bot_rules_limit" value="{{ old('bot_rules_limit', $botRulesLimit) }}" min="-1" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#8B5CF6]" placeholder="-1 for unlimited">
                            <p class="text-white/50 text-xs mt-2">Number of auto-reply rules allowed</p>
                        </div>
                        <div>
                            <select name="bot_rules_period" class="w-full px-4 py-3 bg-[#252525] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#8B5CF6]">
                                <option value="lifetime" {{ $botRulesPeriod === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bot Feature Toggles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Menu Bot -->
                    <label class="flex items-center p-4 rounded-xl bg-[#1A1A1A]/50 border border-white/5 cursor-pointer hover:border-[#8B5CF6]/50 transition-all">
                        <input type="checkbox" name="bot_menus" value="1" {{ $botMenus ? 'checked' : '' }} class="h-5 w-5 text-[#8B5CF6] focus:ring-[#8B5CF6] border-white/20 rounded bg-[#252525]">
                        <div class="ml-4">
                            <span class="block font-medium text-white">📱 Menu Bot</span>
                            <span class="block text-sm text-white/60">Interactive menu navigation</span>
                        </div>
                    </label>

                    <!-- Quick Buttons -->
                    <label class="flex items-center p-4 rounded-xl bg-[#1A1A1A]/50 border border-white/5 cursor-pointer hover:border-[#8B5CF6]/50 transition-all">
                        <input type="checkbox" name="bot_buttons" value="1" {{ $botButtons ? 'checked' : '' }} class="h-5 w-5 text-[#8B5CF6] focus:ring-[#8B5CF6] border-white/20 rounded bg-[#252525]">
                        <div class="ml-4">
                            <span class="block font-medium text-white">🔘 Quick Buttons</span>
                            <span class="block text-sm text-white/60">Clickable reply buttons</span>
                        </div>
                    </label>

                    <!-- Bot Analytics -->
                    <label class="flex items-center p-4 rounded-xl bg-[#1A1A1A]/50 border border-white/5 cursor-pointer hover:border-[#8B5CF6]/50 transition-all">
                        <input type="checkbox" name="bot_analytics" value="1" {{ $botAnalytics ? 'checked' : '' }} class="h-5 w-5 text-[#8B5CF6] focus:ring-[#8B5CF6] border-white/20 rounded bg-[#252525]">
                        <div class="ml-4">
                            <span class="block font-medium text-white">📊 Bot Analytics</span>
                            <span class="block text-sm text-white/60">Message stats & insights</span>
                        </div>
                    </label>

                    <!-- Priority Support -->
                    <label class="flex items-center p-4 rounded-xl bg-[#1A1A1A]/50 border border-white/5 cursor-pointer hover:border-[#00D9A5]/50 transition-all">
                        <input type="checkbox" name="priority_support" value="1" {{ $prioritySupport ? 'checked' : '' }} class="h-5 w-5 text-[#00D9A5] focus:ring-[#00D9A5] border-white/20 rounded bg-[#252525]">
                        <div class="ml-4">
                            <span class="block font-medium text-white">⚡ Priority Support</span>
                            <span class="block text-sm text-white/60">Fast-track customer support</span>
                        </div>
                    </label>
                </div>
            </div>
        </x-card>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('admin.packages.index') }}">
                <x-button type="button" variant="outline" size="md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Packages
                </x-button>
            </a>
            <x-button type="submit" variant="primary" size="lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Package
            </x-button>
        </div>
    </form>
</div>
@endsection

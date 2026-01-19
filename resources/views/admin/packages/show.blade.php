@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $package->name }}</h1>
            <p class="text-white/70">Package details</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.packages.edit', $package) }}">
                <x-button variant="primary" size="md">Edit</x-button>
            </a>
            <a href="{{ route('admin.packages.index') }}">
                <x-button variant="outline" size="md">Back</x-button>
            </a>
        </div>
    </div>

    <x-card>
        <div class="space-y-6">
            <div class="flex items-start justify-between pb-4 border-b border-white/5">
                <div>
                    <h2 class="text-xl font-semibold text-white mb-2">{{ $package->name }}</h2>
                    <x-badge variant="{{ $package->is_active ? 'success' : 'error' }}">
                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-[#FCD535]">{{ number_format($package->getPriceForCurrency('USD'), 2) }} USD</p>
                    <p class="text-white/60 text-sm">Per {{ $package->duration_days }} days</p>
                    <p class="text-white/40 text-xs mt-1">💡 Base price - converts to TZS/USDT for payment methods</p>
                </div>
            </div>

            @if($package->description)
                <div>
                    <h3 class="text-sm font-medium text-white/70 mb-2">Description</h3>
                    <p class="text-white">{{ $package->description }}</p>
                </div>
            @endif

            @if($package->features && count($package->features) > 0)
                <div>
                    <h3 class="text-sm font-medium text-white/70 mb-3">Features</h3>
                    <ul class="space-y-2">
                        @php
                            $features = $package->features ?? [];
                            $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
                        @endphp
                        
                        @if($isOldFormat)
                            @foreach($features as $feature)
                                <li class="text-white flex items-center">
                                    <svg class="w-5 h-5 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        @else
                            @if(isset($features['instances']))
                                <li class="text-white flex items-center">
                                    <svg class="w-5 h-5 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    @if($features['instances']['limit'] == -1)
                                        Unlimited Instances
                                    @else
                                        {{ $features['instances']['limit'] }} Instance{{ $features['instances']['limit'] != 1 ? 's' : '' }}
                                    @endif
                                </li>
                            @endif
                            @if(isset($features['messages']))
                                <li class="text-white flex items-center">
                                    <svg class="w-5 h-5 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    @if($features['messages']['limit'] == -1)
                                        Unlimited Messages
                                    @else
                                        {{ number_format($features['messages']['limit']) }} Messages
                                    @endif
                                    @if($features['messages']['period'] != 'lifetime')
                                        / {{ ucfirst($features['messages']['period']) }}
                                    @endif
                                </li>
                            @endif
                            @if(isset($features['api_calls']))
                                <li class="text-white flex items-center">
                                    <svg class="w-5 h-5 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    @if($features['api_calls']['limit'] == -1)
                                        Unlimited API Calls
                                    @else
                                        {{ number_format($features['api_calls']['limit']) }} API Calls
                                    @endif
                                    @if($features['api_calls']['period'] != 'lifetime')
                                        / {{ ucfirst($features['api_calls']['period']) }}
                                    @endif
                                </li>
                            @endif
                            @if(isset($features['api_keys']))
                                <li class="text-white flex items-center">
                                    <svg class="w-5 h-5 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    @if($features['api_keys']['limit'] == -1)
                                        Unlimited API Keys
                                    @else
                                        {{ $features['api_keys']['limit'] }} API Key{{ $features['api_keys']['limit'] != 1 ? 's' : '' }}
                                    @endif
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-white/5">
                <div>
                    <p class="text-sm text-white/70 mb-1">Base Currency</p>
                    <p class="text-white font-medium">USD (converts automatically)</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Duration</p>
                    <p class="text-white font-medium">{{ $package->duration_days }} days</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Sort Order</p>
                    <p class="text-white font-medium">{{ $package->sort_order ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Subscriptions Count</p>
                    <p class="text-white font-medium">{{ $package->subscriptions_count ?? 0 }}</p>
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Packages Management</h1>
            <p class="text-white/70">Manage subscription packages and pricing</p>
        </div>
        <a href="{{ route('admin.packages.create') }}">
            <x-button variant="primary" size="md">Create Package</x-button>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-[#EA3943] mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-[#EA3943] text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($packages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $package)
                <x-card hover>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-white mb-2">{{ $package->name }}</h3>
                            @if($package->description)
                                <p class="text-white/70 text-sm mb-3">{{ $package->description }}</p>
                            @endif
                            <div class="mb-3">
                                <p class="text-2xl font-bold text-[#FCD535]">{{ number_format($package->getPriceForCurrency('USD'), 2) }} USD</p>
                                <p class="text-white/60 text-xs">Per {{ $package->duration_days }} days</p>
                                <p class="text-white/40 text-xs mt-1">💡 Converts to TZS/USDT for payment methods</p>
                            </div>
                            @if($package->features && count($package->features) > 0)
                                <ul class="space-y-1 mb-4">
                                    @php
                                        $features = $package->features ?? [];
                                        $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
                                    @endphp
                                    
                                    @if($isOldFormat)
                                        @foreach($features as $feature)
                                            <li class="text-white/70 text-sm flex items-center">
                                                <svg class="w-4 h-4 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    @else
                                        @if(isset($features['instances']))
                                            <li class="text-white/70 text-sm flex items-center">
                                                <svg class="w-4 h-4 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            <li class="text-white/70 text-sm flex items-center">
                                                <svg class="w-4 h-4 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            <li class="text-white/70 text-sm flex items-center">
                                                <svg class="w-4 h-4 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            <li class="text-white/70 text-sm flex items-center">
                                                <svg class="w-4 h-4 text-[#FCD535] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <x-badge variant="{{ $package->is_active ? 'success' : 'error' }}">
                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                            </x-badge>
                            @if($package->subscriptions_count > 0)
                                <span class="text-xs text-white/50">
                                    {{ $package->subscriptions_count }} Subscription{{ $package->subscriptions_count != 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                        <a href="{{ route('admin.packages.edit', $package) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm font-medium transition-colors">Edit</a>
                        <a href="{{ route('admin.packages.show', $package) }}" class="text-white/70 hover:text-white text-sm transition-colors">View</a>
                        @if($package->subscriptions_count > 0)
                            <span class="text-white/40 text-sm" title="Cannot delete package with {{ $package->subscriptions_count }} subscription(s)">
                                Delete
                            </span>
                        @else
                            <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this package?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[#EA3943] hover:text-[#D1323A] text-sm font-medium transition-colors">Delete</button>
                            </form>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $packages->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-white/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-white/70 mb-4">No packages yet</p>
                <a href="{{ route('admin.packages.create') }}">
                    <x-button variant="primary" size="md">Create Your First Package</x-button>
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection

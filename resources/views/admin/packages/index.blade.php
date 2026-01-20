@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Modern Header with Gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#F59E0B] via-[#D97706] to-[#B45309] p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIyMCIvPjwvZz48L2c+PC9zdmc+')] opacity-20"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Packages Management
                </h1>
                <p class="text-white/90 text-lg">Configure subscription packages with API & Bot features</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold rounded-xl transition-all hover:scale-105 border border-white/30">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Package
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-xl p-4 flex items-center space-x-3">
            <svg class="w-6 h-6 text-[#00D9A5] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[#00D9A5]">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-xl p-4">
            @foreach($errors->all() as $error)
                <p class="text-[#EA3943] text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($packages->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($packages as $package)
                @php
                    $features = $package->features ?? [];
                    $botType = $features['bot_type'] ?? 'simple';
                    $isAdvancedBot = $botType === 'advanced';
                @endphp
                
                <div class="group relative overflow-hidden bg-[#252525] border border-white/10 rounded-2xl hover:border-[#FCD535]/50 transition-all duration-300">
                    <!-- Package Header -->
                    <div class="p-6 border-b border-white/5">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-white group-hover:text-[#FCD535] transition-colors">{{ $package->name }}</h3>
                                    <x-badge variant="{{ $package->is_active ? 'success' : 'error' }}">
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </x-badge>
                                </div>
                                @if($package->description)
                                    <p class="text-white/60 text-sm line-clamp-2">{{ $package->description }}</p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-3xl font-bold text-[#FCD535]">${{ number_format($package->getPriceForCurrency('USD'), 0) }}</p>
                                <p class="text-white/50 text-sm">/ {{ $package->duration_days }} days</p>
                                @if($package->subscriptions_count > 0)
                                    <span class="inline-flex items-center px-2 py-1 mt-2 bg-[#8B5CF6]/20 text-[#8B5CF6] text-xs font-medium rounded-lg">
                                        {{ $package->subscriptions_count }} Subscribers
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features Grid -->
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <!-- API Features Column -->
                        <div>
                            <p class="text-xs text-white/40 uppercase tracking-wider font-semibold mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                                API Features
                            </p>
                            <ul class="space-y-2">
                                @if(isset($features['instances']))
                                    <li class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">
                                            @if($features['instances']['limit'] == -1) <strong class="text-[#FCD535]">∞</strong> @else <strong>{{ $features['instances']['limit'] }}</strong> @endif Instances
                                        </span>
                                    </li>
                                @endif
                                @if(isset($features['messages']))
                                    <li class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">
                                            @if($features['messages']['limit'] == -1) <strong class="text-[#FCD535]">∞</strong> @else <strong>{{ number_format($features['messages']['limit']) }}</strong> @endif Msgs/{{ ucfirst(substr($features['messages']['period'] ?? 'day', 0, 1)) }}
                                        </span>
                                    </li>
                                @endif
                                @if(isset($features['api_keys']))
                                    <li class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-[#00D9A5] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">
                                            @if($features['api_keys']['limit'] == -1) <strong class="text-[#FCD535]">∞</strong> @else <strong>{{ $features['api_keys']['limit'] }}</strong> @endif API Keys
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        
                        <!-- Bot Features Column -->
                        <div>
                            <p class="text-xs text-white/40 uppercase tracking-wider font-semibold mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#8B5CF6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Bot Builder
                            </p>
                            
                            <!-- Bot Type Badge -->
                            <div class="mb-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold {{ $isAdvancedBot ? 'bg-gradient-to-r from-[#FCD535]/20 to-[#8B5CF6]/20 text-[#FCD535] border border-[#FCD535]/30' : 'bg-white/10 text-white/70' }}">
                                    {{ $isAdvancedBot ? '🤖 Advanced Bot' : '💬 Simple Bot' }}
                                </span>
                            </div>
                            
                            <ul class="space-y-2">
                                @if(isset($features['bot_rules']))
                                    <li class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-[#FCD535] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">
                                            @if($features['bot_rules']['limit'] == -1) <strong class="text-[#FCD535]">∞</strong> @else <strong>{{ $features['bot_rules']['limit'] }}</strong> @endif Rules
                                        </span>
                                    </li>
                                @endif
                                <li class="flex items-center gap-2 text-sm">
                                    @if($features['bot_menus'] ?? false)
                                        <svg class="w-4 h-4 text-[#FCD535] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">Menu Bot</span>
                                    @else
                                        <svg class="w-4 h-4 text-white/30 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                                        <span class="text-white/40 line-through">Menu Bot</span>
                                    @endif
                                </li>
                                <li class="flex items-center gap-2 text-sm">
                                    @if($features['bot_buttons'] ?? false)
                                        <svg class="w-4 h-4 text-[#FCD535] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">Quick Buttons</span>
                                    @else
                                        <svg class="w-4 h-4 text-white/30 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                                        <span class="text-white/40 line-through">Quick Buttons</span>
                                    @endif
                                </li>
                                <li class="flex items-center gap-2 text-sm">
                                    @if($features['bot_analytics'] ?? false)
                                        <svg class="w-4 h-4 text-[#FCD535] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        <span class="text-white/80">Analytics</span>
                                    @else
                                        <svg class="w-4 h-4 text-white/30 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                                        <span class="text-white/40 line-through">Analytics</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Actions Footer -->
                    <div class="px-6 py-4 bg-white/5 border-t border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.packages.edit', $package) }}" class="inline-flex items-center px-4 py-2 bg-[#FCD535] hover:bg-[#F0C420] text-[#1A1A1A] text-sm font-semibold rounded-lg transition-all">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('admin.packages.show', $package) }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View
                            </a>
                        </div>
                        @if($package->subscriptions_count == 0)
                            <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this package?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 text-[#EA3943] hover:bg-[#EA3943]/10 text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $packages->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-[#F59E0B]/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No Packages Yet</h3>
                <p class="text-white/70 mb-6">Create your first subscription package to get started.</p>
                <a href="{{ route('admin.packages.create') }}">
                    <x-button variant="primary" size="md">Create Your First Package</x-button>
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection

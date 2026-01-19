@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $apiKey->name }}</h1>
            <p class="text-white/70">API Key details</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($apiKey->is_active)
                <form method="POST" action="{{ route('admin.api-keys.revoke', $apiKey) }}" class="inline" onsubmit="return confirm('Are you sure you want to revoke this API key?')">
                    @csrf
                    <x-button type="submit" variant="secondary" size="md">Revoke</x-button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.api-keys.reactivate', $apiKey) }}" class="inline">
                    @csrf
                    <x-button type="submit" variant="primary" size="md">Reactivate</x-button>
                </form>
            @endif
            <a href="{{ route('admin.api-keys.index') }}">
                <x-button variant="outline" size="md">Back</x-button>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <x-card>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4 border-b border-white/5">
                <div>
                    <p class="text-sm text-white/70 mb-1">Key Name</p>
                    <p class="text-white font-medium text-lg">{{ $apiKey->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Status</p>
                    <x-badge variant="{{ $apiKey->is_active ? 'success' : 'error' }}">
                        {{ $apiKey->is_active ? 'Active' : 'Revoked' }}
                    </x-badge>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Key Preview</p>
                    <code class="text-sm text-white bg-[#1A1A1A] px-3 py-2 rounded block mt-1">...{{ $apiKey->key_preview }}</code>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Owner</p>
                    <a href="{{ route('admin.users.show', $apiKey->user) }}" class="text-[#FCD535] hover:text-[#F0C420] font-medium">
                        {{ $apiKey->user->name }}
                    </a>
                    <p class="text-white/60 text-sm">{{ $apiKey->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Last Used</p>
                    <p class="text-white font-medium">
                        {{ $apiKey->last_used_at ? $apiKey->last_used_at->format('Y-m-d H:i:s') : 'Never' }}
                    </p>
                    @if($apiKey->last_used_at)
                        <p class="text-white/60 text-xs mt-1">{{ $apiKey->last_used_at->diffForHumans() }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Expires At</p>
                    <p class="text-white font-medium">
                        {{ $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d H:i:s') : 'Never' }}
                    </p>
                    @if($apiKey->expires_at)
                        <p class="text-white/60 text-xs mt-1">
                            {{ $apiKey->expires_at->isPast() ? 'Expired ' : '' }}{{ $apiKey->expires_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Created At</p>
                    <p class="text-white font-medium">{{ $apiKey->created_at->format('Y-m-d H:i:s') }}</p>
                    <p class="text-white/60 text-xs mt-1">{{ $apiKey->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Validity</p>
                    @if($apiKey->isExpired())
                        <x-badge variant="error">Expired</x-badge>
                    @elseif(!$apiKey->is_active)
                        <x-badge variant="error">Revoked</x-badge>
                    @else
                        <x-badge variant="success">Valid</x-badge>
                    @endif
                </div>
            </div>

            <div class="pt-4">
                <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    @if($apiKey->is_active)
                        <form method="POST" action="{{ route('admin.api-keys.revoke', $apiKey) }}" class="inline" onsubmit="return confirm('Are you sure you want to revoke this API key? This will immediately disable all requests using this key.')">
                            @csrf
                            <x-button type="submit" variant="secondary" size="sm">Revoke Key</x-button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.api-keys.reactivate', $apiKey) }}" class="inline">
                            @csrf
                            <x-button type="submit" variant="primary" size="sm">Reactivate Key</x-button>
                        </form>
                    @endif
                    <a href="{{ route('admin.users.show', $apiKey->user) }}">
                        <x-button variant="outline" size="sm">View Owner Profile</x-button>
                    </a>
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection

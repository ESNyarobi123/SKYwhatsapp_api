@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $instance->name }}</h1>
            <p class="text-white/70">Instance details</p>
        </div>
        <div class="flex items-center space-x-3">
            <form method="POST" action="{{ route('admin.instances.reset', $instance) }}" class="inline" onsubmit="return confirm('Are you sure you want to reset this instance session?')">
                @csrf
                <x-button type="submit" variant="warning" size="md">Reset Session</x-button>
            </form>
            <form method="POST" action="{{ route('admin.instances.destroy', $instance) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this instance?')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" size="md">Delete</x-button>
            </form>
            <a href="{{ route('admin.instances.index') }}">
                <x-button variant="outline" size="md">Back</x-button>
            </a>
        </div>
    </div>

    <x-card>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4 border-b border-white/5">
                <div>
                    <p class="text-sm text-white/70 mb-1">User</p>
                    <p class="text-white font-medium text-lg">{{ $instance->user->name }}</p>
                    <p class="text-white/60 text-sm">{{ $instance->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Instance Name</p>
                    <p class="text-white font-medium text-lg">{{ $instance->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Phone Number</p>
                    <p class="text-white font-medium">{{ $instance->phone_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Status</p>
                    <x-badge variant="{{ $instance->status === 'connected' ? 'success' : ($instance->status === 'disconnected' ? 'error' : 'warning') }}">
                        {{ ucfirst($instance->status) }}
                    </x-badge>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Last Connected</p>
                    <p class="text-white font-medium">{{ $instance->last_connected_at ? $instance->last_connected_at->format('Y-m-d H:i') : 'Never' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Created</p>
                    <p class="text-white font-medium">{{ $instance->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                <x-metric-card :value="$stats['messages_count']" label="Total Messages" />
                <x-metric-card :value="$stats['inbound_messages']" label="Inbound Messages" />
                <x-metric-card :value="$stats['outbound_messages']" label="Outbound Messages" />
            </div>

            @if($instance->qr_code)
                <div class="pt-4 border-t border-white/5">
                    <p class="text-sm text-white/70 mb-3">QR Code</p>
                    <div class="bg-white p-4 rounded-lg inline-block">
                        <img src="data:image/png;base64,{{ $instance->qr_code }}" alt="QR Code" class="w-64 h-64">
                    </div>
                    @if($instance->qr_expires_at)
                        <p class="text-white/60 text-xs mt-2">Expires: {{ $instance->qr_expires_at->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
            @endif

            @if($instance->session_data)
                <div class="pt-4 border-t border-white/5">
                    <p class="text-sm text-white/70 mb-2">Session Data (Encrypted)</p>
                    <code class="block bg-[#1A1A1A] p-4 rounded-lg text-xs text-white/70 overflow-x-auto">
                        {{ substr($instance->session_data, 0, 200) }}...
                    </code>
                </div>
            @endif
        </div>
    </x-card>
</div>
@endsection

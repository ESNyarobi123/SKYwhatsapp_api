@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Notifications</h1>
                <p class="text-white/70">View all your notifications and updates</p>
            </div>
            <div class="flex items-center space-x-3">
                <form method="POST" action="{{ route('api.notifications.read-all') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-[#1A1A1A] border border-white/10 text-white rounded-lg hover:bg-white/5 transition-colors text-sm">
                        Mark All as Read
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <x-card class="{{ !$notification->is_read ? 'bg-[#FCD535]/5 border-[#FCD535]/20' : '' }}">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-[#1A1A1A] flex items-center justify-center text-2xl">
                                {{ $notification->icon ?? '🔔' }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-white font-semibold">{{ $notification->title }}</h3>
                                        @if(!$notification->is_read)
                                            <span class="w-2 h-2 bg-[#FCD535] rounded-full"></span>
                                        @endif
                                        @if($notification->priority === 'urgent')
                                            <x-badge variant="error" size="sm">Urgent</x-badge>
                                        @elseif($notification->priority === 'high')
                                            <x-badge variant="warning" size="sm">High</x-badge>
                                        @endif
                                    </div>
                                    <p class="text-white/70 text-sm mb-2">{{ $notification->message }}</p>
                                    <div class="flex items-center space-x-4 text-xs text-white/50">
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        <span>{{ $notification->type }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    @if($notification->action_url)
                                        <a href="{{ $notification->action_url }}" class="px-3 py-1.5 bg-[#FCD535] text-[#1A1A1A] text-xs font-semibold rounded-lg hover:bg-[#F0C420] transition-colors">
                                            {{ $notification->action_text ?? 'View' }}
                                        </a>
                                    @endif
                                    @if(!$notification->is_read)
                                        <form method="POST" action="{{ route('api.notifications.read', $notification) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 border border-white/10 text-white/70 text-xs rounded-lg hover:bg-white/5 transition-colors">
                                                Mark Read
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('api.notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 border border-white/10 text-white/70 text-xs rounded-lg hover:bg-[#EA3943]/10 hover:border-[#EA3943]/30 hover:text-[#EA3943] transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔔</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Notifications</h3>
                <p class="text-white/70">You don't have any notifications yet.</p>
            </div>
        </x-card>
    @endif
</div>
@endsection

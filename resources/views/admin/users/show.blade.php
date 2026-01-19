@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ $user->name }}</h1>
            <p class="text-white/70">User details</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}">
                <x-button variant="primary" size="md">Edit</x-button>
            </a>
            <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                @csrf
                <x-button type="submit" variant="secondary" size="md">Login as User</x-button>
            </form>
            <a href="{{ route('admin.users.index') }}">
                <x-button variant="outline" size="md">Back</x-button>
            </a>
        </div>
    </div>

    <x-card>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4 border-b border-white/5">
                <div>
                    <p class="text-sm text-white/70 mb-1">Name</p>
                    <p class="text-white font-medium text-lg">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Email</p>
                    <p class="text-white font-medium text-lg">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Role</p>
                    <x-badge variant="{{ $user->role === 'admin' ? 'gold' : 'default' }}">{{ ucfirst($user->role) }}</x-badge>
                </div>
                <div>
                    <p class="text-sm text-white/70 mb-1">Joined</p>
                    <p class="text-white font-medium">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 pt-4">
                <x-metric-card :value="$stats['subscriptions_count']" label="Subscriptions" />
                <x-metric-card :value="$stats['api_keys_count']" label="API Keys" />
                <x-metric-card :value="$stats['instances_count']" label="Instances" />
                <x-metric-card :value="$stats['messages_count']" label="Messages" />
                <x-metric-card :value="$stats['usage_logs_count']" label="Usage Logs" />
            </div>
        </div>
    </x-card>

    @if($user->subscriptions->count() > 0)
        <x-card>
            <h2 class="text-xl font-semibold text-white mb-4">Subscriptions</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Plan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Amount</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Expires</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($user->subscriptions as $subscription)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-4 text-white">{{ $subscription->plan_name }}</td>
                                <td class="px-4 py-4">
                                    <x-badge variant="{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'error' : 'warning') }}">
                                        {{ ucfirst($subscription->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">{{ number_format($subscription->amount, 2) }}</td>
                                <td class="px-4 py-4 text-white/70 text-sm">{{ $subscription->expires_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>
@endsection

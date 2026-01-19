@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Activity Logs</h1>
        <p class="text-white/70">Monitor system activity and API usage</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-metric-card :value="$stats['total_requests']" label="Total Requests" />
        <x-metric-card :value="$stats['successful_requests']" label="Successful" />
        <x-metric-card :value="$stats['failed_requests']" label="Failed" />
        <x-metric-card :value="$stats['average_response_time'] . 'ms'" label="Avg Response Time" />
    </div>

    <!-- Filters -->
    <x-card>
        <form method="GET" action="{{ route('admin.activity.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="method" class="block text-sm font-medium text-white/70 mb-2">Method</label>
                <select id="method" name="method" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                    <option value="">All</option>
                    <option value="GET" {{ request('method') === 'GET' ? 'selected' : '' }}>GET</option>
                    <option value="POST" {{ request('method') === 'POST' ? 'selected' : '' }}>POST</option>
                    <option value="PUT" {{ request('method') === 'PUT' ? 'selected' : '' }}>PUT</option>
                    <option value="DELETE" {{ request('method') === 'DELETE' ? 'selected' : '' }}>DELETE</option>
                </select>
            </div>
            <div>
                <label for="status_code" class="block text-sm font-medium text-white/70 mb-2">Status Code</label>
                <input type="number" id="status_code" name="status_code" value="{{ request('status_code') }}" placeholder="e.g. 200" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
            </div>
            <div>
                <label for="endpoint" class="block text-sm font-medium text-white/70 mb-2">Endpoint</label>
                <input type="text" id="endpoint" name="endpoint" value="{{ request('endpoint') }}" placeholder="Search endpoint" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-white/70 mb-2">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
            </div>
            <div class="flex items-end">
                <x-button type="submit" variant="primary" size="md" class="w-full">Filter</x-button>
            </div>
        </form>
    </x-card>

    @if($activities->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">User</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Method</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Endpoint</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Response Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($activities as $activity)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-4 text-white/70 text-sm">
                                    <div>
                                        <p>{{ $activity->created_at->format('Y-m-d') }}</p>
                                        <p class="text-xs text-white/50">{{ $activity->created_at->format('H:i:s') }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div>
                                        <p class="text-white text-sm">{{ $activity->user->name ?? 'N/A' }}</p>
                                        <p class="text-white/60 text-xs">{{ $activity->user->email ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge variant="{{ $activity->method === 'GET' ? 'default' : ($activity->method === 'POST' ? 'success' : ($activity->method === 'PUT' ? 'warning' : 'error')) }}">
                                        {{ $activity->method }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-4">
                                    <code class="text-xs text-white/70 bg-[#1A1A1A] px-2 py-1 rounded">{{ $activity->endpoint }}</code>
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge variant="{{ $activity->status_code >= 200 && $activity->status_code < 300 ? 'success' : ($activity->status_code >= 400 ? 'error' : 'warning') }}">
                                        {{ $activity->status_code }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-4 text-white/70 text-sm">{{ $activity->response_time }}ms</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <p class="text-white/70 text-center py-12">No activity logs found</p>
        </x-card>
    @endif
</div>
@endsection

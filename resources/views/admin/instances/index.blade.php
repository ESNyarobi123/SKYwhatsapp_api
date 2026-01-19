@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Instances Management</h1>
        <p class="text-white/70">View all instances across all users</p>
    </div>

    @if($instances->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">User</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Instance Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Phone Number</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Last Connected</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($instances as $instance)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white">{{ $instance->user->name }}</td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $instance->name }}</td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $instance->phone_number ?? 'N/A' }}</td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $instance->status === 'connected' ? 'success' : ($instance->status === 'disconnected' ? 'error' : 'warning') }}">
                                    {{ ucfirst($instance->status) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $instance->last_connected_at ? $instance->last_connected_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.instances.show', $instance) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.instances.reset', $instance) }}" class="inline" onsubmit="return confirm('Are you sure you want to reset this instance session?')">
                                        @csrf
                                        <button type="submit" class="text-[#FFB800] hover:text-[#E5A700] text-sm transition-colors">Reset</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.instances.destroy', $instance) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this instance?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[#EA3943] hover:text-[#D1323A] text-sm transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $instances->links() }}
        </div>
    @else
        <x-card>
            <p class="text-white/70 text-center py-12">No instances found</p>
        </x-card>
    @endif
</div>
@endsection

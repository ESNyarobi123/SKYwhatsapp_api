@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Users Management</h1>
            <p class="text-white/70">Manage all platform users</p>
        </div>
        <a href="{{ route('admin.users.create') }}">
            <x-button variant="primary" size="md">Create User</x-button>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-[#00D9A5]/20 border border-[#00D9A5]/50 rounded-lg p-4 mb-6">
            <p class="text-[#00D9A5] text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Subscription</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Created</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($users as $user)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white">{{ $user->name }}</td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $user->role === 'admin' ? 'gold' : 'default' }}">
                                    {{ ucfirst($user->role) }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4">
                                @if($user->activeSubscription)
                                    <x-badge variant="success">Active</x-badge>
                                @else
                                    <x-badge variant="error">None</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-white/70 hover:text-white text-sm transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[#00D9A5] hover:text-[#00C595] text-sm transition-colors">Login</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
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
            {{ $users->links() }}
        </div>
    @else
        <x-card>
            <p class="text-white/70 text-center py-12">No users found</p>
        </x-card>
    @endif
</div>
@endsection

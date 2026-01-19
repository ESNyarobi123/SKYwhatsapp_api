@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">API Keys Management</h1>
        <p class="text-white/70">View all API keys across all users</p>
    </div>

    @if($apiKeys->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">User</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Key Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Key Preview</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Last Used</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-white/70">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($apiKeys as $apiKey)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4 text-white">{{ $apiKey->user->name }}</td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $apiKey->name }}</td>
                            <td class="px-4 py-4">
                                <code class="text-sm text-white/70 bg-[#1A1A1A] px-2 py-1 rounded">{{ $apiKey->key_preview }}</code>
                            </td>
                            <td class="px-4 py-4">
                                <x-badge variant="{{ $apiKey->is_active ? 'success' : 'error' }}">
                                    {{ $apiKey->is_active ? 'Active' : 'Revoked' }}
                                </x-badge>
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">
                                {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-4 py-4 text-white/70 text-sm">{{ $apiKey->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.api-keys.show', $apiKey) }}" class="text-[#FCD535] hover:text-[#F0C420] text-sm transition-colors">View</a>
                                    @if($apiKey->is_active)
                                        <form method="POST" action="{{ route('admin.api-keys.revoke', $apiKey) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[#EA3943] hover:text-[#D1323A] text-sm transition-colors">Revoke</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.api-keys.reactivate', $apiKey) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[#00D9A5] hover:text-[#00C595] text-sm transition-colors">Reactivate</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $apiKeys->links() }}
        </div>
    @else
        <x-card>
            <p class="text-white/70 text-center py-12">No API keys found</p>
        </x-card>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Edit User</h1>
        <p class="text-white/70">Update user information</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-[#EA3943]/20 border border-[#EA3943]/50 rounded-lg p-4">
                    <ul class="text-sm text-[#EA3943] space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-white/90 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-white/90 mb-2">New Password (Leave blank to keep current)</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-2">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-white/90 mb-2">Role</label>
                    <select id="role" name="role" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <x-button type="submit" variant="primary" size="md">Update User</x-button>
                <a href="{{ route('admin.users.index') }}">
                    <x-button type="button" variant="outline" size="md">Cancel</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection

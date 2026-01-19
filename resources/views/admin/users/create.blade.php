@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Create User</h1>
        <p class="text-white/70">Add a new user to the platform</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

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
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-white/90 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-white/90 mb-2">Password *</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-2">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-white/90 mb-2">Role</label>
                    <select id="role" name="role" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-[#FCD535]">
                        <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t border-white/5">
                <x-button type="submit" variant="primary" size="md">Create User</x-button>
                <a href="{{ route('admin.users.index') }}">
                    <x-button type="button" variant="outline" size="md">Cancel</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
@endsection

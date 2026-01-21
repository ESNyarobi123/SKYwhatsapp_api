@extends('layouts.landing')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#121212] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-[#1E1E1E] p-8 rounded-2xl border border-white/10 shadow-xl">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-[#00D9A5]/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-[#00D9A5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-white mb-2">Reset Password</h2>
            <p class="text-gray-400 text-sm">
                Enter your new password below.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-500">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-8 space-y-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-3 bg-[#2A2A2A] border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#00D9A5] focus:border-transparent transition-all" 
                            placeholder="••••••••" required autofocus>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-3 bg-[#2A2A2A] border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#00D9A5] focus:border-transparent transition-all" 
                            placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="mt-6 group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-[#1A1A1A] bg-[#00D9A5] hover:bg-[#00C495] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00D9A5] focus:ring-offset-[#121212] transition-all duration-200">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

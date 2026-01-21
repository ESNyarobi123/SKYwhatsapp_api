@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#121212] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-[#1E1E1E] p-8 rounded-2xl border border-white/10 shadow-xl">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-[#00D9A5]/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-[#00D9A5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-white mb-2">Enter Reset Code</h2>
            <p class="text-gray-400 text-sm">
                We've sent a 6-digit code to <span class="text-white font-medium">{{ $email }}</span>
            </p>
        </div>

        @if (session('status'))
            <div class="bg-[#00D9A5]/10 border border-[#00D9A5]/20 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-[#00D9A5]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-[#00D9A5]">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

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
            <form method="POST" action="{{ route('password.code.verify') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-gray-300 mb-2">Reset Code</label>
                    <input type="text" name="code" id="code" maxlength="6" pattern="[0-9]{6}" 
                        class="w-full px-4 py-3 bg-[#2A2A2A] border border-white/10 rounded-xl text-white text-center text-2xl tracking-widest placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#00D9A5] focus:border-transparent transition-all" 
                        placeholder="000000" required autofocus>
                </div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-[#1A1A1A] bg-[#00D9A5] hover:bg-[#00C495] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00D9A5] focus:ring-offset-[#121212] transition-all duration-200">
                    Verify Code
                </button>
            </form>

            <div class="text-center">
                <p class="text-gray-400 text-sm mb-4">Didn't receive the code?</p>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="text-[#00D9A5] hover:text-[#00C495] font-medium transition-colors">
                        Resend Code
                    </button>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('password.forgot') }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                    ← Use Different Email
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

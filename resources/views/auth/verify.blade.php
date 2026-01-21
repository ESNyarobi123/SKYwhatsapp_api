@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#121212] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-[#1E1E1E] p-8 rounded-2xl border border-white/10 shadow-xl">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-[#00D9A5]/10 rounded-full flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-[#00D9A5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-white mb-2">Verify your email</h2>
            <p class="text-gray-400 text-sm">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="bg-[#00D9A5]/10 border border-[#00D9A5]/20 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-[#00D9A5]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-[#00D9A5]">
                            A new verification link has been sent to the email address you provided during registration.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-8 space-y-6">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-[#1A1A1A] bg-[#00D9A5] hover:bg-[#00C495] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00D9A5] focus:ring-offset-[#121212] transition-all duration-200">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-white transition-colors">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

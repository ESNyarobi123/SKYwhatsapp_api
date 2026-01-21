@extends('layouts.landing')

@section('content')
<div class="min-h-screen flex items-center justify-center pt-20 md:pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="text-center text-3xl font-bold text-white mb-2">Sign in to your account</h2>
            <p class="text-center text-white/70">Access your Orange API dashboard</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6 bg-[#252525] border border-white/5 rounded-lg p-8">
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

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-white/90 mb-2">Email address</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent"
                        placeholder="Enter your email"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-white/90 mb-2">Password</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent"
                        placeholder="Enter your password"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-[#FCD535] focus:ring-[#FCD535] border-white/20 rounded bg-[#1A1A1A]"
                        >
                        <label for="remember" class="ml-2 block text-sm text-white/70">Remember me</label>
                    </div>

                    <a href="{{ route('password.forgot') }}" class="text-sm text-[#FCD535] hover:text-[#F0C420] transition-colors">
                        Forgot password?
                    </a>
                </div>
            </div>

            <div>
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Sign in
                </x-button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white/70">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-[#FCD535] hover:text-[#F0C420] font-medium transition-colors">
                        Sign up
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

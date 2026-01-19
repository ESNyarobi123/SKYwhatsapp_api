@extends('layouts.landing')

@section('content')
<div class="min-h-screen flex items-center justify-center pt-20 md:pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="text-center text-3xl font-bold text-white mb-2">Create your account</h2>
            <p class="text-center text-white/70">Start using Orange API today</p>
        </div>

        @if(isset($package))
            <div class="bg-[#FCD535]/10 border border-[#FCD535]/30 rounded-lg p-4 mb-6">
                <p class="text-[#FCD535] text-sm font-medium mb-1">Selected Plan: {{ $package->name }}</p>
                <p class="text-white/70 text-xs">
                    @if($package->isFree())
                        Free Trial - {{ $package->duration_days }} days
                    @else
                        {{ number_format($package->price, 2) }} {{ $package->currency }} - {{ $package->duration_days }} days
                    @endif
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-6 bg-[#252525] border border-white/5 rounded-lg p-8">
            @csrf
            
            @if(isset($package))
                <input type="hidden" name="package_id" value="{{ $package->id }}">
            @endif

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
                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Full name</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        required 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent"
                        placeholder="Enter your full name"
                    >
                </div>

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
                        placeholder="Create a password"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-2">Confirm password</label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent"
                        placeholder="Confirm your password"
                    >
                </div>
            </div>

            <div>
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Create account
                </x-button>
            </div>

            <div class="text-center">
                <p class="text-sm text-white/70">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-[#FCD535] hover:text-[#F0C420] font-medium transition-colors">
                        Sign in
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

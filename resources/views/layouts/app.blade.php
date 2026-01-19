<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Orange API') }} - {{ $title ?? 'Dashboard' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#1A1A1A] text-white min-h-screen">
    <div class="flex min-h-screen relative">
        @include('components.sidebar')

        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300">
            {{-- Dashboard Toggle & Avatar (no header) --}}
            @if(auth()->check() && (request()->routeIs('dashboard*') || request()->routeIs('admin.*')))
                <div class="fixed top-0 right-0 z-50 flex items-center space-x-3 p-3 md:p-4">
                    <button id="sidebar-toggle" type="button" class="p-2 rounded-lg text-white/70 hover:text-[#FCD535] hover:bg-white/5 transition-colors" aria-label="Toggle sidebar">
                        <svg id="sidebar-toggle-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    @if(!request()->routeIs('admin.*'))
                        {{-- Notification Icon --}}
                        <x-notification-icon :count="auth()->user()->unreadNotificationsCount()" />
                    @endif
                    
                    {{-- User Avatar Dropdown --}}
                    <div class="relative" id="user-menu-container">
                        <button id="user-menu-button" type="button" class="flex items-center space-x-2 p-1.5 rounded-full hover:bg-white/5 transition-colors" aria-label="User menu">
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-[#FCD535]/20 flex items-center justify-center border-2 border-[#FCD535]/30">
                                <span class="text-[#FCD535] font-semibold text-sm md:text-base">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                        </button>
                        
                        {{-- Dropdown Menu --}}
                        <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-[#252525] border border-white/10 rounded-lg shadow-xl py-1 z-50 transition-all duration-200">
                            <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-white/70 hover:bg-white/5 hover:text-white transition-colors">
                                Update Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-white/70 hover:bg-white/5 hover:text-[#EA3943] transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                @include('components.navbar')
            @endif

            <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto {{ (auth()->check() && (request()->routeIs('dashboard*') || request()->routeIs('admin.*'))) ? '' : 'pt-16 md:pt-20' }}">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

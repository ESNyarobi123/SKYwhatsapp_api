@props(['showAuth' => true])

<nav class="fixed top-0 left-0 right-0 z-50 bg-transparent backdrop-blur-md border-b border-white/5 px-3 md:px-6 py-2 md:py-3 transition-all duration-300" id="main-navbar">
    <div class="flex items-center justify-between max-w-7xl mx-auto">
        <div class="flex items-center space-x-6">
            @if(!request()->routeIs('landing') && !request()->routeIs('login') && !request()->routeIs('register'))
                <button id="sidebar-toggle" type="button" class="p-2 rounded-lg text-white/70 hover:text-[#FCD535] hover:bg-white/5 transition-colors" aria-label="Toggle sidebar">
                    <svg id="sidebar-toggle-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            @endif
            @if(request()->routeIs('landing') || request()->routeIs('login') || request()->routeIs('register'))
                <a href="{{ route('landing') }}" class="flex items-center">
                    <img src="{{ asset('logo.png') }}" alt="Orange API" class="h-8 md:h-12 lg:h-14 object-contain" style="background: transparent; mix-blend-mode: normal;">
                </a>
                
                {{-- Navigation Menu for Landing/Login/Register Pages --}}
                <nav class="hidden md:flex items-center space-x-2 ml-4">
                    <a href="#features" class="text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 whitespace-nowrap">
                        Features
                    </a>
                    <a href="#pricing" class="text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 whitespace-nowrap">
                        Pricing
                    </a>
                    <a href="#subscription" class="text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 whitespace-nowrap">
                        Subscription
                    </a>
                    <a href="#contact" class="text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 whitespace-nowrap">
                        Contact
                    </a>
                    <a href="#about" class="text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 whitespace-nowrap">
                        About Us
                    </a>
                    <a href="{{ route('dashboard.documentation') }}" class="text-sm text-[#FCD535] hover:text-[#F0C420] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 whitespace-nowrap flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Documentation
                    </a>
                </nav>
                
                {{-- Mobile Menu Toggle --}}
                <button id="mobile-menu-toggle" type="button" class="md:hidden p-2 rounded-lg text-white/70 hover:text-[#FCD535] hover:bg-white/5 transition-colors ml-auto" aria-label="Toggle mobile menu">
                    <svg id="mobile-menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            @endif
        </div>

        @if($showAuth)
            <div class="flex items-center space-x-3 md:space-x-4">
                @auth
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
                @else
                    {{-- Guest User Menu --}}
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <a href="{{ route('login') }}" class="text-xs md:text-sm text-white/70 hover:text-white transition-colors font-medium px-2 py-1.5 md:px-3 md:py-2 rounded-lg hover:bg-white/5">
                            Log in
                        </a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-2.5 py-1.5 md:px-4 md:py-2 bg-gradient-to-r from-[#FCD535] to-[#F0C420] text-[#1A1A1A] font-semibold rounded-lg hover:from-[#F0C420] hover:to-[#FCD535] transition-all duration-200 shadow-lg hover:shadow-[#FCD535]/20 text-xs md:text-sm">
                                Sign Up
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
        @endif
    </div>
    
    {{-- Mobile Menu for Landing/Login/Register Pages --}}
    @if(request()->routeIs('landing') || request()->routeIs('login') || request()->routeIs('register'))
    <div id="mobile-menu" class="hidden md:hidden border-t border-white/5 bg-[#252525]">
        <div class="px-4 py-3 space-y-1">
            <a href="#features" class="block text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5">
                Features
            </a>
            <a href="#pricing" class="block text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5">
                Pricing
            </a>
            <a href="#subscription" class="block text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5">
                Subscription
            </a>
            <a href="#contact" class="block text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5">
                Contact
            </a>
            <a href="#about" class="block text-sm text-white/70 hover:text-[#FCD535] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5">
                About Us
            </a>
            <a href="{{ route('dashboard.documentation') }}" class="block text-sm text-[#FCD535] hover:text-[#F0C420] transition-colors font-medium px-3 py-2 rounded-lg hover:bg-white/5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Documentation
            </a>
        </div>
    </div>
    @endif
</nav>


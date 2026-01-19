@extends('layouts.landing')

@section('content')
<div class="min-h-screen bg-[#1A1A1A] pt-20 md:pt-24">
    <!-- Hero Section with Amazing Background -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Video Background -->
        <video 
            autoplay 
            loop 
            muted 
            playsinline 
            class="absolute inset-0 w-full h-full object-cover z-0"
            aria-label="Orange API Services Video"
            preload="auto"
        >
            <source src="{{ asset('videos/main-horizontal-en-opt.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <!-- Video Overlay for Text Readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#1A1A1A]/85 via-[#1A1A1A]/75 to-[#1A1A1A]/85 z-10"></div>
        
        <!-- Background Layers (Fallback) -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#1A1A1A] via-[#0F0F0F] to-[#1A1A1A] z-1 opacity-0"></div>
        
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-10 z-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(252,213,53,0.3) 1px, transparent 0); background-size: 50px 50px;"></div>
        </div>
        
        <!-- Animated Gradient Orbs (Subtle over video) -->
        <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-[#FCD535]/10 rounded-full blur-[120px] animate-pulse-slow z-10"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-[#00D9A5]/5 rounded-full blur-[100px] animate-pulse-slow delay-2000 z-10"></div>
        
        <!-- Grid Pattern Overlay (Subtle) -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.01)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.01)_1px,transparent_1px)] bg-[size:50px_50px] z-10 opacity-50"></div>
        
        <!-- Main Content -->
        <div class="relative z-20 container mx-auto px-6 py-20 lg:py-32">
            <div class="text-center max-w-6xl mx-auto">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 bg-[#FCD535]/10 border border-[#FCD535]/20 rounded-full backdrop-blur-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#00D9A5] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#00D9A5]"></span>
                    </span>
                    <span class="text-sm text-[#FCD535] font-medium">Powered by AI Intelligence</span>
                </div>
                
                <!-- Main Heading -->
                <h1 class="text-5xl md:text-6xl lg:text-8xl xl:text-9xl font-extrabold mb-8 leading-[1.1] tracking-tight">
                    <span class="block mb-2 bg-gradient-to-r from-[#FCD535] via-[#F0C420] to-[#FCD535] bg-clip-text text-transparent animate-gradient">
                        Orange API
                    </span>
                    <span class="block text-white/90">API Platform</span>
                </h1>
                
                <!-- Subheading -->
                <p class="text-2xl md:text-3xl lg:text-4xl text-white/90 mb-6 font-semibold max-w-4xl mx-auto leading-relaxed">
                    Transform Your Business Communication with 
                    <span class="text-[#FCD535]">AI-Powered</span> WhatsApp Integration
                </p>
                
                <!-- Description -->
                <p class="text-lg md:text-xl text-white/70 mb-12 max-w-3xl mx-auto leading-relaxed">
                    Connect, automate, and scale your customer communication. Send messages, receive notifications, 
                    and leverage AI intelligence to deliver exceptional customer experiences through WhatsApp.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-20">
                    <a href="{{ route('register') }}" class="group relative">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-[#FCD535] to-[#F0C420] rounded-lg blur opacity-50 group-hover:opacity-75 transition duration-300"></div>
                        <x-button variant="primary" size="lg" class="relative w-full sm:w-auto">
                            <span class="flex items-center gap-2">
                                Get Started Free
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                        </x-button>
                    </a>
                    <a href="#features" class="group">
                        <x-button variant="secondary" size="lg" class="w-full sm:w-auto">
                            <span class="flex items-center gap-2">
                                Explore Features
                                <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </x-button>
                    </a>
                </div>

                <!-- Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div class="group relative p-6 bg-[#252525]/50 backdrop-blur-sm border border-white/5 rounded-xl hover:border-[#FCD535]/30 transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FCD535]/5 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <p class="text-5xl font-bold text-[#FCD535] mb-2">100%</p>
                            <p class="text-white/70 font-medium">Uptime Guarantee</p>
                            <p class="text-white/50 text-sm mt-1">Always available</p>
                        </div>
                    </div>
                    <div class="group relative p-6 bg-[#252525]/50 backdrop-blur-sm border border-white/5 rounded-xl hover:border-[#FCD535]/30 transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FCD535]/5 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <p class="text-5xl font-bold text-[#FCD535] mb-2">99.9%</p>
                            <p class="text-white/70 font-medium">Reliability</p>
                            <p class="text-white/50 text-sm mt-1">Enterprise-grade</p>
                        </div>
                    </div>
                    <div class="group relative p-6 bg-[#252525]/50 backdrop-blur-sm border border-white/5 rounded-xl hover:border-[#FCD535]/30 transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FCD535]/5 to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <p class="text-5xl font-bold text-[#FCD535] mb-2">24/7</p>
                            <p class="text-white/70 font-medium">Support</p>
                            <p class="text-white/50 text-sm mt-1">Expert assistance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce z-20">
            <a href="#features" class="flex flex-col items-center gap-2 text-white/50 hover:text-[#FCD535] transition-colors">
                <span class="text-sm">Scroll to explore</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative container mx-auto px-6 py-20 lg:py-32">
        <!-- Background Decoration -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#252525]/20 to-transparent"></div>
        
        <div class="relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white">
                    Powerful <span class="text-[#FCD535]">Features</span>
                </h2>
                <p class="text-xl md:text-2xl text-white/70 max-w-3xl mx-auto leading-relaxed">
                    Everything you need to integrate WhatsApp into your business with AI-powered intelligence
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature cards -->
                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Real-time Messaging</h3>
                        <p class="text-white/70">Send and receive WhatsApp messages in real-time through our robust API.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Webhook Integration</h3>
                        <p class="text-white/70">Get instant notifications via webhooks for incoming messages and status updates.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Secure & Reliable</h3>
                        <p class="text-white/70">Enterprise-grade security with encrypted sessions and secure API keys.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Usage Analytics</h3>
                        <p class="text-white/70">Track your API usage, message statistics, and performance metrics in real-time.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Easy Integration</h3>
                        <p class="text-white/70">Simple REST API that integrates seamlessly with your existing applications.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Multiple Instances</h3>
                        <p class="text-white/70">Manage multiple WhatsApp numbers from a single dashboard.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">AI-Powered Responses</h3>
                        <p class="text-white/70">Smart AI assistant that automatically responds to customer inquiries with context awareness.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Bulk Messaging</h3>
                        <p class="text-white/70">Send messages to thousands of contacts simultaneously with delivery tracking.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Template Messages</h3>
                        <p class="text-white/70">Create and send pre-approved message templates for marketing and notifications.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Media Attachments</h3>
                        <p class="text-white/70">Send images, videos, documents, and audio files through WhatsApp messages.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Message Scheduling</h3>
                        <p class="text-white/70">Schedule messages to be sent at specific times and dates automatically.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Multi-language Support</h3>
                        <p class="text-white/70">Send messages in multiple languages with automatic translation support.</p>
                    </div>
                </x-card>

                <x-card hover>
                    <div class="mb-4">
                        <div class="w-12 h-12 bg-[#FCD535]/10 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Platform Integrations</h3>
                        <p class="text-white/70">Integrate with popular platforms like CRM, e-commerce, and customer support tools.</p>
                    </div>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Character Image Section (Below Features) -->
    <section class="relative container mx-auto px-6 py-16 lg:py-24">
        <!-- Background Decoration -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#252525]/10 to-transparent"></div>
        
        <div class="relative z-10 flex justify-center items-center">
            <div class="w-full max-w-5xl mx-auto">
                <div class="relative">
                    <!-- Subtle glow effect behind character -->
                    <div class="absolute inset-0 bg-[#FCD535]/10 rounded-full blur-[80px] opacity-50 -z-10"></div>
                    <img src="{{ asset('character.png') }}" alt="Orange API Character" class="w-full h-auto object-contain animate-float mx-auto" style="filter: drop-shadow(0 0 60px rgba(252,213,53,0.5)); max-height: 700px;">
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="relative container mx-auto px-6 py-20 lg:py-32">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white">
                How It <span class="text-[#FCD535]">Works</span>
            </h2>
            <p class="text-xl text-white/70 max-w-2xl mx-auto">Get started in minutes with our simple 4-step process</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#FCD535] to-[#F0C420] rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative p-8 bg-[#252525] border border-white/5 rounded-2xl h-full">
                    <div class="w-16 h-16 bg-[#FCD535]/10 rounded-xl flex items-center justify-center mb-6">
                        <span class="text-3xl font-bold text-[#FCD535]">1</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Sign Up</h3>
                    <p class="text-white/70">Create your account and get instant access to the dashboard</p>
                </div>
            </div>

            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#FCD535] to-[#F0C420] rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative p-8 bg-[#252525] border border-white/5 rounded-2xl h-full">
                    <div class="w-16 h-16 bg-[#FCD535]/10 rounded-xl flex items-center justify-center mb-6">
                        <span class="text-3xl font-bold text-[#FCD535]">2</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Create Instance</h3>
                    <p class="text-white/70">Set up your WhatsApp instance and generate API keys</p>
                </div>
            </div>

            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#FCD535] to-[#F0C420] rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative p-8 bg-[#252525] border border-white/5 rounded-2xl h-full">
                    <div class="w-16 h-16 bg-[#FCD535]/10 rounded-xl flex items-center justify-center mb-6">
                        <span class="text-3xl font-bold text-[#FCD535]">3</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Scan QR Code</h3>
                    <p class="text-white/70">Connect your WhatsApp by scanning the QR code</p>
                </div>
            </div>

            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#FCD535] to-[#F0C420] rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                <div class="relative p-8 bg-[#252525] border border-white/5 rounded-2xl h-full">
                    <div class="w-16 h-16 bg-[#FCD535]/10 rounded-xl flex items-center justify-center mb-6">
                        <span class="text-3xl font-bold text-[#FCD535]">4</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Start Building</h3>
                    <p class="text-white/70">Begin sending messages and integrating with your apps</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    @if(isset($packages) && $packages && count($packages) > 0)
    <section id="pricing" class="relative container mx-auto px-6 py-20 lg:py-32">
        <!-- Background Decoration -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#252525]/20 to-transparent"></div>
        
        <div class="relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white">
                    Choose Your <span class="text-[#FCD535]">Plan</span>
                </h2>
                <p class="text-xl md:text-2xl text-white/70 max-w-3xl mx-auto leading-relaxed">
                    Select the perfect package for your business needs. Start with our free trial or choose a paid plan for more features.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
                @foreach($packages as $package)
                    <x-card hover class="relative {{ $package->isFree() ? 'border-[#FCD535]/50 ring-2 ring-[#FCD535]/20' : '' }}">
                        @if($package->isFree())
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 bg-[#FCD535]/10 text-[#FCD535] text-xs font-semibold rounded-full border border-[#FCD535]/30">FREE TRIAL</span>
                            </div>
                        @endif
                        
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ $package->name }}</h3>
                            @if($package->description)
                                <p class="text-white/70 text-sm mb-4">{{ $package->description }}</p>
                            @endif
                            <div class="mb-4">
                                <span class="text-4xl font-bold text-[#FCD535]">
                                    {{ $package->isFree() ? 'Free' : number_format($package->price, 2) }}
                                </span>
                                @if(!$package->isFree())
                                    <span class="text-white/60 text-lg ml-2">{{ $package->currency }}</span>
                                @endif
                                <p class="text-white/60 text-xs mt-1">Per {{ $package->duration_days }} {{ $package->duration_days == 1 ? 'day' : 'days' }}</p>
                            </div>
                        </div>

                        @if($package->features && count($package->features) > 0)
                            <ul class="space-y-3 mb-8">
                                @php
                                    $features = $package->features ?? [];
                                    $isOldFormat = is_array($features) && isset($features[0]) && is_string($features[0]);
                                @endphp
                                
                                @if($isOldFormat)
                                    @foreach($features as $feature)
                                        <li class="text-white/70 text-sm flex items-start">
                                            <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    @if(isset($features['instances']))
                                        <li class="text-white/70 text-sm flex items-start">
                                            <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                @if($features['instances']['limit'] == -1)
                                                    Unlimited Instances
                                                @else
                                                    {{ $features['instances']['limit'] }} Instance{{ $features['instances']['limit'] != 1 ? 's' : '' }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if(isset($features['messages']))
                                        <li class="text-white/70 text-sm flex items-start">
                                            <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                @if($features['messages']['limit'] == -1)
                                                    Unlimited Messages
                                                @else
                                                    {{ number_format($features['messages']['limit']) }} Messages
                                                @endif
                                                @if($features['messages']['period'] != 'lifetime')
                                                    / {{ ucfirst($features['messages']['period']) }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if(isset($features['api_calls']))
                                        <li class="text-white/70 text-sm flex items-start">
                                            <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                @if($features['api_calls']['limit'] == -1)
                                                    Unlimited API Calls
                                                @else
                                                    {{ number_format($features['api_calls']['limit']) }} API Calls
                                                @endif
                                                @if($features['api_calls']['period'] != 'lifetime')
                                                    / {{ ucfirst($features['api_calls']['period']) }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    @if(isset($features['api_keys']))
                                        <li class="text-white/70 text-sm flex items-start">
                                            <svg class="w-5 h-5 text-[#FCD535] mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                @if($features['api_keys']['limit'] == -1)
                                                    Unlimited API Keys
                                                @else
                                                    {{ $features['api_keys']['limit'] }} API Key{{ $features['api_keys']['limit'] != 1 ? 's' : '' }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        @endif

                        <div class="pt-6 border-t border-white/5">
                            @auth
                                @if(auth()->user()->hasActiveSubscription() && auth()->user()->activeSubscription->package_id == $package->id)
                                    <x-button variant="outline" size="md" class="w-full" disabled>
                                        Current Plan
                                    </x-button>
                                @else
                                    <a href="{{ route('register', ['package_id' => $package->id]) }}">
                                        <x-button variant="{{ $package->isFree() ? 'primary' : 'secondary' }}" size="md" class="w-full">
                                            {{ $package->isFree() ? 'Start Free Trial' : 'Choose Plan' }}
                                        </x-button>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register', ['package_id' => $package->id]) }}">
                                    <x-button variant="{{ $package->isFree() ? 'primary' : 'secondary' }}" size="md" class="w-full">
                                        {{ $package->isFree() ? 'Start Free Trial' : 'Choose Plan' }}
                                    </x-button>
                                </a>
                            @endauth
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Subscription Section (same as Pricing, just for anchor link) -->
    <section id="subscription" class="relative container mx-auto px-6 py-8">
        <div class="text-center">
            <a href="#pricing" class="text-[#FCD535] hover:text-[#F0C420] transition-colors text-sm font-medium">
                View our subscription plans →
            </a>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative container mx-auto px-6 py-20 lg:py-32">
        <div class="relative overflow-hidden bg-gradient-to-br from-[#252525] via-[#1A1A1A] to-[#252525] rounded-3xl border border-white/5 p-12 md:p-16 text-center max-w-5xl mx-auto">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(252,213,53,0.5) 1px, transparent 0); background-size: 40px 40px;"></div>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white">
                    Ready to Get <span class="text-[#FCD535]">Started?</span>
                </h2>
                <p class="text-xl text-white/70 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Join thousands of businesses using Orange API to enhance their customer communication and automate their workflows.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('register') }}">
                        <x-button variant="primary" size="lg" class="w-full sm:w-auto">
                            Start Free Trial
                        </x-button>
                    </a>
                    <a href="#features">
                        <x-button variant="secondary" size="lg" class="w-full sm:w-auto">
                            Learn More
                        </x-button>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="relative container mx-auto px-6 py-20 lg:py-32">
        <!-- Background Decoration -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#252525]/20 to-transparent"></div>
        
        <div class="relative z-10 max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white">
                    About <span class="text-[#FCD535]">Us</span>
                </h2>
                <p class="text-xl md:text-2xl text-white/70 max-w-3xl mx-auto leading-relaxed">
                    Empowering businesses with powerful WhatsApp API integration
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-4">Who We Are</h3>
                    <p class="text-white/70 mb-4 leading-relaxed">
                        Orange API is a leading provider of WhatsApp Business API solutions, designed to help businesses 
                        seamlessly integrate WhatsApp into their communication workflows. We combine cutting-edge technology 
                        with user-friendly interfaces to deliver exceptional customer experiences.
                    </p>
                    <p class="text-white/70 mb-4 leading-relaxed">
                        Our platform enables businesses of all sizes to send messages, receive notifications, automate 
                        customer interactions, and scale their communication efforts through the power of WhatsApp.
                    </p>
                    <div class="flex flex-wrap gap-4 mt-6">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-white/70 text-sm">AI-Powered Solutions</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-white/70 text-sm">24/7 Support</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-white/70 text-sm">Enterprise Grade</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <x-card class="text-center">
                        <div class="text-4xl font-bold text-[#FCD535] mb-2">500+</div>
                        <div class="text-white/70 text-sm">Active Businesses</div>
                    </x-card>
                    <x-card class="text-center">
                        <div class="text-4xl font-bold text-[#FCD535] mb-2">10M+</div>
                        <div class="text-white/70 text-sm">Messages Sent</div>
                    </x-card>
                    <x-card class="text-center">
                        <div class="text-4xl font-bold text-[#FCD535] mb-2">99.9%</div>
                        <div class="text-white/70 text-sm">Uptime SLA</div>
                    </x-card>
                    <x-card class="text-center">
                        <div class="text-4xl font-bold text-[#FCD535] mb-2">24/7</div>
                        <div class="text-white/70 text-sm">Support Available</div>
                    </x-card>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="relative container mx-auto px-6 py-20 lg:py-32">
        <!-- Background Decoration -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#252525]/20 to-transparent"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white">
                    Get in <span class="text-[#FCD535]">Touch</span>
                </h2>
                <p class="text-xl md:text-2xl text-white/70 max-w-3xl mx-auto leading-relaxed">
                    Have questions? We're here to help. Reach out to our team and we'll respond as soon as possible.
                </p>
            </div>
            
            <x-card class="p-8 md:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Contact Information</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-[#FCD535]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm mb-1">Email</p>
                                    <a href="mailto:support@skywhatsapp.com" class="text-[#FCD535] hover:text-[#F0C420] transition-colors">support@skywhatsapp.com</a>
                                </div>
                            </div>
                            @php
                                $whatsappNumber = \App\Models\Setting::getValue('whatsapp_number', '+255000000000');
                                $whatsappNumberClean = preg_replace('/[^0-9]/', '', $whatsappNumber);
                            @endphp
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-[#FCD535]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#FCD535]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm mb-1">WhatsApp</p>
                                    <a href="https://wa.me/{{ $whatsappNumberClean }}" target="_blank" class="text-[#FCD535] hover:text-[#F0C420] transition-colors">{{ $whatsappNumber }}</a>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-[#FCD535]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#FCD535]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm mb-1">Location</p>
                                    <p class="text-white">Dar es Salaam, Tanzania</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Send Us a Message</h3>
                        <form class="space-y-4">
                            <div>
                                <label for="contact_name" class="block text-sm font-medium text-white/90 mb-2">Name</label>
                                <input type="text" id="contact_name" name="name" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent" placeholder="Your name">
                            </div>
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-white/90 mb-2">Email</label>
                                <input type="email" id="contact_email" name="email" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent" placeholder="your@email.com">
                            </div>
                            <div>
                                <label for="contact_message" class="block text-sm font-medium text-white/90 mb-2">Message</label>
                                <textarea id="contact_message" name="message" rows="4" class="w-full px-4 py-3 bg-[#1A1A1A] border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[#FCD535] focus:border-transparent" placeholder="Your message"></textarea>
                            </div>
                            <div>
                                <x-button type="submit" variant="primary" size="md" class="w-full">
                                    Send Message
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12">
        <div class="container mx-auto px-6">
            <div class="text-center text-white/50">
                <p>&copy; {{ date('Y') }} Orange API. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const navbar = document.getElementById('main-navbar');
    
    // Mobile menu toggle
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking on a link
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        });
    }
    
    // Navbar scroll effect - enhance shadow on scroll
    if (navbar) {
        let lastScroll = 0;
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 50) {
                navbar.classList.add('shadow-2xl');
                navbar.classList.remove('shadow-xl');
            } else {
                navbar.classList.add('shadow-xl');
                navbar.classList.remove('shadow-2xl');
            }
            
            lastScroll = currentScroll;
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offsetTop = target.offsetTop - 80; // Account for fixed navbar
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
@keyframes gradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.4;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.animate-pulse-slow {
    animation: pulse-slow 8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.delay-2000 {
    animation-delay: 2s;
}

.delay-4000 {
    animation-delay: 4s;
}

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Glow effect on hover */
.group:hover .glow {
    box-shadow: 0 0 30px rgba(252, 213, 53, 0.3);
}
</style>
@endpush
@endsection

@php
$currentRoute = request()->route()->getName();
$isAdmin = auth()->check() && auth()->user()->isAdmin();

// Define which groups should be open based on current route
$messagingRoutes = ['dashboard.instances', 'dashboard.messages', 'dashboard.bot.index', 'dashboard.templates.index', 'dashboard.scheduler.index'];
$automationRoutes = ['dashboard.bot-templates.index', 'dashboard.webhooks', 'dashboard.webhook-logs.index'];
$developerRoutes = ['dashboard.api-keys', 'dashboard.analytics', 'dashboard.usage', 'dashboard.documentation'];
$accountRoutes = ['dashboard.team.index', 'dashboard.notifications.index', 'dashboard.support.index', 'dashboard.orders', 'dashboard.packages', 'dashboard.settings'];

$messagingOpen = collect($messagingRoutes)->contains(fn($r) => str_starts_with($currentRoute, str_replace('.index', '', $r)));
$automationOpen = collect($automationRoutes)->contains(fn($r) => str_starts_with($currentRoute, str_replace('.index', '', $r)));
$developerOpen = collect($developerRoutes)->contains(fn($r) => str_starts_with($currentRoute, str_replace('.index', '', $r)));
$accountOpen = collect($accountRoutes)->contains(fn($r) => str_starts_with($currentRoute, str_replace('.index', '', $r)));

// Admin Groups
$adminUserRoutes = ['admin.users', 'admin.packages', 'admin.subscriptions'];
$adminSystemRoutes = ['admin.instances', 'admin.api-keys', 'admin.activity'];
$adminFinanceRoutes = ['admin.revenue'];
$adminSupportRoutes = ['admin.notifications', 'admin.support'];

$adminUserOpen = collect($adminUserRoutes)->contains(fn($r) => str_starts_with($currentRoute, $r));
$adminSystemOpen = collect($adminSystemRoutes)->contains(fn($r) => str_starts_with($currentRoute, $r));
$adminFinanceOpen = collect($adminFinanceRoutes)->contains(fn($r) => str_starts_with($currentRoute, $r));
$adminSupportOpen = collect($adminSupportRoutes)->contains(fn($r) => str_starts_with($currentRoute, $r));
@endphp

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 bg-[#252525] border-r border-white/5 min-h-screen w-64 md:w-64 transition-all duration-300 ease-in-out transform -translate-x-full md:translate-x-0 overflow-y-auto">
    <div class="p-6 pb-32 md:pb-6">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 mb-8 sidebar-item">
            <img src="{{ asset('logo.png') }}" alt="Orange API" class="h-12 object-contain" style="background: transparent; mix-blend-mode: normal;">
        </a>

        <nav class="space-y-1">
            @if($isAdmin)
                {{-- Admin menu stays the same --}}
                <a href="{{ route('admin.dashboard') }}" class="group sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ $currentRoute === 'admin.dashboard' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Dashboard">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-label">Admin Dashboard</span>
                </a>

                {{-- User Management Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $adminUserOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="sidebar-label font-medium">User Management</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.users.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Users</span>
                        </a>
                        <a href="{{ route('admin.packages.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.packages') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.packages') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Packages</span>
                        </a>
                        <a href="{{ route('admin.subscriptions.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.subscriptions') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.subscriptions') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Subscriptions</span>
                        </a>
                    </div>
                </div>

                {{-- System Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $adminSystemOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                            <span class="sidebar-label font-medium">System</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.instances.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.instances') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.instances') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Instances</span>
                        </a>
                        <a href="{{ route('admin.api-keys.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.api-keys') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.api-keys') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>API Keys</span>
                        </a>
                        <a href="{{ route('admin.activity.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.activity') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.activity') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Activity Logs</span>
                        </a>
                    </div>
                </div>

                {{-- Finance Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $adminFinanceOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="sidebar-label font-medium">Finance</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.revenue.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.revenue') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.revenue') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Revenue</span>
                        </a>
                    </div>
                </div>

                {{-- Support Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $adminSupportOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="sidebar-label font-medium">Support</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('admin.notifications.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.notifications') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.notifications') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Notifications</span>
                        </a>
                        <a href="{{ route('admin.support.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'admin.support') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'admin.support') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Support Tickets</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.settings') }}" class="group sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ $currentRoute === 'admin.settings' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Settings">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-label">Settings</span>
                </a>
            @else
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Dashboard">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                {{-- Messaging Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $messagingOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span class="sidebar-label font-medium">Messaging</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('dashboard.instances') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.instances' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.instances' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Instances</span>
                        </a>
                        <a href="{{ route('dashboard.messages') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.messages' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.messages' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Messages</span>
                        </a>
                        <a href="{{ route('dashboard.bot.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.bot') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.bot') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Bot Builder</span>
                        </a>
                        <a href="{{ route('dashboard.templates.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.templates') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.templates') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Templates</span>
                        </a>
                        <a href="{{ route('dashboard.scheduler.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.scheduler') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.scheduler') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Scheduler</span>
                        </a>
                    </div>
                </div>

                {{-- Automation Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $automationOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="sidebar-label font-medium">Automation</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('dashboard.bot-templates.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.bot-templates') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.bot-templates') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Bot Templates</span>
                        </a>
                        <a href="{{ route('dashboard.webhooks') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.webhooks' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.webhooks' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Webhooks</span>
                        </a>
                        <a href="{{ route('dashboard.webhook-logs.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.webhook-logs') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.webhook-logs') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Webhook Logs</span>
                        </a>
                    </div>
                </div>

                {{-- Developer Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $developerOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                            <span class="sidebar-label font-medium">Developer</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('dashboard.api-keys') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.api-keys' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.api-keys' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>API Keys</span>
                        </a>
                        <a href="{{ route('dashboard.analytics') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.analytics' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.analytics' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Analytics</span>
                        </a>
                        <a href="{{ route('dashboard.usage') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.usage' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.usage' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Usage</span>
                        </a>
                        <a href="{{ route('dashboard.documentation') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.documentation' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.documentation' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>API Docs</span>
                        </a>
                    </div>
                </div>

                {{-- Account Group --}}
                <div class="sidebar-group" x-data="{ open: {{ $accountOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full group flex items-center justify-between px-4 py-3 rounded-lg transition-colors text-white/70 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="sidebar-label font-medium">Account</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('dashboard.team.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.team') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.team') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Team</span>
                        </a>
                        <a href="{{ route('dashboard.notifications.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.notifications') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.notifications') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Notifications</span>
                        </a>
                        <a href="{{ route('dashboard.support.index') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ str_starts_with($currentRoute, 'dashboard.support') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_starts_with($currentRoute, 'dashboard.support') ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Support</span>
                        </a>
                        <a href="{{ route('dashboard.orders') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.orders' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.orders' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('dashboard.packages') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.packages' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.packages' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Plans</span>
                        </a>
                        <a href="{{ route('dashboard.settings') }}" class="group flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $currentRoute === 'dashboard.settings' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentRoute === 'dashboard.settings' ? 'bg-[#FCD535]' : 'bg-white/30' }}"></span>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </div>

    @auth
        <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-white/5 sidebar-user-info bg-[#252525]">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-[#FCD535]/20 flex items-center justify-center">
                    <span class="text-[#FCD535] font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-white/50 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    @endauth
</aside>

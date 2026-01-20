@php
$currentRoute = request()->route()->getName();
$isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 bg-[#252525] border-r border-white/5 min-h-screen w-64 md:w-64 transition-all duration-300 ease-in-out transform -translate-x-full md:translate-x-0">
    <div class="p-6 pb-32 md:pb-6">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 mb-8 sidebar-item">
            <img src="{{ asset('logo.png') }}" alt="Orange API" class="h-12 object-contain" style="background: transparent; mix-blend-mode: normal;">
        </a>

        <nav class="space-y-2">
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="group sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Dashboard">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Users">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-label">Users</span>
                </a>

                <a href="{{ route('admin.packages.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.packages') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Packages">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="sidebar-label">Packages</span>
                </a>

                <a href="{{ route('admin.subscriptions.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.subscriptions') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Subscriptions">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="sidebar-label">Subscriptions</span>
                </a>

                <a href="{{ route('admin.api-keys.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.api-keys') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="API Keys">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="sidebar-label">API Keys</span>
                </a>

                <a href="{{ route('admin.instances.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.instances') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Instances">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span class="sidebar-label">Instances</span>
                </a>

                <a href="{{ route('admin.revenue.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.revenue') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Revenue">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="sidebar-label">Revenue</span>
                </a>

                <a href="{{ route('admin.activity.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.activity') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Activity Logs">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="sidebar-label">Activity Logs</span>
                </a>

                <a href="{{ route('admin.analytics') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'admin.analytics' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Analytics">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-label">Analytics</span>
                </a>

                <a href="{{ route('admin.support.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.support') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Support Tickets">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-label">Support Tickets</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.notifications') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Send Notifications">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="sidebar-label">Send Notifications</span>
                </a>

                <a href="{{ route('admin.settings') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'admin.settings') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Settings">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-label">Settings</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Dashboard">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                <a href="{{ route('dashboard.instances') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.instances' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Instances">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span class="sidebar-label">Instances</span>
                </a>

                <a href="{{ route('dashboard.messages') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.messages' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Messages">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    <span class="sidebar-label">Messages</span>
                </a>

                <a href="{{ route('dashboard.bot.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'dashboard.bot') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Bot Builder">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span class="sidebar-label">Bot Builder</span>
                </a>

                <a href="{{ route('dashboard.api-keys') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.api-keys' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="API Keys">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="sidebar-label">API Keys</span>
                </a>

                <a href="{{ route('dashboard.webhooks') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.webhooks' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Webhooks">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="sidebar-label">Webhooks</span>
                </a>

                <a href="{{ route('dashboard.notifications.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'dashboard.notifications') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Notifications">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="sidebar-label">Notifications</span>
                </a>

                <a href="{{ route('dashboard.support.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ str_starts_with($currentRoute, 'dashboard.support') ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Support">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-label">Support</span>
                </a>

                <a href="{{ route('dashboard.orders') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.orders' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Orders">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-label">Orders</span>
                </a>

                <a href="{{ route('dashboard.usage') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.usage' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Usage">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-label">Usage</span>
                </a>

                <a href="{{ route('dashboard.settings') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors sidebar-item {{ $currentRoute === 'dashboard.settings' ? 'bg-[#FCD535]/10 text-[#FCD535]' : 'text-white/70 hover:bg-white/5 hover:text-white' }}" title="Settings">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-label">Settings</span>
                </a>
            @endif
        </nav>
    </div>

    @auth
        <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-white/5 sidebar-user-info">
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

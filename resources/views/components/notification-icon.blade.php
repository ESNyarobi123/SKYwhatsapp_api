@props(['count' => 0])

<div class="relative" id="notification-container">
    <a href="{{ route('dashboard.notifications.index') }}" class="relative p-2 rounded-lg text-white/70 hover:text-[#FCD535] hover:bg-white/5 transition-colors" aria-label="Notifications">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($count > 0)
            <span id="notification-badge" class="absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs font-bold text-[#1A1A1A] bg-[#FCD535] rounded-full transform translate-x-1 -translate-y-1">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </a>
</div>

@push('scripts')
<script>
(function() {
    const updateNotificationCount = () => {
        fetch('{{ route("api.notifications.unread") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const count = data.data.count;
                const badge = document.getElementById('notification-badge');
                const container = document.getElementById('notification-container');
                
                if (count > 0) {
                    if (!badge) {
                        const link = container.querySelector('a');
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notification-badge';
                        newBadge.className = 'absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs font-bold text-[#1A1A1A] bg-[#FCD535] rounded-full transform translate-x-1 -translate-y-1';
                        newBadge.textContent = count > 99 ? '99+' : count;
                        link.appendChild(newBadge);
                    } else {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'flex';
                    }
                } else if (badge) {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching notification count:', error);
        });
    };

    // Update on page load
    updateNotificationCount();

    // Update every 30 seconds
    setInterval(updateNotificationCount, 30000);
})();
</script>
@endpush

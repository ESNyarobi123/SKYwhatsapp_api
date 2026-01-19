import './bootstrap';

// Copy to clipboard utility
window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy:', err);
        showToast('Failed to copy to clipboard', 'error');
    });
};

// Simple toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? '#00D9A5' : type === 'error' ? '#EA3943' : '#FCD535';
    toast.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg text-white shadow-lg z-50 transition-all duration-300';
    toast.style.backgroundColor = bgColor;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Make showToast available globally
window.showToast = showToast;

// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const mainContent = document.getElementById('main-content');
    const toggleIcon = document.getElementById('sidebar-toggle-icon');
    const isMobile = window.innerWidth < 768;

    if (isMobile) {
        // Mobile: toggle visibility (overlay)
        const isOpen = sidebar.classList.contains('translate-x-0');
        
        if (isOpen) {
            // Close sidebar
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            // Change icon to hamburger
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
        } else {
            // Open sidebar
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            // Change icon to X
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
        }
    } else {
        // Desktop: toggle collapsed state (icons-only)
        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
        
        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('sidebar-collapsed');
            mainContent.classList.remove('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
            // Change icon to hamburger
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
        } else {
            // Collapse sidebar
            sidebar.classList.add('sidebar-collapsed');
            mainContent.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');
            // Change icon to X (for visual consistency)
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
        }
    }
}

function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const overlay = document.getElementById('sidebar-overlay');
    const isMobile = window.innerWidth < 768;

    if (isMobile) {
        // Mobile: always hidden initially
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
    } else {
        // Desktop: restore state from localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            mainContent.classList.add('sidebar-collapsed');
            // Set icon to X
            const toggleIcon = document.getElementById('sidebar-toggle-icon');
            if (toggleIcon) {
                toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            }
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            mainContent.classList.remove('sidebar-collapsed');
        }
    }
}

function handleResize() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const isMobile = window.innerWidth < 768;

    const mainContent = document.getElementById('main-content');
    
    if (isMobile) {
        // Mobile: ensure sidebar is hidden and remove collapsed state
        sidebar.classList.remove('sidebar-collapsed');
        if (mainContent) mainContent.classList.remove('sidebar-collapsed');
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
        overlay.classList.add('opacity-0', 'pointer-events-none');
    } else {
        // Desktop: restore collapsed state from localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            if (mainContent) mainContent.classList.add('sidebar-collapsed');
        }
    }
}

// Form submission with fetch API (if needed)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar on page load
    initSidebar();

    // Handle sidebar toggle button
    const toggleButton = document.getElementById('sidebar-toggle');
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleSidebar);
    }

    // Handle overlay click (close sidebar on mobile)
    const overlay = document.getElementById('sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        });
    }

    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleResize, 250);
    });

    // Handle modals (close on outside click)
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                const closeFunc = modal.getAttribute('data-close-func');
                if (closeFunc && window[closeFunc]) {
                    window[closeFunc]();
                }
            }
        });
    });

    // User menu dropdown toggle
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');
    
    if (userMenuButton && userMenuDropdown) {
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('user-menu-container');
            if (container && !container.contains(e.target)) {
                userMenuDropdown.classList.add('hidden');
            }
        });
    }
});

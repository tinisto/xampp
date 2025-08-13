/**
 * Enhanced Dashboard JavaScript
 * Modern dashboard functionality for 11klassniki.ru
 */

document.addEventListener('DOMContentLoaded', function() {
    // Dashboard elements
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('dashboardSidebar');
    const overlay = document.getElementById('dashboardOverlay');
    const main = document.getElementById('dashboardMain');

    // Initialize dashboard
    initializeDashboard();

    // Sidebar functionality
    function initializeDashboard() {
        // Toggle sidebar
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Auto-open sidebar on larger screens
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);

        // Initialize tooltips
        initializeTooltips();

        // Initialize dashboard animations
        initializeAnimations();

        // Initialize counter animations
        animateCounters();
    }

    function toggleSidebar() {
        if (sidebar && overlay && main) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            main.classList.toggle('sidebar-open');
        }
    }

    function closeSidebar() {
        if (sidebar && overlay && main) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            main.classList.remove('sidebar-open');
        }
    }

    function checkScreenSize() {
        if (window.innerWidth >= 992) {
            if (sidebar && main && overlay) {
                sidebar.classList.add('active');
                main.classList.add('sidebar-open');
                overlay.classList.remove('active');
            }
        } else {
            if (sidebar && main && overlay) {
                sidebar.classList.remove('active');
                main.classList.remove('sidebar-open');
                overlay.classList.remove('active');
            }
        }
    }

    // Initialize tooltips (if Bootstrap is available)
    function initializeTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    // Dashboard card animations
    function initializeAnimations() {
        // Animate cards on load
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Hover effects for nav items
        const navItems = document.querySelectorAll('.dashboard-nav-item');
        navItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    }

    // Counter animation for numbers
    function animateCounters() {
        const numbers = document.querySelectorAll('.dashboard-card-number');
        
        numbers.forEach(number => {
            const target = parseInt(number.textContent.replace(/,/g, ''));
            const increment = target / 100;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                number.textContent = Math.floor(current).toLocaleString();
            }, 20);
        });
    }

    // Loading states for AJAX operations
    function showLoading(element) {
        if (element) {
            element.innerHTML = '<div class="dashboard-loading"><div class="dashboard-spinner"></div></div>';
        }
    }

    function hideLoading(element, originalContent) {
        if (element) {
            element.innerHTML = originalContent;
        }
    }

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} dashboard-notification`;
        notification.innerHTML = `
            <i class="fas fa-${getIconForType(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" aria-label="Close"></button>
        `;
        
        // Add to page
        const content = document.querySelector('.dashboard-content');
        if (content) {
            content.insertBefore(notification, content.firstChild);
        }

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);

        // Manual dismiss
        notification.querySelector('.btn-close').addEventListener('click', () => {
            notification.remove();
        });
    }

    function getIconForType(type) {
        const icons = {
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'danger': 'times-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Search functionality
    function initializeSearch() {
        const searchInput = document.getElementById('dashboardSearch');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(this.value);
                }, 300);
            });
        }
    }

    function performSearch(query) {
        // Implementation for dashboard search
        console.log('Searching for:', query);
    }

    // Export functions for external use
    window.DashboardJS = {
        showNotification,
        showLoading,
        hideLoading,
        toggleSidebar,
        closeSidebar
    };

    // Theme switcher (if dark mode is implemented)
    function initializeThemeSwitcher() {
        const themeSwitcher = document.getElementById('themeSwitcher');
        if (themeSwitcher) {
            themeSwitcher.addEventListener('click', function() {
                document.body.classList.toggle('dashboard-dark');
                localStorage.setItem('dashboardTheme', 
                    document.body.classList.contains('dashboard-dark') ? 'dark' : 'light'
                );
            });

            // Load saved theme
            const savedTheme = localStorage.getItem('dashboardTheme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dashboard-dark');
            }
        }
    }

    // Real-time updates (placeholder)
    function initializeRealTimeUpdates() {
        // This would connect to WebSockets or use polling for real-time data
        setInterval(() => {
            // updateStatistics();
        }, 30000); // Update every 30 seconds
    }

    // Initialize additional features
    initializeThemeSwitcher();
    initializeSearch();
    
    console.log('Dashboard JavaScript initialized successfully');
});

// Utility functions
function formatNumber(num) {
    return num.toLocaleString();
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
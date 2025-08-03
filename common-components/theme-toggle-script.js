// Dark Mode Toggle Script
(function() {
    // Make toggleTheme globally available
    window.toggleTheme = function() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeToggle(newTheme);
        
        // Update mobile menu text if exists
        const mobileThemeLink = document.querySelector('.nav-link[onclick*="toggleTheme"]');
        if (mobileThemeLink) {
            const icon = mobileThemeLink.querySelector('i');
            const text = mobileThemeLink.childNodes[1];
            if (newTheme === 'dark') {
                icon.className = 'fas fa-sun me-2';
                if (text) text.textContent = 'Светлая тема';
            } else {
                icon.className = 'fas fa-moon me-2';
                if (text) text.textContent = 'Темная тема';
            }
        }
    }
    
    function updateThemeToggle(theme) {
        // Update all theme toggle buttons
        const toggles = document.querySelectorAll('.theme-toggle, .theme-toggle-fixed');
        toggles.forEach(toggle => {
            const icon = toggle.querySelector('i');
            if (icon) {
                if (theme === 'dark') {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
            }
        });
    }
    
    // Initialize theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    
    // Wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        updateThemeToggle(savedTheme);
        
        // Add click handlers
        document.querySelectorAll('.theme-toggle, .theme-toggle-fixed').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.toggleTheme();
            });
        });
        
        // Event delegation
        document.addEventListener('click', function(e) {
            if (e.target.closest('.theme-toggle') || e.target.closest('.theme-toggle-fixed')) {
                e.preventDefault();
                e.stopPropagation();
                window.toggleTheme();
            }
        });
    }
})();
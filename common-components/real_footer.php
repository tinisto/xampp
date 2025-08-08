<?php
/**
 * Unified Footer Component - THE ONLY FOOTER
 * Replaces all other footer files
 */

// Favicon is now handled directly in template head section - no longer needed here

// Load session manager if not already loaded
if (!class_exists('SessionManager')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/SessionManager.php';
}

// Ensure session is started
SessionManager::start();

// Safe database connection check
$hasDatabase = false;
$connection = null;

// Try to establish database connection for categories
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if (!$connection->connect_error) {
            $connection->set_charset("utf8mb4");
            $hasDatabase = true;
        }
    }
} catch (Exception $e) {
    // Silently fail - categories dropdown will not show
    $hasDatabase = false;
}

// Load environment config for security
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
?>

<style>
    /* Unified Footer Styles - Modern, Clean, Responsive */
    :root {
        --primary-color: #28a745;
        --surface: white;
        --text-color: #333;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
        --border-color: #e9ecef;
    }

    [data-theme="dark"], [data-bs-theme="dark"] {
        --surface: #1f2937;
        --text-color: #f8f9fa;
        --border-color: #374151;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.3);
    }

    .footer {
        background: var(--surface);
        border-top: 1px solid var(--border-color);
        margin-top: auto;
        width: 100%;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    
    .footer-container {
        max-width: none; /* Remove width limit */
        margin: 0;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
        width: 100%;
    }
    
    
    .footer-nav {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        justify-content: flex-end;
        white-space: nowrap;
    }
    
    .nav-link {
        color: var(--text-color);
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        padding: 0 8px;
        transition: all 0.3s ease;
        position: relative;
        white-space: nowrap;
    }
    
    .nav-link:hover {
        color: #28a745;
    }
    
    /* Dropdown Styles */
    .dropdown {
        position: relative;
        display: inline-block;
        z-index: 10;
    }
    
    .dropdown-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    /* Make button dropdown toggles look like links */
    button.dropdown-toggle,
    button.nav-link {
        background: none;
        border: none;
        padding: 0 15px;
        font: inherit;
        font-size: 14px;
        color: var(--text-color);
        text-decoration: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        position: relative;
        z-index: 10;
        pointer-events: auto !important;
    }
    
    button.dropdown-toggle:hover,
    button.nav-link:hover {
        color: #28a745;
    }
    
    /* Remove Bootstrap's dropdown arrow */
    .dropdown-toggle::after {
        display: none !important;
    }
    
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        min-width: 240px; /* INCREASED for longer names */
        max-width: 320px; /* Max width for very long names */
        padding: 4px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1001;
        pointer-events: none;
    }
    
    .dropdown.show .dropdown-menu {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        display: block !important;
        pointer-events: auto !important;
    }
    
    .dropdown-item {
        display: block;
        padding: 10px 16px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.2s ease;
        border-radius: 0;
        cursor: pointer;
        pointer-events: auto !important;
        position: relative;
        z-index: 10000;
        font-size: 14px;
        white-space: normal; /* Allow text wrapping */
        word-wrap: break-word; /* Break long words */
        line-height: 1.5; /* Better spacing for wrapped text */
        max-width: 100%; /* Ensure it doesn't overflow */
    }
    
    .dropdown-item:hover {
        background: rgba(40, 167, 69, 0.1);
        color: var(--primary-color);
        padding-left: 25px;
    }
    
    /* Footer Copyright */
    .footer-copyright {
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        flex: 1;
        font-size: 14px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        color: var(--text-color);
    }
    
    .theme-toggle-btn {
        background: transparent;
        border: 2px solid var(--border-color);
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex !important;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        color: var(--text-color);
        transition: all 0.3s ease;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .theme-toggle-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: scale(1.05);
    }
    
    .user-menu {
        position: relative;
        z-index: 1002;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color) !important;
        color: white !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 18px;
        position: relative;
        z-index: 1;
        user-select: none;
        border: 2px solid var(--border-color);
    }
    
    .user-avatar.dropdown-toggle {
        cursor: pointer !important;
    }
    
    .user-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    }
    
    .user-menu .dropdown-menu {
        right: 0;
        left: auto;
        top: calc(100% + 5px);
    }
    
    .dropdown-divider {
        height: 0;
        margin: 8px 0;
        overflow: hidden;
        border-top: 1px solid var(--border-color);
    }
    
    /* Mobile Menu */
    .mobile-menu-toggle {
        display: none;
        background: transparent;
        border: none;
        font-size: 24px;
        color: var(--text-color);
        cursor: pointer;
    }
    
    /* Hide mobile-only login on desktop */
    .mobile-only-login {
        display: none !important;
    }
    
    /* Mobile Styles */
    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column;
            height: auto;
            padding: 12px 8px;
            gap: 8px;
        }
        
        .footer-copyright {
            justify-content: center;
            order: 2;
            font-size: 12px;
            text-align: center;
        }
        
        .footer-nav {
            justify-content: center;
            order: 1;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .nav-link {
            font-size: 12px;
            padding: 0 4px;
        }
    }
    
    @media (max-width: 480px) {
        .footer-container {
            padding: 8px 5px;
            gap: 6px;
        }
        
        .footer-nav {
            gap: 6px;
        }
        
        .nav-link {
            font-size: 11px;
            padding: 0 3px;
        }
        
        .footer-copyright {
            font-size: 10px;
        }
    }
    
    /* Tablet styles */
    @media (min-width: 481px) and (max-width: 768px) {
        .footer-container {
            padding: 15px 12px;
        }
        
        .nav-link {
            font-size: 13px;
            padding: 0 5px;
        }
        
        .footer-copyright {
            font-size: 13px;
        }
    }
</style>

<footer class="footer">
    <div class="footer-container">
        <!-- Left: Copyright -->
        <div class="footer-copyright">
            <span>&copy; <?= date('Y') ?> 11-классники. Все права защищены.</span>
        </div>
        
        <!-- Right: Navigation Links -->
        <nav class="footer-nav" id="footerNav">
            <a href="/about" class="nav-link">О проекте</a>
            <a href="/write" class="nav-link">Контакты</a>
            <a href="/privacy" class="nav-link">Конфиденциальность</a>
            <a href="/terms" class="nav-link">Условия</a>
        </nav>
    </div>
</footer>

<script>
// Dropdown functionality
function toggleDropdown(event, element) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const dropdown = element.closest('.dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Mobile menu functionality
function toggleMobileMenu() {
    const nav = document.getElementById('headerNav');
    const toggle = document.querySelector('.mobile-menu-toggle i');
    
    nav.classList.toggle('mobile-open');
    
    // Change icon based on menu state
    if (nav.classList.contains('mobile-open')) {
        toggle.className = 'fas fa-times';
    } else {
        toggle.className = 'fas fa-bars';
    }
}

// Simple click outside handler for both desktop and mobile
document.addEventListener('click', function(event) {
    // Close dropdowns if clicking outside - but don't interfere with Bootstrap
    if (!event.target.closest('.dropdown') && !event.target.closest('[data-bs-toggle="dropdown"]')) {
        // Let Bootstrap handle its own dropdowns
    }
    
    // Close mobile menu if clicking outside (on mobile)
    if (window.innerWidth <= 768) {
        const nav = document.getElementById('headerNav');
        const clickedInNav = event.target.closest('.header-nav');
        const clickedToggle = event.target.closest('.mobile-menu-toggle');
        
        if (!clickedInNav && !clickedToggle && nav.classList.contains('mobile-open')) {
            nav.classList.remove('mobile-open');
            const toggleIcon = document.querySelector('.mobile-menu-toggle i');
            if (toggleIcon) {
                toggleIcon.className = 'fas fa-bars';
            }
        }
    }
});

// Also handle escape key to close dropdowns
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu) {
                menu.style.cssText = '';
            }
        });
    }
});

// Removed duplicate click handler - handled above

// Theme toggle functionality (if not already defined)
if (typeof toggleTheme === 'undefined') {
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-bs-theme') || html.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-bs-theme', newTheme);
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('preferred-theme', newTheme);
        
        // Update theme icon
        const themeIcon = document.getElementById('theme-icon');
        if (themeIcon) {
            themeIcon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
    }
    
    // Initialize theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('preferred-theme') || 'light';
        const html = document.documentElement;
        const themeIcon = document.getElementById('theme-icon');
        
        html.setAttribute('data-bs-theme', savedTheme);
        html.setAttribute('data-theme', savedTheme);
        
        if (themeIcon) {
            themeIcon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
        
        // Add click handler to user avatar as fallback
        const userAvatar = document.querySelector('.user-avatar.dropdown-toggle');
        if (userAvatar && !userAvatar.hasAttribute('data-listener-added')) {
            userAvatar.setAttribute('data-listener-added', 'true');
            userAvatar.addEventListener('click', function(e) {
                toggleDropdown(e, this);
            });
        }
    });
}
</script>
<?php
// Statistics for admin/monitoring (if environment allows)
if (Environment::isDebug()) {
    echo "<!-- Header loaded: " . date('Y-m-d H:i:s') . " -->\n";
}
?>
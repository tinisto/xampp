<?php
/**
 * Unified Header Component - THE ONLY HEADER
 * Replaces all other header files
 */

// Favicon now handled directly in template head section - no longer needed here

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
    /* Unified Header Styles - Modern, Clean, Responsive */
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

    .header {
        background: var(--surface);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .header-container {
        max-width: none; /* Remove width limit */
        margin: 0;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
    }
    
    .header-brand {
        text-decoration: none;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    
    .header-brand:hover {
        text-decoration: none;
        transform: scale(1.02);
    }
    
    .header-nav {
        display: flex;
        align-items: center;
        gap: 30px;
        flex: 1;
        justify-content: center;
    }
    
    .nav-link {
        color: var(--text-color);
        text-decoration: none;
        font-weight: 500;
        font-size: 16px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        padding: 0 15px;
        transition: all 0.3s ease;
        position: relative;
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
        font-size: 16px;
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
    
    /* User Actions */
    .header-actions {
        display: flex !important;
        align-items: center;
        gap: 15px;
        flex-shrink: 0;
        position: relative;
        z-index: 10;
        visibility: visible !important;
        opacity: 1 !important;
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
        .header-container {
            padding: 0 12px;
            height: 60px;
        }
        
        .header-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--surface);
            border-top: 1px solid var(--border-color);
            flex-direction: row;
            flex-wrap: wrap;
            padding: 10px;
            gap: 6px;
            max-height: calc(100vh - 60px);
            overflow-y: auto;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }
        
        .header-nav.mobile-open {
            display: flex !important;
            flex-direction: row !important; /* Change from column to row */
            flex-wrap: wrap !important;
        }
        
        .mobile-menu-toggle {
            display: block;
        }
        
        /* Show mobile login on mobile */
        .mobile-only-login {
            display: inline-block !important;
        }
        
        /* Hide desktop login link on mobile */
        .header-actions > .nav-link {
            display: none !important;
        }
        
        .header-actions {
            gap: 10px;
        }
        
        .theme-toggle-btn {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }
        
        /* User avatar should be visible on mobile */
        .user-menu {
            display: block !important;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        
        /* Keep dropdown menu absolute positioned on mobile for user avatar */
        .user-menu .dropdown-menu {
            position: absolute;
            right: 0;
            left: auto;
            min-width: 160px;
            margin-top: 10px;
        }
        
        /* Hide dropdown menus on mobile by default */
        .header-nav.mobile-open .dropdown-menu {
            display: none !important;
        }
        
        /* Only show user menu dropdown when clicked */
        .user-menu.dropdown.show .dropdown-menu {
            display: block !important;
        }
        
        /* When categories dropdown is shown, display inline with other badges */
        .header-nav.mobile-open .dropdown.show .dropdown-menu {
            display: contents !important; /* Use contents to make items appear inline */
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            border: none;
            background: transparent;
            margin: 0;
            padding: 0;
            min-width: auto;
            max-width: 100%;
        }
        
        /* Mobile dropdown items with less spacing */
        .header-nav.mobile-open .dropdown-item {
            padding: 8px 20px; /* REDUCED vertical padding */
            font-size: 15px; /* Slightly smaller */
            line-height: 1.3; /* Tighter line height */
        }
        
        /* Style category items as badges on mobile with distinct color */
        .header-nav.mobile-open .dropdown-item {
            display: inline-block !important;
            background: #17a2b8 !important; /* Teal/cyan color for categories */
            color: white !important;
            padding: 8px 16px !important; /* Same padding as main nav badges */
            margin: 0 !important;
            border-radius: 20px !important; /* Same radius as main nav badges */
            font-size: 14px !important; /* Same size as main nav badges */
            font-weight: 500 !important;
            line-height: 1.2 !important;
            white-space: nowrap;
            width: auto !important;
            text-align: center;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            border: 1px solid #17a2b8 !important;
            flex: 0 0 auto; /* Prevent flex stretching */
            min-width: 60px !important; /* Ensure minimum width */
            overflow: visible !important; /* Ensure text is visible */
        }
        
        .header-nav.mobile-open .dropdown-item:hover {
            background: #138496 !important; /* Darker teal on hover */
            border-color: #138496 !important;
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Dark mode support for category badges */
        [data-theme="dark"] .header-nav.mobile-open .dropdown-item {
            background: #5dd4e8 !important; /* Light teal for dark mode */
            color: #1a202c !important;
            border-color: #5dd4e8 !important;
        }
        
        [data-theme="dark"] .header-nav.mobile-open .dropdown-item:hover {
            background: #4bb7c9 !important;
            border-color: #4bb7c9 !important;
        }
        
        /* Remove duplicate - already defined above */
        
        /* Make dropdown display inline */
        .header-nav.mobile-open .dropdown {
            display: contents !important; /* This makes dropdown children appear as direct children of nav */
        }
        
        /* Style ALL nav links as badges - both direct children and inside dropdowns */
        .header-nav.mobile-open .nav-link {
            display: inline-block !important;
            background: var(--surface-variant, #f0f4f8) !important;
            color: var(--text-primary, #333) !important;
            padding: 8px 16px !important;
            margin: 0 !important;
            border-radius: 20px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            border: 1px solid var(--border-color, #e0e0e0) !important;
            white-space: nowrap;
            width: auto !important;
            flex: 0 0 auto;
        }
        
        .header-nav.mobile-open .nav-link:hover {
            background: var(--primary-color, #28a745) !important;
            color: white !important;
            border-color: var(--primary-color, #28a745) !important;
            transform: scale(1.05);
        }
        
        /* Dark mode for nav badges */
        [data-theme="dark"] .header-nav.mobile-open .nav-link {
            background: var(--surface-variant, #2d3748) !important;
            color: var(--text-primary, #e4e6eb) !important;
            border-color: var(--border-color, #4a5568) !important;
        }
        
        [data-theme="dark"] .header-nav.mobile-open .nav-link:hover {
            background: var(--primary-color, #28a745) !important;
            color: white !important;
            border-color: var(--primary-color, #28a745) !important;
        }
    }
</style>

<header class="header">
    <div class="header-container">
        <!-- Brand -->
        <?php 
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/site-icon.php';
        renderSiteIcon('small', '/', 'header-brand-icon');
        ?>
        
        <!-- Navigation -->
        <nav class="header-nav" id="headerNav">
            
            <!-- Categories Dropdown -->
            <?php if ($hasDatabase): ?>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="nav-link dropdown-toggle" onclick="toggleDropdown(event, this)">
                        Категории
                    </a>
                    <div class="dropdown-menu">
                        <?php
                        try {
                            $hasCategories = false;
                            
                            // Get current page to exclude current category
                            $currentPath = $_SERVER['REQUEST_URI'];
                            $currentCategory = null;
                            
                            // Extract category from URL like /category/11-klassniki
                            if (preg_match('/\/category\/(.+?)(?:\/|$)/', $currentPath, $matches)) {
                                $currentCategory = $matches[1];
                            }
                            
                            // Show general categories only, excluding current category
                            $queryCategories = "SELECT url_category, title_category FROM categories ORDER BY title_category";
                            if ($connection && !$connection->connect_error) {
                                $resultCategories = mysqli_query($connection, $queryCategories);
                                
                                if ($resultCategories && mysqli_num_rows($resultCategories) > 0) {
                                    while ($category = mysqli_fetch_assoc($resultCategories)) {
                                        // Skip current category
                                        if ($currentCategory && $category['url_category'] === $currentCategory) {
                                            continue;
                                        }
                                        
                                        // Show shorter category name
                                        $shortTitle = trim($category['title_category']);
                                        
                                        // For "А напоследок я скажу" - no colon, so just use it as-is
                                        // Only truncate if it has a colon
                                        if (strpos($shortTitle, ':') !== false) {
                                            $parts = explode(':', $shortTitle);
                                            $shortTitle = trim($parts[0]);
                                        }
                                        
                                        // Truncate to 25 chars if needed
                                        if (strlen($shortTitle) > 25) {
                                            $shortTitle = mb_substr($shortTitle, 0, 25) . '...';
                                        }
                                        
                                        // Final safety - should never be empty now
                                        if (empty($shortTitle)) {
                                            $shortTitle = mb_substr($category['title_category'], 0, 25);
                                        }
                                        
                                        echo '<a href="/category/' . htmlspecialchars($category['url_category']) . '" class="dropdown-item" title="' . 
                                             htmlspecialchars($category['title_category']) . '">' . 
                                             htmlspecialchars($shortTitle) . '</a>';
                                        $hasCategories = true;
                                    }
                                }
                            }
                            
                            if (!$hasCategories) {
                                echo '<a href="#" class="dropdown-item disabled">Категории не найдены</a>';
                            }
                        } catch (Exception $e) {
                            echo '<a href="#" class="dropdown-item">Ошибка загрузки категорий</a>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <a href="/vpo-all-regions" class="nav-link">ВУЗы</a>
            <a href="/spo-all-regions" class="nav-link">ССУЗы</a>
            <a href="/schools-all-regions" class="nav-link">Школы</a>
            <a href="/news" class="nav-link">Новости</a>
            <a href="/tests" class="nav-link">Тесты</a>
            
            <!-- Login link for mobile (hidden on desktop) -->
            <?php if (!SessionManager::isLoggedIn()): ?>
                <a href="/login" class="nav-link mobile-only-login">Войти</a>
            <?php endif; ?>
        </nav>
        
        <!-- Actions -->
        <div class="header-actions">
            <!-- Theme Toggle -->
            <button class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Переключить тему">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            
            <!-- User Menu -->
            <?php if (SessionManager::isLoggedIn()): ?>
                <div class="dropdown user-menu">
                    <div class="user-avatar dropdown-toggle" onclick="toggleDropdown(event, this)">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="dropdown-menu">
                        <?php 
                        // Get current page URL
                        $current_url = $_SERVER['REQUEST_URI'];
                        // Only show My Account link if not already on account page
                        if ($current_url !== '/account' && $current_url !== '/account/'): 
                        ?>
                        <a href="/account" class="dropdown-item">
                            <i class="fas fa-user" style="margin-right: 10px; width: 16px;"></i>Мой аккаунт
                        </a>
                        <?php endif; ?>
                        <?php if (SessionManager::get('role') === 'admin'): ?>
                        <a href="/dashboard" class="dropdown-item">
                            <i class="fas fa-tachometer-alt" style="margin-right: 10px; width: 16px;"></i>Dashboard
                        </a>
                        <?php endif; ?>
                        <?php 
                        // Only show divider if we have items above it
                        if (($current_url !== '/account' && $current_url !== '/account/') || SessionManager::get('role') === 'admin'): 
                        ?>
                        <hr class="dropdown-divider">
                        <?php endif; ?>
                        <a href="/logout" class="dropdown-item">
                            <i class="fas fa-sign-out-alt" style="margin-right: 10px; width: 16px;"></i>Выйти
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="nav-link">Войти</a>
            <?php endif; ?>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Меню">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

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
    // Close dropdowns if clicking outside
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
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

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const nav = document.getElementById('headerNav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    const toggleIcon = document.querySelector('.mobile-menu-toggle i');
    
    if (!nav.contains(event.target) && !toggle.contains(event.target)) {
        nav.classList.remove('mobile-open');
        // Reset icon to hamburger when closing
        if (toggleIcon) {
            toggleIcon.className = 'fas fa-bars';
        }
    }
});

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
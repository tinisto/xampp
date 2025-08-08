<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safe database connection check
$hasDatabase = false;
if (isset($connection) && $connection && mysqli_ping($connection)) {
    $hasDatabase = true;
}
?>
<style>
    /* Unified Header Styles - Pure CSS, No Bootstrap */
    * {
        box-sizing: border-box;
    }
    
    .header {
        background: var(--surface, white);
        box-shadow: var(--shadow-sm, 0 2px 10px rgba(0,0,0,0.08));
        padding: 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        overflow: visible !important; /* DIAGNOSTIC: Allow dropdown to show */
    }
    
    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        width: 100%;
        justify-content: space-between;
        overflow: visible !important; /* DIAGNOSTIC: Allow dropdown to show */
    }
    
    .header-brand {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color, #28a745) !important;
        padding: 20px 0;
        text-decoration: none;
        flex-shrink: 0;
        margin-right: 20px;
    }
    
    .header-nav {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 0;
        align-items: center;
        flex: 1;
        justify-content: flex-start;
        overflow: visible !important; /* DIAGNOSTIC: Allow dropdown to show */
    }
    
    .nav-item {
        position: relative;
    }
    
    .nav-link {
        color: var(--text-primary, #333) !important;
        font-weight: 500;
        padding: 20px 10px !important;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        white-space: nowrap;
        font-size: 13px;
        cursor: pointer;
    }
    
    .nav-link:hover {
        color: var(--primary-color, #28a745) !important;
    }
    
    .dropdown {
        position: relative;
    }
    
    /* DIAGNOSTIC: NUCLEAR OPTION - Override EVERYTHING */
    #categories-menu.dropdown-menu {
        all: unset !important; /* Reset ALL CSS properties */
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        background: #ff0000 !important; /* RED BACKGROUND */
        border: 5px solid #000000 !important; /* BLACK BORDER */
        box-shadow: 0 0 30px 10px rgba(255,0,0,0.8) !important; /* RED GLOW */
        border-radius: 8px !important;
        padding: 20px !important;
        min-width: 300px !important;
        width: 300px !important;
        height: auto !important;
        min-height: 150px !important;
        z-index: 999999 !important;
        max-height: 400px !important;
        overflow-y: auto !important;
        display: none !important;
        transform: none !important;
        opacity: 1 !important;
        visibility: visible !important;
        clip: unset !important;
        clip-path: unset !important;
        mask: unset !important;
        filter: none !important;
        backdrop-filter: none !important;
        mix-blend-mode: normal !important;
        isolation: auto !important;
        contain: none !important;
        margin: 0 !important;
        outline: 3px solid yellow !important; /* YELLOW OUTLINE */
        font-family: Arial, sans-serif !important;
        font-size: 16px !important;
        color: white !important;
        text-align: left !important;
        list-style: none !important;
        box-sizing: border-box !important;
    }
    
    /* DIAGNOSTIC: FORCE SHOW - NUCLEAR OPTION */
    .dropdown.show #categories-menu.dropdown-menu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* BACKUP - Generic dropdown rule */
    .dropdown.show .dropdown-menu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    .dropdown-menu-end {
        left: auto;
        right: 0;
    }
    
    #categories-menu .dropdown-item {
        all: unset !important; /* Reset everything */
        display: block !important;
        padding: 15px 25px !important;
        color: #ffffff !important; /* WHITE TEXT */
        background: rgba(0,0,0,0.2) !important; /* SEMI-TRANSPARENT BLACK */
        text-decoration: none !important;
        font-weight: bold !important;
        font-size: 16px !important;
        font-family: Arial, sans-serif !important;
        border: 2px solid #ffffff !important; /* WHITE BORDER */
        margin: 5px !important;
        border-radius: 5px !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
        width: calc(100% - 10px) !important;
    }
    
    #categories-menu .dropdown-item:hover {
        background: #000000 !important; /* BLACK HOVER */
        color: #ffff00 !important; /* YELLOW TEXT ON HOVER */
        border-color: #ffff00 !important;
    }
    
    .auth-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    
    .btn-login {
        color: var(--primary-color, #28a745);
        border: 2px solid var(--primary-color, #28a745);
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }
    
    .btn-login:hover {
        background: var(--primary-color, #28a745);
        color: white;
    }
    
    .user-menu {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .theme-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        padding: 0;
        border-radius: 50%;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .theme-toggle-btn:hover {
        background-color: rgba(0,0,0,0.05);
    }
    
    [data-theme="dark"] .theme-toggle-btn:hover {
        background-color: rgba(255,255,255,0.1);
    }
    
    .theme-toggle-btn i {
        color: var(--text-primary, #333) !important;
        font-size: 20px;
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary-color, #28a745);
        cursor: pointer;
    }
    
    .user-avatar::after {
        display: none !important;
    }
    
    .mobile-toggle {
        display: none;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 10px;
        flex-shrink: 0;
    }
    
    .mobile-toggle span {
        display: block;
        width: 25px;
        height: 3px;
        background: var(--text-primary, #333);
        margin: 5px 0;
        transition: 0.3s;
    }
    
    /* Fix for auth section alignment */
    .auth-section {
        flex-shrink: 0;
    }
    
    /* DIAGNOSTIC CSS - Force overflow visible on ALL parent containers */
    body, html, main, .main-content, .header, .header-container, .header-nav, #categories-dropdown {
        overflow: visible !important;
    }
    
    #categories-dropdown {
        border: 2px solid blue !important;
        overflow: visible !important;
        z-index: 999999 !important;
    }
    
    .diagnostic-info {
        position: fixed;
        top: 100px;
        right: 10px;
        background: yellow;
        padding: 10px;
        border: 2px solid red;
        z-index: 10000;
        font-size: 12px;
        max-width: 300px;
    }
    
    @media (max-width: 991px) {
        .mobile-toggle {
            display: block;
        }
        
        .header-nav {
            position: fixed;
            top: 60px;
            left: -100%;
            width: 100%;
            height: calc(100vh - 60px);
            background: var(--surface, white);
            flex-direction: column;
            padding: 20px;
            transition: left 0.3s ease;
            overflow-y: auto;
        }
        
        .header-nav.active {
            left: 0;
        }
        
        .dropdown-menu {
            position: static;
            box-shadow: none;
            padding-left: 20px;
        }
    }
</style>

<div class="diagnostic-info" id="diagnostic-info">
    DIAGNOSTIC MODE ACTIVE<br>
    Categories dropdown should be RED when opened<br>
    <span id="debug-output"></span>
</div>

<header class="header">
    <div class="header-container">
        <a class="header-brand" href="/">11-классники</a>
        
        <button class="mobile-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <ul class="header-nav" id="headerNav">
            <!-- Categories Dropdown -->
            <li class="nav-item dropdown" id="categories-dropdown">
                <a class="nav-link" href="javascript:void(0)" onclick="toggleCategoriesDropdown(event)">
                    Категории [DIAGNOSTIC]
                </a>
                <ul class="dropdown-menu" id="categories-menu">
                    <?php
                    if ($hasDatabase) {
                        // Use the old category IDs as requested
                        $selectedCategoriesDropdown = [2, 3, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 18];
                        $categoryIds = implode(',', $selectedCategoriesDropdown);
                        $queryCategories = "SELECT * FROM category WHERE id IN ($categoryIds) AND status = 1 ORDER BY category";
                        $resultCategories = @mysqli_query($connection, $queryCategories);
                        if ($resultCategories && mysqli_num_rows($resultCategories) > 0) {
                            while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                                echo '<li><a class="dropdown-item" href="/category/' . $rowCategory['url_category'] . '">' . 
                                     htmlspecialchars($rowCategory['category']) . '</a></li>';
                            }
                        } else {
                            echo '<li><a class="dropdown-item" href="/category/ege">ЕГЭ</a></li>';
                            echo '<li><a class="dropdown-item" href="/category/oge">ОГЭ</a></li>';
                            echo '<li><a class="dropdown-item" href="/category/vpr">ВПР</a></li>';
                        }
                    } else {
                        // Default categories if no database
                        echo '<li><a class="dropdown-item" href="/category/ege">ЕГЭ [NO DB]</a></li>';
                        echo '<li><a class="dropdown-item" href="/category/oge">ОГЭ [NO DB]</a></li>';
                        echo '<li><a class="dropdown-item" href="/category/vpr">ВПР [NO DB]</a></li>';
                    }
                    ?>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/vpo-all-regions">ВУЗы</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/spo-all-regions">ССУЗы</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/schools-all-regions">Школы</a>
            </li>
            
            <!-- News Link -->
            <li class="nav-item">
                <a class="nav-link" href="/news">Новости</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/tests">Тесты</a>
            </li>
        </ul>
        
        <!-- Auth Section Outside Nav -->
        <div class="auth-section">
            <div class="auth-buttons">
                <?php if (isset($_SESSION['email'])): ?>
                    <div class="user-menu">
                        <!-- Theme Toggle Button -->
                        <button type="button" class="theme-toggle-btn" title="Переключить тему">
                            <i id="theme-icon-user" class="fas fa-moon"></i>
                        </button>
                        
                        <div class="dropdown">
                            <?php if (isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])): ?>
                                <img src="/<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Avatar" class="user-avatar" onclick="toggleDropdown(this); return false;">
                            <?php else: ?>
                                <div class="user-avatar" onclick="toggleDropdown(this); return false;" style="background: var(--primary-color, #28a745); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; cursor: pointer;">
                                    <?= strtoupper(substr($_SESSION['email'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/account">Личный кабинет</a></li>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="/dashboard">Админ панель</a></li>
                                <?php endif; ?>
                                <li><hr style="margin: 5px 0; border-top: 1px solid var(--border-color, #dee2e6);"></li>
                                <li><a class="dropdown-item" href="/logout">Выйти</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Theme Toggle Button for non-logged users -->
                    <button type="button" class="theme-toggle-btn" title="Переключить тему">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>
                    <a href="/login" class="btn-login">Войти</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
// Mobile menu toggle
function toggleMobileMenu() {
    const nav = document.getElementById('headerNav');
    nav.classList.toggle('active');
    updateDiagnostic('Mobile menu toggled');
}

// Categories dropdown toggle - works on both desktop and mobile
function toggleCategoriesDropdown(event) {
    console.log('toggleCategoriesDropdown called');
    updateDiagnostic('Categories clicked!');
    
    event.preventDefault();
    event.stopPropagation();
    
    const dropdown = document.getElementById('categories-dropdown');
    if (!dropdown) {
        console.error('Categories dropdown element not found!');
        updateDiagnostic('ERROR: dropdown element not found');
        return;
    }
    
    const isOpen = dropdown.classList.contains('show');
    console.log('Dropdown is currently open:', isOpen);
    updateDiagnostic('Was open: ' + isOpen);
    
    // Close all dropdowns first
    document.querySelectorAll('.dropdown.show').forEach(d => {
        d.classList.remove('show');
        console.log('Closed dropdown');
    });
    
    // Toggle this dropdown
    if (!isOpen) {
        dropdown.classList.add('show');
        console.log('Added show class to categories dropdown');
        updateDiagnostic('OPENED - should see RED dropdown now!');
    } else {
        updateDiagnostic('CLOSED');
    }
    
    // Double check with detailed positioning info and parent overflow
    setTimeout(() => {
        const menu = document.getElementById('categories-menu');
        if (menu) {
            const style = window.getComputedStyle(menu);
            const rect = menu.getBoundingClientRect();
            console.log('Menu display style:', style.display);
            console.log('Menu visibility:', style.visibility);
            console.log('Menu opacity:', style.opacity);
            console.log('Menu position:', style.position);
            console.log('Menu z-index:', style.zIndex);
            console.log('Menu top:', style.top);
            console.log('Menu left:', style.left);
            console.log('Menu width:', style.width);
            console.log('Menu height:', style.height);
            console.log('Menu bounding rect:', rect);
            console.log('Menu background:', style.backgroundColor);
            
            // Check parent overflow settings
            let parent = menu.parentElement;
            let level = 0;
            while (parent && level < 5) {
                const parentStyle = window.getComputedStyle(parent);
                console.log(`Parent ${level} (${parent.tagName}.${parent.className}):`, 
                    'overflow:', parentStyle.overflow, 
                    'overflowX:', parentStyle.overflowX, 
                    'overflowY:', parentStyle.overflowY);
                parent = parent.parentElement;
                level++;
            }
            
            updateDiagnostic('Pos: ' + rect.x + ',' + rect.y + ' Size: ' + rect.width + 'x' + rect.height + ' - CHECK CONSOLE FOR PARENT OVERFLOW!');
        }
    }, 100);
}

// User dropdown toggle function
function toggleDropdown(element) {
    const dropdown = element.closest('.dropdown');
    if (!dropdown) return;
    
    // Close other dropdowns
    document.querySelectorAll('.dropdown.show').forEach(d => {
        if (d !== dropdown) d.classList.remove('show');
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
        updateDiagnostic('Closed all dropdowns (clicked outside)');
    }
});

// Diagnostic helper
function updateDiagnostic(message) {
    const output = document.getElementById('debug-output');
    if (output) {
        output.innerHTML = new Date().toLocaleTimeString() + ': ' + message;
    }
}

// Setup theme toggle
document.addEventListener('DOMContentLoaded', function() {
    updateDiagnostic('Page loaded - ready for testing');
    
    // Update icons based on saved theme
    const savedTheme = localStorage.getItem('preferred-theme') || 'light';
    const themeIcon = document.getElementById('theme-icon');
    const themeIconUser = document.getElementById('theme-icon-user');
    const iconClass = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    
    if (themeIcon) themeIcon.className = iconClass;
    if (themeIconUser) themeIconUser.className = iconClass;
    
    // Add click handlers to theme toggle buttons
    document.querySelectorAll('.theme-toggle-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof window.toggleTheme === 'function') {
                window.toggleTheme();
            }
        });
    });
});
</script>
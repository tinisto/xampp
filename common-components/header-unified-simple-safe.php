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
        overflow: hidden;
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
    }
    
    .nav-link:hover {
        color: var(--primary-color, #28a745) !important;
    }
    
    .dropdown {
        position: relative;
    }
    
    .dropdown-toggle::after {
        content: '▼';
        font-size: 10px;
        margin-left: 5px;
    }
    
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--surface, white);
        border: none;
        box-shadow: var(--shadow-lg, 0 5px 20px rgba(0,0,0,0.1));
        border-radius: 8px;
        padding: 10px 0;
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        z-index: 1001;
        max-height: 400px;
        overflow-y: auto;
        transition: opacity 0.2s, visibility 0.2s;
    }
    
    .dropdown-menu-end {
        left: auto;
        right: 0;
    }
    
    .dropdown:hover .dropdown-menu,
    .dropdown.show .dropdown-menu {
        opacity: 1;
        visibility: visible;
    }
    
    .dropdown-item {
        padding: 10px 20px;
        display: block;
        color: var(--text-primary, #333);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: var(--primary-color, #28a745);
        color: white;
        padding-left: 25px;
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
    
    /* Prevent wrapping on medium screens */
    @media (max-width: 1200px) and (min-width: 992px) {
        .header-brand {
            font-size: 18px;
        }
        .nav-link {
            padding: 20px 8px !important;
            font-size: 12px;
        }
    }
    
    /* Desktop-specific fix for dropdowns */
    @media (min-width: 992px) {
        .dropdown {
            position: relative;
        }
        
        .dropdown-menu {
            position: absolute;
            opacity: 0;
            visibility: hidden;
        }
        
        .dropdown:hover .dropdown-menu,
        .dropdown.show .dropdown-menu {
            opacity: 1;
            visibility: visible;
        }
    }
    
    @media (max-width: 991px) {
        .header-container {
            flex-wrap: wrap;
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
        
        .mobile-toggle {
            display: block;
        }
        
        .nav-link {
            padding: 15px 20px !important;
        }
        
        .dropdown-menu {
            position: static;
            box-shadow: none;
            padding-left: 20px;
            opacity: 1;
            visibility: visible;
        }
        
        .auth-section {
            position: fixed;
            top: 60px;
            right: -100%;
            width: 250px;
            height: calc(100vh - 60px);
            background: var(--surface, white);
            padding: 20px;
            transition: right 0.3s ease;
            z-index: 999;
        }
        
        .auth-section.active {
            right: 0;
        }
        
        .auth-buttons {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }
        
        .btn-login {
            width: 100%;
            text-align: center;
        }
    }
    
    /* Dark mode styles */
    [data-theme="dark"] .header {
        background: var(--surface, #1e293b);
        box-shadow: var(--shadow-sm, 0 2px 10px rgba(0,0,0,0.2));
    }
    
    [data-theme="dark"] .dropdown-menu {
        background: var(--surface, #1e293b);
        box-shadow: var(--shadow-lg, 0 5px 20px rgba(0,0,0,0.3));
    }
    
    [data-theme="dark"] .dropdown-item:hover {
        background: var(--primary-color, #28a745);
        color: white;
    }
    
    [data-theme="dark"] .mobile-toggle span {
        background: var(--text-primary, #e4e6eb);
    }
</style>

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
                <a class="nav-link dropdown-toggle" href="#" id="categories-link">
                    Категории
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
                        echo '<li><a class="dropdown-item" href="/category/ege">ЕГЭ</a></li>';
                        echo '<li><a class="dropdown-item" href="/category/oge">ОГЭ</a></li>';
                        echo '<li><a class="dropdown-item" href="/category/vpr">ВПР</a></li>';
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
}

// Simple Categories dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    const categoriesLink = document.getElementById('categories-link');
    const categoriesDropdown = document.getElementById('categories-dropdown');
    
    if (categoriesLink && categoriesDropdown) {
        categoriesLink.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Categories clicked!');
            categoriesDropdown.classList.toggle('show');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#categories-dropdown')) {
            const dropdown = document.getElementById('categories-dropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }
    });
});

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

// Setup theme toggle
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Add click handlers to dropdown toggles
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Dropdown clicked via event listener');
            toggleDropdown(this);
        });
    });
});
</script>
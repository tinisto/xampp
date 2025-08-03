<?php
/**
 * Modern Header Component with CSS Variables
 * YouTube-style implementation
 */

// Include SessionManager if available
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/SessionManager.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/SessionManager.php';
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_first_name'] ?? '';
?>

<style>
    /* Header Styles using CSS Variables */
    .header-modern {
        background-color: var(--color-header-bg);
        border-bottom: 1px solid var(--color-border-primary);
        position: sticky;
        top: 0;
        z-index: 1000;
        transition: background-color var(--transition-normal);
    }
    
    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 60px;
    }
    
    /* Logo */
    .header-logo {
        display: flex;
        align-items: center;
        font-size: 24px;
        font-weight: 700;
        color: var(--color-text-primary);
        text-decoration: none;
        transition: opacity var(--transition-fast);
    }
    
    .header-logo:hover {
        opacity: 0.8;
    }
    
    /* Navigation */
    .header-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: 40px;
        flex: 1;
    }
    
    .header-nav-link {
        padding: 8px 16px;
        color: var(--color-text-secondary);
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all var(--transition-fast);
        position: relative;
    }
    
    .header-nav-link:hover {
        background-color: var(--color-bg-hover);
        color: var(--color-text-primary);
    }
    
    .header-nav-link.active {
        background-color: var(--color-bg-selected);
        color: var(--color-primary);
    }
    
    /* Dropdown */
    .header-dropdown {
        position: relative;
    }
    
    .header-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background-color: var(--color-surface-elevated);
        border: 1px solid var(--color-border-primary);
        border-radius: 12px;
        padding: 8px;
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all var(--transition-fast);
        box-shadow: 0 4px 12px var(--color-shadow-md);
    }
    
    .header-dropdown:hover .header-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .header-dropdown-item {
        display: block;
        padding: 8px 12px;
        color: var(--color-text-primary);
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        transition: background-color var(--transition-fast);
    }
    
    .header-dropdown-item:hover {
        background-color: var(--color-bg-hover);
    }
    
    /* Actions */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Search */
    .header-search {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .header-search-input {
        width: 240px;
        padding: 8px 36px 8px 16px;
        background-color: var(--color-surface-secondary);
        border: 1px solid var(--color-border-primary);
        border-radius: 20px;
        font-size: 14px;
        color: var(--color-text-primary);
        transition: all var(--transition-fast);
    }
    
    .header-search-input:focus {
        outline: none;
        background-color: var(--color-surface-primary);
        border-color: var(--color-primary);
        width: 320px;
    }
    
    .header-search-icon {
        position: absolute;
        right: 12px;
        color: var(--color-text-tertiary);
        pointer-events: none;
    }
    
    /* Theme Toggle */
    .theme-toggle-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background-color: transparent;
        color: var(--color-text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast);
    }
    
    .theme-toggle-btn:hover {
        background-color: var(--color-bg-hover);
        color: var(--color-text-primary);
    }
    
    /* User Menu */
    .user-menu-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background-color: transparent;
        border: 1px solid var(--color-border-primary);
        border-radius: 20px;
        color: var(--color-text-primary);
        cursor: pointer;
        transition: all var(--transition-fast);
    }
    
    .user-menu-btn:hover {
        background-color: var(--color-bg-hover);
        border-color: var(--color-border-secondary);
    }
    
    .user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: var(--color-primary);
        color: var(--color-text-inverse);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* Mobile Menu */
    .mobile-menu-toggle {
        display: none;
        width: 40px;
        height: 40px;
        border: none;
        background-color: transparent;
        color: var(--color-text-primary);
        cursor: pointer;
        border-radius: 8px;
        transition: all var(--transition-fast);
    }
    
    .mobile-menu-toggle:hover {
        background-color: var(--color-bg-hover);
    }
    
    /* Mobile Styles */
    @media (max-width: 768px) {
        .header-nav {
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            background-color: var(--color-surface-primary);
            border-bottom: 1px solid var(--color-border-primary);
            flex-direction: column;
            padding: 16px;
            gap: 4px;
            transform: translateY(-100%);
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-normal);
        }
        
        .header-nav.mobile-open {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }
        
        .header-search {
            display: none;
        }
        
        .mobile-menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-nav-link {
            width: 100%;
            text-align: left;
        }
    }
</style>

<header class="header-modern">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="header-logo">
            <i class="fas fa-graduation-cap" style="margin-right: 8px;"></i>
            11klassniki
        </a>
        
        <!-- Navigation -->
        <nav class="header-nav" id="headerNav">
            <a href="/news" class="header-nav-link">Новости</a>
            
            <div class="header-dropdown">
                <a href="#" class="header-nav-link">
                    Абитуриенту <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
                </a>
                <div class="header-dropdown-menu">
                    <a href="/vpo" class="header-dropdown-item">ВУЗы</a>
                    <a href="/spo" class="header-dropdown-item">Колледжи</a>
                    <a href="/schools" class="header-dropdown-item">Школы</a>
                </div>
            </div>
            
            <div class="header-dropdown">
                <a href="#" class="header-nav-link">
                    Учебные материалы <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
                </a>
                <div class="header-dropdown-menu">
                    <a href="/category/ege" class="header-dropdown-item">ЕГЭ</a>
                    <a href="/category/oge" class="header-dropdown-item">ОГЭ</a>
                    <a href="/category/vpr" class="header-dropdown-item">ВПР</a>
                    <a href="/category/olimpiady" class="header-dropdown-item">Олимпиады</a>
                </div>
            </div>
            
            <a href="/tests" class="header-nav-link">Тесты</a>
        </nav>
        
        <!-- Actions -->
        <div class="header-actions">
            <!-- Search -->
            <div class="header-search">
                <input type="text" class="header-search-input" placeholder="Поиск...">
                <i class="fas fa-search header-search-icon"></i>
            </div>
            
            <!-- Theme Toggle -->
            <button class="theme-toggle-btn" data-theme-toggle aria-label="Переключить тему">
                <i class="fas fa-moon"></i>
            </button>
            
            <!-- User Menu -->
            <?php if ($isLoggedIn): ?>
                <div class="header-dropdown">
                    <button class="user-menu-btn">
                        <div class="user-avatar"><?= mb_substr($userName, 0, 1) ?></div>
                        <span><?= htmlspecialchars($userName) ?></span>
                    </button>
                    <div class="header-dropdown-menu">
                        <a href="/account" class="header-dropdown-item">
                            <i class="fas fa-user" style="width: 20px;"></i> Профиль
                        </a>
                        <a href="/write" class="header-dropdown-item">
                            <i class="fas fa-pen" style="width: 20px;"></i> Написать статью
                        </a>
                        <a href="/logout" class="header-dropdown-item">
                            <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Выйти
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-primary">Войти</a>
            <?php endif; ?>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    const nav = document.getElementById('headerNav');
    const toggle = document.querySelector('.mobile-menu-toggle i');
    
    nav.classList.toggle('mobile-open');
    
    if (nav.classList.contains('mobile-open')) {
        toggle.className = 'fas fa-times';
    } else {
        toggle.className = 'fas fa-bars';
    }
}

// Update theme toggle icon on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const themeIcon = document.querySelector('.theme-toggle-btn i');
    if (themeIcon) {
        themeIcon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
});
</script>
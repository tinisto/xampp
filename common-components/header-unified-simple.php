<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    /* Unified Header Styles - Pure CSS, No Bootstrap */
    .unified-navbar {
        background: var(--surface, white);
        box-shadow: var(--shadow-sm, 0 2px 10px rgba(0,0,0,0.08));
        padding: 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    
    .navbar-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .navbar-brand {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color, #28a745) !important;
        padding: 20px 0;
        text-decoration: none;
    }
    
    .navbar-nav {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 10px;
        align-items: center;
    }
    
    .nav-item {
        position: relative;
    }
    
    .nav-link {
        color: var(--text-primary, #333) !important;
        font-weight: 500;
        padding: 25px 20px !important;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
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
        display: none;
        z-index: 1001;
    }
    
    .dropdown:hover .dropdown-menu {
        display: block;
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
        gap: 15px;
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
    }
    
    .btn-login:hover {
        background: var(--primary-color, #28a745);
        color: white;
    }
    
    .user-menu {
        display: flex;
        align-items: center;
        gap: 10px;
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
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary-color, #28a745);
        cursor: pointer;
    }
    
    .mobile-toggle {
        display: none;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 10px;
    }
    
    .mobile-toggle span {
        display: block;
        width: 25px;
        height: 3px;
        background: var(--text-primary, #333);
        margin: 5px 0;
        transition: 0.3s;
    }
    
    @media (max-width: 991px) {
        .navbar-nav {
            position: fixed;
            top: 60px;
            left: -100%;
            width: 100%;
            height: calc(100vh - 60px);
            background: var(--surface, white);
            flex-direction: column;
            padding: 20px;
            transition: left 0.3s ease;
        }
        
        .navbar-nav.active {
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
</style>

<nav class="unified-navbar">
    <div class="navbar-container">
        <a class="navbar-brand" href="/">11-классники</a>
        
        <button class="mobile-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a class="nav-link" href="/">Главная</a>
            </li>
            
            <!-- Categories Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" onclick="return false;">
                    Категории
                </a>
                <ul class="dropdown-menu">
                    <?php
                    if (isset($connection) && $connection) {
                        $queryCategories = "SELECT * FROM category WHERE status = 1 ORDER BY category";
                        $resultCategories = @mysqli_query($connection, $queryCategories);
                        if ($resultCategories) {
                            while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                                echo '<li><a class="dropdown-item" href="/category/' . $rowCategory['url_category'] . '">' . 
                                     htmlspecialchars($rowCategory['category']) . '</a></li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/vpo">ВУЗы</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/spo">ССУЗы</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/schools-all-regions">Школы</a>
            </li>
            
            <!-- News Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" onclick="return false;">
                    Новости
                </a>
                <ul class="dropdown-menu">
                    <?php
                    if (isset($connection) && $connection) {
                        $queryCategoriesNews = "SELECT * FROM category_news WHERE status = 1 ORDER BY category_news";
                        $resultCategoriesNews = @mysqli_query($connection, $queryCategoriesNews);
                        if ($resultCategoriesNews) {
                            while ($rowCategoryNews = mysqli_fetch_assoc($resultCategoriesNews)) {
                                echo '<li><a class="dropdown-item" href="/news/' . $rowCategoryNews['url_category_news'] . '">' . 
                                     htmlspecialchars($rowCategoryNews['category_news']) . '</a></li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/tests">Тесты</a>
            </li>
            
            <li class="nav-item auth-section">
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['email'])): ?>
                        <div class="user-menu">
                            <!-- Theme Toggle Button -->
                            <button type="button" class="theme-toggle-btn" title="Переключить тему">
                                <i id="theme-icon-user" class="fas fa-moon"></i>
                            </button>
                            
                            <?php if (isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])): ?>
                                <img src="/<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Avatar" class="user-avatar dropdown-toggle">
                            <?php else: ?>
                                <div class="user-avatar dropdown-toggle" style="background: var(--primary-color, #28a745); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    <?= strtoupper(substr($_SESSION['email'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="#" style="color: var(--text-primary, #333); text-decoration: none;">
                                    <?= htmlspecialchars($_SESSION['email']) ?>
                                </a>
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
            </li>
        </ul>
    </div>
</nav>

<script>
// Mobile menu toggle
function toggleMobileMenu() {
    const nav = document.getElementById('navbarNav');
    nav.classList.toggle('active');
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
});
</script>
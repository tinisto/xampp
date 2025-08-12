<?php
// Modern header with new logo design
// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/logo.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? '11klassniki.ru - Российское образование'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#0039A6">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f7fa;
        }
        
        /* Header Styles */
        .header {
            background: white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        
        /* Logo Styles */
        .logo {
            font-size: 28px;
            position: relative;
            display: inline-block;
            font-family: Arial, sans-serif;
            font-weight: 400;
            color: #333;
            text-decoration: none;
        }
        
        .logo:hover {
            opacity: 0.9;
        }
        
        .logo .eleven {
            font-weight: 700;
            color: #0039A6;
        }
        
        .logo .ru {
            color: #D52B1E;
            font-weight: 500;
        }
        
        .logo svg {
            position: absolute;
            bottom: -5px;
            left: -3px;
            width: 45px;
            height: 15px;
        }
        
        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .nav a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav a:hover,
        .nav a.active {
            color: #0039A6;
        }
        
        .nav a.active::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 3px;
            background: #0039A6;
            border-radius: 3px 3px 0 0;
        }
        
        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-menu a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .user-menu a:hover {
            color: #0039A6;
        }
        
        .btn-primary {
            background: #0039A6;
            color: white !important;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background: #002D87;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 57, 166, 0.2);
        }
        
        /* Mobile Menu Toggle */
        .mobile-toggle {
            display: none;
            font-size: 24px;
            color: #555;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .mobile-toggle:hover {
            color: #0039A6;
        }
        
        /* Theme Toggle */
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            color: #555;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .theme-toggle:hover {
            color: #0039A6;
        }
        
        /* Dark Mode Styles */
        body.dark-mode {
            background: #1a1a1a;
            color: #e0e0e0;
        }
        
        body.dark-mode .header {
            background: #2d2d2d;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        body.dark-mode .logo {
            color: #e0e0e0;
        }
        
        body.dark-mode .nav a {
            color: #b0b0b0;
        }
        
        body.dark-mode .nav a:hover,
        body.dark-mode .nav a.active {
            color: #4299e1;
        }
        
        body.dark-mode .user-menu a {
            color: #b0b0b0;
        }
        
        body.dark-mode .user-menu a:hover {
            color: #4299e1;
        }
        
        body.dark-mode .btn-primary {
            background: #4299e1;
        }
        
        body.dark-mode .btn-primary:hover {
            background: #3182ce;
        }
        
        body.dark-mode .mobile-toggle {
            color: #b0b0b0;
        }
        
        body.dark-mode .mobile-toggle:hover {
            color: #4299e1;
        }
        
        body.dark-mode .theme-toggle {
            color: #b0b0b0;
        }
        
        body.dark-mode .theme-toggle:hover {
            color: #4299e1;
        }
        
        body.dark-mode .mobile-nav {
            background: #2d2d2d;
        }
        
        body.dark-mode .mobile-nav a {
            color: #b0b0b0;
            border-bottom-color: #444;
        }
        
        body.dark-mode .mobile-nav a:hover {
            color: #4299e1;
        }
        
        /* SPO admission section dark mode fix */
        body.dark-mode div[style*="background: #e8f5e9"] {
            background: #2d4a2f !important;
            color: #e0e0e0 !important;
        }
        
        body.dark-mode div[style*="background: #e8f5e9"] h4 {
            color: #68d391 !important;
        }
        
        body.dark-mode div[style*="background: #e8f5e9"] ul,
        body.dark-mode div[style*="background: #e8f5e9"] li {
            color: #b0b0b0 !important;
        }
        
        /* Dark Mode Main Content */
        body.dark-mode .main-content {
            background: #1a1a1a;
            color: #e0e0e0;
        }
        
        /* Dark Mode Footer */
        body.dark-mode .footer {
            background: #1a1a1a !important;
        }
        
        /* Dark Mode Cards and Sections */
        body.dark-mode div[style*="background: white"],
        body.dark-mode div[style*="background:#f8f9fa"],
        body.dark-mode div[style*="background: #f8f9fa"],
        body.dark-mode [style*="background: white"],
        body.dark-mode [style*="background:#f8f9fa"],
        body.dark-mode [style*="background: #f8f9fa"] {
            background: #2d2d2d !important;
        }
        
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6,
        body.dark-mode p,
        body.dark-mode div,
        body.dark-mode span,
        body.dark-mode label {
            color: #e0e0e0 !important;
        }
        
        /* Dark Mode Text Colors - Override hardcoded colors */
        body.dark-mode [style*="color: #333"],
        body.dark-mode [style*="color:#333"],
        body.dark-mode [style*="color: #555"],
        body.dark-mode [style*="color:#555"],
        body.dark-mode [style*="color: #666"],
        body.dark-mode [style*="color:#666"] {
            color: #e0e0e0 !important;
        }
        
        /* Dark Mode Form Elements */
        body.dark-mode input,
        body.dark-mode textarea,
        body.dark-mode select,
        body.dark-mode [style*="background: white"] input,
        body.dark-mode [style*="background: white"] textarea,
        body.dark-mode [style*="background: white"] select {
            background: #2d2d2d !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }
        
        body.dark-mode input::placeholder,
        body.dark-mode textarea::placeholder {
            color: #888 !important;
        }
        
        /* Dark Mode Buttons */
        body.dark-mode button:not(.btn-primary):not([style*="background: linear-gradient"]) {
            background: #2d2d2d !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }
        
        
        /* Mobile Navigation */
        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 0 0 12px 12px;
            padding: 20px;
            z-index: 999;
        }
        
        .mobile-nav.active {
            display: block;
        }
        
        .mobile-nav a {
            display: block;
            padding: 15px 0;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px solid #eee;
            transition: color 0.3s;
        }
        
        .mobile-nav a:last-child {
            border-bottom: none;
        }
        
        .mobile-nav a:hover {
            color: #0039A6;
        }
        
        .mobile-nav .btn-primary {
            background: #0039A6;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-align: center;
            margin-top: 10px;
            border-bottom: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav {
                display: none;
            }
            
            .user-menu {
                display: none;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .logo svg {
                width: 38px;
                height: 12px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <?php logo('normal'); ?>
            
            <nav class="nav">
                <a href="/schools_modern.php" class="<?php echo $current_page == 'schools_modern.php' ? 'active' : ''; ?>">Школы</a>
                <a href="/events.php" class="<?php echo $current_page == 'events.php' ? 'active' : ''; ?>">События</a>
                <a href="/spo_modern.php" class="<?php echo $current_page == 'spo_modern.php' ? 'active' : ''; ?>">СПО</a>
                <a href="/vpo_modern.php" class="<?php echo $current_page == 'vpo_modern.php' ? 'active' : ''; ?>">ВПО</a>
                <a href="/posts" class="<?php echo $current_page == 'posts_modern.php' ? 'active' : ''; ?>">Статьи</a>
                <a href="/news" class="<?php echo $current_page == 'news_modern.php' ? 'active' : ''; ?>">Новости</a>
                <a href="/tests" class="<?php echo $current_page == 'tests-new.php' ? 'active' : ''; ?>">Тесты</a>
            </nav>
            
            <div class="user-menu">
                <!-- Theme Toggle -->
                <div class="theme-toggle" onclick="toggleTheme()" title="Переключить тему">
                    <i class="fas fa-sun" id="theme-icon"></i>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <div class="admin-dropdown" style="position: relative; display: inline-block;">
                            <a href="#" onclick="toggleAdminMenu(event)" style="cursor: pointer;">Админ ▼</a>
                            <div id="adminMenu" class="admin-menu" style="display: none; position: absolute; right: 0; top: 100%; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 200px; z-index: 1000;">
                                <a href="/admin/dashboard.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">📊 Обзор</a>
                                <a href="/admin/content/posts.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">📝 Посты</a>
                                <a href="/admin/content/news.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">📰 Новости</a>
                                <a href="/admin/users/" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">👥 Пользователи</a>
                                <a href="/admin/content/moderation.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">🔒 Модерация</a>
                                <a href="/admin/analytics/" style="display: block; padding: 10px 15px; color: #333; text-decoration: none;">📈 Аналитика</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <a href="/profile_modern.php">Профиль</a>
                    <a href="/logout_modern.php">Выйти</a>
                <?php else: ?>
                    <a href="/login_modern.php" class="btn-primary">Войти</a>
                <?php endif; ?>
            </div>
            
            <div class="mobile-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div class="mobile-nav" id="mobileNav">
            <div style="display: flex; justify-content: center; margin-bottom: 15px;">
                <div class="theme-toggle" onclick="toggleTheme()" title="Переключить тему" style="font-size: 18px; padding: 10px;">
                    <i class="fas fa-sun" id="mobile-theme-icon"></i> Тема
                </div>
            </div>
            <a href="/schools_modern.php">Школы</a>
            <a href="/events.php">События</a>
            <a href="/spo_modern.php">СПО</a>
            <a href="/vpo_modern.php">ВПО</a>
            <a href="/posts">Статьи</a>
            <a href="/news">Новости</a>
            <a href="/tests">Тесты</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/profile_modern.php">Профиль</a>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <div class="admin-dropdown" style="position: relative; display: inline-block;">
                        <a href="#" onclick="toggleAdminMenu(event)" style="cursor: pointer;">Админ ▼</a>
                        <div id="adminMenu" class="admin-menu" style="display: none; position: absolute; right: 0; top: 100%; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 200px; z-index: 1000;">
                            <a href="/admin/dashboard.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">📊 Обзор</a>
                            <a href="/admin/content/posts.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">📝 Посты</a>
                            <a href="/admin/content/news.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">📰 Новости</a>
                            <a href="/admin/users/" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">👥 Пользователи</a>
                            <a href="/admin/content/moderation.php" style="display: block; padding: 10px 15px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">🔒 Модерация</a>
                            <a href="/admin/analytics/" style="display: block; padding: 10px 15px; color: #333; text-decoration: none;">📈 Аналитика</a>
                        </div>
                    </div>
                <?php endif; ?>
                <a href="/logout_modern.php">Выйти</a>
            <?php else: ?>
                <a href="/login_modern.php" class="btn-primary">Войти</a>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- JavaScript for mobile toggle and theme -->
    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-toggle i');
            
            if (mobileNav.classList.contains('active')) {
                mobileNav.classList.remove('active');
                toggle.className = 'fas fa-bars';
            } else {
                mobileNav.classList.add('active');
                toggle.className = 'fas fa-times';
            }
        }
        
        // Admin menu toggle
        function toggleAdminMenu(event) {
            event.preventDefault();
            const menu = document.getElementById('adminMenu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }
        
        // Close admin menu when clicking outside
        document.addEventListener('click', function(event) {
            const adminDropdown = document.querySelector('.admin-dropdown');
            const adminMenu = document.getElementById('adminMenu');
            if (adminDropdown && adminMenu && !adminDropdown.contains(event.target)) {
                adminMenu.style.display = 'none';
            }
        });
        
        // Theme toggle functionality
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            const mobileThemeIcon = document.getElementById('mobile-theme-icon');
            
            if (body.classList.contains('dark-mode')) {
                // Switch to light mode
                body.classList.remove('dark-mode');
                themeIcon.className = 'fas fa-sun';
                mobileThemeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'light');
            } else {
                // Switch to dark mode
                body.classList.add('dark-mode');
                themeIcon.className = 'fas fa-moon';
                mobileThemeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            const mobileThemeIcon = document.getElementById('mobile-theme-icon');
            
            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
                themeIcon.className = 'fas fa-moon';
                mobileThemeIcon.className = 'fas fa-moon';
            } else {
                themeIcon.className = 'fas fa-sun';
                mobileThemeIcon.className = 'fas fa-sun';
            }
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (!toggle.contains(event.target) && !mobileNav.contains(event.target)) {
                mobileNav.classList.remove('active');
                document.querySelector('.mobile-toggle i').className = 'fas fa-bars';
            }
        });
        
        // Close mobile menu when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('mobileNav').classList.remove('active');
                document.querySelector('.mobile-toggle i').className = 'fas fa-bars';
            }
        });
    </script>
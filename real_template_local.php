<?php
// Local template version - no database connections needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Template content variables - can be set by including pages
$pageTitle = $pageTitle ?? 'Template';
$greyContent1 = $greyContent1 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Title/Header</p></div>';
$greyContent2 = $greyContent2 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Navigation/Categories</p></div>';
$greyContent3 = $greyContent3 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Metadata</p></div>';
$greyContent4 = $greyContent4 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Recent Content</p></div>';
$greyContent5 = $greyContent5 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Main Content</p></div>';
$greyContent6 = $greyContent6 ?? '<div style="text-align: center; padding: 20px; margin: 0;"><p>Footer Content</p></div>';
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '11-классники') ?></title>
    
    <!-- Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHJ4PSI2IiBmaWxsPSJ1cmwoI2dyYWRpZW50KSIvPgogIDx0ZXh0IHg9IjE2IiB5PSIyMiIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTgiIGZvbnQtd2VpZ2h0PSI3MDAiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4xMTwvdGV4dD4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwIiB5MT0iMCIgeDI9IjMyIiB5Mj0iMzIiPgogICAgICA8c3RvcCBzdG9wLWNvbG9yPSIjMDA3YmZmIi8+CiAgICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzAwNTNkNCIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+Cjwvc3ZnPg==" type="image/svg+xml">
    
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border-color: #dee2e6;
            --shadow: rgba(0,0,0,0.08);
            --link-color: #007bff;
            --link-hover: #0056b3;
        }
        
        [data-bs-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --border-color: #495057;
            --shadow: rgba(255,255,255,0.1);
            --link-color: #66b3ff;
            --link-hover: #4da3ff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; 
            line-height: 1.6; 
            color: var(--text-primary);
            background: var(--bg-primary);
            transition: background-color 0.3s, color 0.3s;
        }
        
        .container { 
            max-width: 1200px; 
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header { 
            background: var(--bg-primary); 
            box-shadow: 0 2px 10px var(--shadow); 
            position: sticky; 
            top: 0; 
            z-index: 1050;
            transition: background-color 0.3s;
        }
        
        .header-nav { 
            display: flex; 
            align-items: center; 
            gap: 30px; 
            padding: 15px 0;
            flex-wrap: wrap;
        }
        
        .nav-link { 
            color: var(--text-primary); 
            text-decoration: none; 
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-link:hover { 
            color: var(--link-color); 
        }
        
        .footer { 
            background: var(--bg-secondary); 
            padding: 30px 0; 
            margin-top: 40px;
            color: var(--text-primary);
            transition: background-color 0.3s;
        }
        
        .content-section {
            padding: 20px 0;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .header-nav {
                gap: 10px;
                position: relative;
            }
            
            .header-nav nav {
                order: 3;
                width: 100%;
                display: flex;
                gap: 10px;
                overflow-x: auto;
                padding: 10px 0;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            
            .header-nav nav::-webkit-scrollbar {
                display: none;
            }
            
            .nav-link {
                white-space: nowrap;
                padding: 5px 12px;
                background: var(--bg-secondary);
                border-radius: 20px;
                font-size: 14px;
            }
            
            .container {
                padding: 0 15px;
            }
            
            /* Hide search on mobile, show search icon */
            form[action="/search"] input {
                display: none;
            }
            
            form[action="/search"] {
                width: auto;
            }
            
            /* Adjust user menu */
            #userMenu {
                position: fixed !important;
                left: 10px !important;
                right: 10px !important;
                width: auto !important;
                top: auto !important;
                bottom: 20px !important;
            }
            
            /* Stack content sections on mobile */
            [style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
            
            /* Adjust font sizes */
            h1 {
                font-size: 28px !important;
            }
            
            h2 {
                font-size: 24px !important;
            }
            
            h3 {
                font-size: 20px !important;
            }
            
            /* Adjust padding on mobile */
            [style*="padding: 50px 20px"],
            [style*="padding: 60px 20px"],
            [style*="padding: 40px 20px"] {
                padding: 30px 15px !important;
            }
            
            /* Mobile-friendly buttons */
            button, a[style*="padding"] {
                min-height: 44px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            /* Even smaller screens */
            .header-nav {
                padding: 10px 0;
            }
            
            /* Hide theme toggle on very small screens */
            .theme-toggle {
                display: none;
            }
            
            /* Full width forms */
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                font-size: 16px !important; /* Prevent zoom on iOS */
            }
        }
        
        /* Dark mode specific styles */
        [data-bs-theme="dark"] a {
            color: var(--link-color);
        }
        
        [data-bs-theme="dark"] a:hover {
            color: var(--link-hover);
        }
        
        /* Smooth transitions */
        * {
            transition-property: background-color, color, border-color;
            transition-duration: 0.3s;
            transition-timing-function: ease;
        }
        
        /* Theme toggle button */
        .theme-toggle {
            background: transparent;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .theme-toggle:hover {
            background: var(--bg-secondary);
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-nav">
                <a href="/" style="font-size: 24px; font-weight: 700; color: #007bff; text-decoration: none;">11</a>
                <nav style="display: flex; gap: 20px;">
                    <a href="/vpo" class="nav-link">ВУЗы</a>
                    <a href="/spo" class="nav-link">ССУЗы</a>
                    <a href="/schools" class="nav-link">Школы</a>
                    <a href="/news" class="nav-link">Новости</a>
                    <a href="/posts" class="nav-link">Статьи</a>
                    <a href="/events" class="nav-link">События</a>
                </nav>
                <div style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
                    <form action="/search" method="get" style="display: flex; gap: 5px;" onsubmit="return handleSearch(event)">
                        <input type="text" name="q" placeholder="Поиск..." id="headerSearch"
                               style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 20px; width: 150px; font-size: 14px;"
                               oninput="handleSearchInput(this.value)">
                        <button type="submit" style="background: transparent; border: none; cursor: pointer; color: var(--text-primary);">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                    <?php 
                    if (isset($_SESSION['user_id'])) {
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
                        echo include_notification_badge();
                    }
                    ?>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div style="position: relative;">
                        <?php 
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/upload.php';
                        $currentUser = db_fetch_one("SELECT avatar FROM users WHERE id = ?", [$_SESSION['user_id']]);
                        $avatarUrl = get_user_avatar($_SESSION['user_id'], $_SESSION['user_name'], $currentUser['avatar']);
                        ?>
                        <button onclick="toggleUserMenu()" 
                                style="display: flex; align-items: center; gap: 8px; background: transparent; 
                                       border: 1px solid var(--border-color); border-radius: 20px; 
                                       padding: 4px 12px 4px 4px; cursor: pointer; color: var(--text-primary);">
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                                 alt="Avatar" 
                                 style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                            <span style="font-size: 14px;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                        </button>
                        <div id="userMenu" style="display: none; position: absolute; right: 0; top: 100%; 
                                                  margin-top: 8px; background: var(--bg-primary); 
                                                  border: 1px solid var(--border-color); border-radius: 8px; 
                                                  box-shadow: 0 4px 12px var(--shadow); min-width: 200px; z-index: 1000;">
                            <a href="/profile" style="display: block; padding: 10px 16px; color: var(--text-primary); 
                                                     text-decoration: none; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-user"></i> Мой профиль
                            </a>
                            <a href="/favorites" style="display: block; padding: 10px 16px; color: var(--text-primary); 
                                                       text-decoration: none; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-heart"></i> Избранное
                            </a>
                            <a href="/reading-lists" style="display: block; padding: 10px 16px; color: var(--text-primary); 
                                                           text-decoration: none; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-bookmark"></i> Списки для чтения
                            </a>
                            <a href="/settings" style="display: block; padding: 10px 16px; color: var(--text-primary); 
                                                      text-decoration: none; border-bottom: 1px solid var(--border-color);">
                                <i class="fas fa-cog"></i> Настройки
                            </a>
                            <a href="/logout" style="display: block; padding: 10px 16px; color: #dc3545; 
                                                    text-decoration: none;">
                                <i class="fas fa-sign-out-alt"></i> Выйти
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="/login" 
                       style="display: flex; align-items: center; gap: 6px; padding: 6px 16px; 
                              background: #007bff; color: white; border-radius: 20px; 
                              text-decoration: none; font-size: 14px; font-weight: 500;">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </a>
                    <?php endif; ?>
                    
                    <button onclick="toggleTheme()" class="theme-toggle" aria-label="Toggle dark mode">
                        <i class="fas fa-moon" id="theme-icon"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Content Section 1 -->
            <div class="content-section">
                <?= $greyContent1 ?>
            </div>
            
            <!-- Content Section 2 -->
            <?php if (!empty($greyContent2)): ?>
            <div class="content-section">
                <?= $greyContent2 ?>
            </div>
            <?php endif; ?>
            
            <!-- Content Section 3 -->
            <?php if (!empty($greyContent3)): ?>
            <div class="content-section">
                <?= $greyContent3 ?>
            </div>
            <?php endif; ?>
            
            <!-- Content Section 4 -->
            <?php if (!empty($greyContent4)): ?>
            <div class="content-section">
                <?= $greyContent4 ?>
            </div>
            <?php endif; ?>
            
            <!-- Content Section 5 -->
            <?php if (!empty($greyContent5)): ?>
            <div class="content-section">
                <?= $greyContent5 ?>
            </div>
            <?php endif; ?>
            
            <!-- Content Section 6 -->
            <?php if (!empty($greyContent6)): ?>
            <div class="content-section">
                <?= $greyContent6 ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>11klassniki.ru</h5>
                    <p>Портал образования России</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/contact.php" class="text-muted">Связь с нами</a> |
                    <a href="/privacy.php" class="text-muted">Политика конфиденциальности</a> |
                    <a href="/news" class="text-muted">Новости</a> |
                    <a href="/events.php" class="text-muted">События</a> |
                    <a href="/schools" class="text-muted">Школы</a> |
                    <a href="/vpo" class="text-muted">ВУЗы</a>
                    <p class="mt-2">&copy; 2025 11klassniki.ru</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        }
        
        // User menu toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            if (menu) {
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            }
        }
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            if (menu && !event.target.closest('[onclick="toggleUserMenu()"]') && !menu.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
        
        // Search functionality
        function handleSearchInput(value) {
            // Show live search suggestions dropdown (better UX)
            if (value.length >= 2) {
                // Could show a dropdown with suggestions here
                // For now, just prepare for search
            }
        }
        
        function handleSearch(event) {
            const input = document.getElementById('headerSearch');
            const query = input.value.trim();
            
            if (query.length === 0) {
                event.preventDefault();
                return false;
            }
            
            // Allow form to submit normally to /search
            return true;
        }
        
        // Auto-focus search field when user presses '/' key
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                e.preventDefault();
                document.getElementById('headerSearch')?.focus();
            }
        });
        
        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            html.setAttribute('data-bs-theme', savedTheme);
            
            if (themeIcon) {
                themeIcon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        });
    </script>
</body>
</html>
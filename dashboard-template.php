<?php
// Dashboard Template - Base template for all dashboard pages
// Usage: Set $dashboardContent and $dashboardTitle before including this file

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    header('Location: /unauthorized');
    exit();
}

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set default title if not provided
if (!isset($dashboardTitle)) {
    $dashboardTitle = 'Dashboard';
}

// Determine active menu item from current path
$currentPath = $_SERVER['REQUEST_URI'];
$activeMenu = '';
if (strpos($currentPath, '/dashboard/news') !== false) {
    $activeMenu = 'news';
} elseif (strpos($currentPath, '/dashboard/posts') !== false) {
    $activeMenu = 'posts';
} elseif (strpos($currentPath, '/dashboard/users') !== false) {
    $activeMenu = 'users';
} elseif (strpos($currentPath, '/dashboard/comments') !== false) {
    $activeMenu = 'comments';
} elseif (strpos($currentPath, '/dashboard/schools') !== false) {
    $activeMenu = 'schools';
} elseif (strpos($currentPath, '/dashboard/vpo') !== false) {
    $activeMenu = 'vpo';
} elseif (strpos($currentPath, '/dashboard/spo') !== false) {
    $activeMenu = 'spo';
} elseif ($currentPath === '/dashboard' || $currentPath === '/dashboard/') {
    $activeMenu = 'home';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($dashboardTitle) ?> - 11-классники</title>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php'; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #007bff;
            --surface: #ffffff;
            --text-primary: #333;
            --text-secondary: #666;
            --border-color: #e9ecef;
            --bg-light: #f8f9fa;
        }
        
        [data-theme="dark"] {
            --surface: #1a202c;
            --text-primary: #e4e6eb;
            --text-secondary: #a0aec0;
            --border-color: #2d3748;
            --bg-light: #2d3748;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        
        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            height: 100vh;
        }
        
        /* Sidebar */
        .dashboard-sidebar {
            width: var(--sidebar-width);
            background: var(--surface);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .close-btn:hover {
            background: var(--bg-light);
            color: var(--text-primary);
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .nav-item {
            display: block;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: var(--bg-light);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .nav-item.active {
            background: var(--bg-light);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .nav-item i {
            width: 20px;
            margin-right: 12px;
        }
        
        /* Main Content */
        .dashboard-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        /* Top Bar */
        .dashboard-topbar {
            background: var(--surface);
            padding: 15px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Theme Toggle */
        .theme-toggle {
            background: transparent;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        /* User Avatar */
        .user-menu {
            position: relative;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid var(--border-color);
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 10px;
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }
        
        .user-dropdown.show {
            display: block;
        }
        
        .dropdown-item {
            padding: 10px 16px;
            color: var(--text-primary);
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: var(--bg-light);
            color: var(--primary-color);
        }
        
        .dropdown-divider {
            height: 0;
            margin: 8px 0;
            border-top: 1px solid var(--border-color);
        }
        
        /* Content Area */
        .dashboard-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        
        /* Mobile Sidebar Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            font-size: 24px;
            z-index: 1000;
            cursor: pointer;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-sidebar {
                position: fixed;
                left: -100%;
                height: 100vh;
                z-index: 999;
            }
            
            .dashboard-sidebar.open {
                left: 0;
            }
            
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .dashboard-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="dashboard-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/" class="logo">11-классники</a>
                <button class="close-btn" onclick="closeDashboard()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item <?= $activeMenu === 'home' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Главная
                </a>
                <a href="/dashboard/news" class="nav-item <?= $activeMenu === 'news' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i> Новости
                </a>
                <a href="/dashboard/posts" class="nav-item <?= $activeMenu === 'posts' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i> Статьи
                </a>
                <a href="/dashboard/users" class="nav-item <?= $activeMenu === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Пользователи
                </a>
                <a href="/dashboard/comments" class="nav-item <?= $activeMenu === 'comments' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> Комментарии
                </a>
                <a href="/dashboard/schools" class="nav-item <?= $activeMenu === 'schools' ? 'active' : '' ?>">
                    <i class="fas fa-school"></i> Школы
                </a>
                <a href="/dashboard/vpo" class="nav-item <?= $activeMenu === 'vpo' ? 'active' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> ВУЗы
                </a>
                <a href="/dashboard/spo" class="nav-item <?= $activeMenu === 'spo' ? 'active' : '' ?>">
                    <i class="fas fa-university"></i> СПО
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Top Bar -->
            <div class="dashboard-topbar">
                <h1 class="topbar-title"><?= htmlspecialchars($dashboardTitle) ?></h1>
                
                <div class="topbar-actions">
                    <!-- Theme Toggle -->
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon" id="theme-icon"></i>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="user-menu">
                        <div class="user-avatar" onclick="toggleUserMenu()">
                            <?= $userInitial ?>
                        </div>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="/account" class="dropdown-item">
                                <i class="fas fa-user" style="width: 20px;"></i> Мой профиль
                            </a>
                            <a href="/settings" class="dropdown-item">
                                <i class="fas fa-cog" style="width: 20px;"></i> Настройки
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/logout" class="dropdown-item">
                                <i class="fas fa-sign-out-alt" style="width: 20px;"></i> Выйти
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="dashboard-content">
                <?= $dashboardContent ?>
            </div>
        </div>
    </div>
    
    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <?php
    // Include modern modal component
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/modal-modern.php';
    renderModalModern();
    ?>
    
    <script>
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const icon = document.getElementById('theme-icon');
            icon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
        
        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.getElementById('theme-icon').className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        });
        
        // User Menu Toggle
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const userMenu = document.querySelector('.user-menu');
            if (!userMenu.contains(e.target)) {
                document.getElementById('userDropdown').classList.remove('show');
            }
        });
        
        // Sidebar Toggle (Mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
        
        // Close Dashboard (X button)
        function closeDashboard() {
            ModalManager.confirm('Выход из панели управления', 'Вы уверены, что хотите выйти из панели управления?', () => {
                window.location.href = '/';
            }, 'warning');
        }
        
        // News Dashboard Functions
        function approveNews(newsId) {
            ModalManager.confirm('Одобрение новости', 'Одобрить эту новость для публикации?', () => {
                window.location.href = `/api/news/approve/${newsId}?redirect=/dashboard/news`;
            }, 'info');
        }

        function unapproveNews(newsId) {
            ModalManager.confirm('Отмена публикации', 'Снять эту новость с публикации?', () => {
                window.location.href = `/api/news/unapprove/${newsId}?redirect=/dashboard/news`;
            }, 'warning');
        }

        function deleteNews(newsId) {
            ModalManager.confirm('Удаление новости', 'Вы уверены, что хотите удалить эту новость? Это действие нельзя отменить.', () => {
                window.location.href = `/api/news/delete/${newsId}?redirect=/dashboard/news`;
            }, 'danger');
        }
        
        // Comments Dashboard Functions
        function deleteComment(commentId) {
            ModalManager.confirm('Удаление комментария', 'Вы уверены, что хотите удалить этот комментарий? Это действие нельзя отменить.', () => {
                window.location.href = `/api/comments/delete/${commentId}?redirect=/dashboard/comments`;
            }, 'danger');
        }
        
        // Posts Dashboard Functions
        function deletePost(postId) {
            ModalManager.confirm('Удаление статьи', 'Вы уверены, что хотите удалить эту статью? Это действие нельзя отменить.', () => {
                window.location.href = `/api/posts/delete/${postId}?redirect=/dashboard/posts`;
            }, 'danger');
        }
    </script>
</body>
</html>
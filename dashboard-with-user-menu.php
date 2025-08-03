<?php
// Ultra-simple professional admin dashboard with dark mode and user menu
session_start();

// Basic auth check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

$username = $_SESSION['username'] ?? $_SESSION['email'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - 11классники</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --light: #f8fafc;
            --dark: #0f172a;
            --white: #ffffff;
            --border: #e2e8f0;
            --sidebar-width: 280px;
            --header-height: 70px;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        /* Dark mode variables */
        [data-theme="dark"] {
            --light: #1e293b;
            --dark: #f1f5f9;
            --white: #0f172a;
            --border: #334155;
            --secondary: #94a3b8;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3), 0 1px 2px -1px rgb(0 0 0 / 0.2);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.3), 0 4px 6px -4px rgb(0 0 0 / 0.2);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--white);
            border-right: 1px solid var(--border);
            box-shadow: var(--shadow);
            z-index: 1000;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            border-bottom: 1px solid var(--border);
            background: var(--primary);
            color: white;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 24px 0;
        }

        .nav-section {
            margin-bottom: 32px;
        }

        .nav-section-title {
            padding: 0 24px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--secondary);
        }

        .nav-item {
            display: block;
            padding: 12px 24px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: var(--light);
            border-left-color: var(--primary);
            color: var(--primary);
        }

        .nav-item.active {
            background: var(--light);
            border-left-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-icon {
            width: 20px;
            display: inline-block;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Header */
        .header {
            height: var(--header-height);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background-color 0.3s ease;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .toggle-btn {
            background: none;
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            color: var(--secondary);
            transition: all 0.2s;
        }

        .toggle-btn:hover {
            background: var(--light);
            color: var(--dark);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Dark mode toggle */
        .theme-toggle {
            background: none;
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            color: var(--secondary);
            transition: all 0.2s;
            font-size: 1.2rem;
        }

        .theme-toggle:hover {
            background: var(--light);
            color: var(--dark);
        }

        /* User menu dropdown */
        .user-menu {
            position: relative;
        }

        .user-menu-trigger {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--light);
            border-radius: 8px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .user-menu-trigger:hover {
            background: var(--border);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-name {
            font-weight: 500;
        }

        .dropdown-arrow {
            font-size: 0.8rem;
            transition: transform 0.2s;
        }

        .user-menu.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* Dropdown menu */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1000;
        }

        .user-menu.open .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            background: var(--light);
            border-radius: 8px 8px 0 0;
        }

        .dropdown-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dropdown-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .dropdown-user-details h4 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 2px;
        }

        .dropdown-user-details p {
            font-size: 0.75rem;
            color: var(--secondary);
            margin: 0;
        }

        .dropdown-menu {
            padding: 8px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .dropdown-item:hover {
            background: var(--light);
            color: var(--primary);
        }

        .dropdown-item.danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .dropdown-icon {
            width: 16px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 0;
        }

        /* Content area */
        .content {
            padding: 32px;
            max-width: 1400px;
        }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--secondary);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.users { background: rgba(6, 182, 212, 0.1); color: var(--info); }
        .stat-icon.news { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .stat-icon.schools { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .stat-icon.content { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .stat-change {
            font-size: 0.875rem;
            color: var(--success);
            font-weight: 500;
        }

        /* Quick actions */
        .quick-actions {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 32px;
            transition: background-color 0.3s ease;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .action-card {
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .action-card:hover {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.02);
        }

        [data-theme="dark"] .action-card:hover {
            background: rgba(37, 99, 235, 0.1);
        }

        .action-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-description {
            color: var(--secondary);
            margin-bottom: 16px;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--light);
            color: var(--secondary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
            color: var(--dark);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content {
                padding: 24px 16px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 16px;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .user-dropdown {
                right: -16px;
            }
        }

        /* Overlay for mobile */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 1024px) {
            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Dark mode animations */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="logo">11классники</a>
            <button class="close-btn" onclick="window.location.href='/'">×</button>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Управление</div>
                <a href="#" class="nav-item active">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>
                <a href="/pages/dashboard/users-dashboard/users-view/users-view.php" class="nav-item">
                    <span class="nav-icon">👥</span>
                    Пользователи
                </a>
                <a href="/admin-backup-tool.php" class="nav-item">
                    <span class="nav-icon">💾</span>
                    База данных
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Контент</div>
                <a href="/pages/common/news/news-create.php" class="nav-item">
                    <span class="nav-icon">📰</span>
                    Новости
                </a>
                <a href="/pages/common/create.php" class="nav-item">
                    <span class="nav-icon">📝</span>
                    Посты
                </a>
                <a href="/pages/dashboard/comments-dashboard/comments-view/comments-view.php" class="nav-item">
                    <span class="nav-icon">💬</span>
                    Комментарии
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Образование</div>
                <a href="/schools-all-regions" class="nav-item">
                    <span class="nav-icon">🏫</span>
                    Школы
                </a>
                <a href="/vpo-all-regions" class="nav-item">
                    <span class="nav-icon">🎓</span>
                    ВУЗы
                </a>
                <a href="/spo-all-regions" class="nav-item">
                    <span class="nav-icon">📚</span>
                    СПО
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Система</div>
                <a href="/" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    Главная
                </a>
                <a href="/logout" class="nav-item">
                    <span class="nav-icon">🚪</span>
                    Выход
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <button class="toggle-btn" id="toggleSidebar">☰</button>
                <h1 class="page-title">Dashboard</h1>
            </div>
            
            <div class="header-right">
                <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
                    <span class="theme-icon">🌙</span>
                </button>
                
                <!-- User Menu with Dropdown -->
                <div class="user-menu" id="userMenu">
                    <button class="user-menu-trigger" id="userMenuTrigger">
                        <div class="user-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
                        <span class="user-name"><?= htmlspecialchars($username) ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-user-info">
                                <div class="dropdown-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
                                <div class="dropdown-user-details">
                                    <h4><?= htmlspecialchars($username) ?></h4>
                                    <p>Администратор</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu">
                            <a href="/account" class="dropdown-item">
                                <span class="dropdown-icon">👤</span>
                                Мой профиль
                            </a>
                            <a href="/account/personal-data-change/" class="dropdown-item">
                                <span class="dropdown-icon">⚙️</span>
                                Настройки
                            </a>
                            <a href="/account/password-change/" class="dropdown-item">
                                <span class="dropdown-icon">🔐</span>
                                Изменить пароль
                            </a>
                            <a href="/admin-backup-tool.php" class="dropdown-item">
                                <span class="dropdown-icon">💾</span>
                                Резервные копии
                            </a>
                            
                            <div class="dropdown-divider"></div>
                            
                            <a href="/" class="dropdown-item">
                                <span class="dropdown-icon">🏠</span>
                                Главная страница
                            </a>
                            <a href="/logout" class="dropdown-item danger">
                                <span class="dropdown-icon">🚪</span>
                                Выйти
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Пользователи</div>
                            <div class="stat-value">1,247</div>
                            <div class="stat-change">+12% за месяц</div>
                        </div>
                        <div class="stat-icon users">👥</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Новости</div>
                            <div class="stat-value">89</div>
                            <div class="stat-change">+5 за неделю</div>
                        </div>
                        <div class="stat-icon news">📰</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Учреждения</div>
                            <div class="stat-value">2,156</div>
                            <div class="stat-change">Актуальная база</div>
                        </div>
                        <div class="stat-icon schools">🏫</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Контент</div>
                            <div class="stat-value">456</div>
                            <div class="stat-change">+8% активность</div>
                        </div>
                        <div class="stat-icon content">📝</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2 class="section-title">Быстрые действия</h2>
                <div class="actions-grid">
                    <div class="action-card">
                        <div class="action-title">👥 Управление пользователями</div>
                        <div class="action-description">
                            Просматривайте, редактируйте и управляйте учетными записями пользователей системы.
                        </div>
                        <div class="action-buttons">
                            <a href="/pages/dashboard/users-dashboard/users-view/users-view.php" class="btn btn-primary">Все пользователи</a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="action-title">📰 Управление новостями</div>
                        <div class="action-description">
                            Создавайте, редактируйте и модерируйте новостные статьи для публикации.
                        </div>
                        <div class="action-buttons">
                            <a href="/pages/common/news/news-create.php" class="btn btn-primary">Создать новость</a>
                            <a href="/news" class="btn btn-secondary">Все новости</a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="action-title">🏫 Образовательные учреждения</div>
                        <div class="action-description">
                            Управляйте базой данных школ, университетов и колледжей по всем регионам.
                        </div>
                        <div class="action-buttons">
                            <a href="/schools-all-regions" class="btn btn-primary">Школы</a>
                            <a href="/vpo-all-regions" class="btn btn-secondary">ВУЗы</a>
                            <a href="/spo-all-regions" class="btn btn-secondary">СПО</a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="action-title">💾 База данных</div>
                        <div class="action-description">
                            Создавайте резервные копии базы данных и управляйте системными настройками.
                        </div>
                        <div class="action-buttons">
                            <a href="/admin-backup-tool.php" class="btn btn-primary">Backup Database</a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="action-title">📝 Создание контента</div>
                        <div class="action-description">
                            Создавайте и публикуйте новые статьи, посты и образовательные материалы.
                        </div>
                        <div class="action-buttons">
                            <a href="/pages/common/create.php" class="btn btn-primary">Создать пост</a>
                            <a href="/write" class="btn btn-secondary">Написать статью</a>
                        </div>
                    </div>

                    <div class="action-card">
                        <div class="action-title">💬 Модерация</div>
                        <div class="action-description">
                            Просматривайте и модерируйте пользовательские комментарии и сообщения.
                        </div>
                        <div class="action-buttons">
                            <a href="/pages/dashboard/comments-dashboard/comments-view/comments-view.php" class="btn btn-secondary">Комментарии</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('overlay');
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('.theme-icon');
        const userMenu = document.getElementById('userMenu');
        const userMenuTrigger = document.getElementById('userMenuTrigger');

        // Theme management
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);
        updateThemeIcon();

        function updateThemeIcon() {
            const theme = document.documentElement.getAttribute('data-theme');
            themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
            themeToggle.title = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
        }

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        });

        // User menu dropdown
        userMenuTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('open');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('open');
            }
        });

        // Close dropdown when clicking a menu item
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
                userMenu.classList.remove('open');
            });
        });

        // Toggle sidebar
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });

        // Add loading states to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            if (btn.href && !btn.onclick) {
                btn.addEventListener('click', function() {
                    this.style.opacity = '0.7';
                    const originalText = this.innerHTML;
                    this.innerHTML = '⏳ Загрузка...';
                    
                    // Reset after 3 seconds if still on page
                    setTimeout(() => {
                        this.style.opacity = '1';
                        this.innerHTML = originalText;
                    }, 3000);
                });
            }
        });

        // Auto-refresh stats every 5 minutes
        setTimeout(() => {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>
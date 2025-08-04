<?php
// Unified Content Creation Dashboard - Professional Design
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

$username = $_SESSION['username'] ?? $_SESSION['email'] ?? 'Admin';
$contentType = $_GET['type'] ?? 'news'; // Default to news
$pageTitle = $contentType === 'news' ? 'Создать новость' : 'Создать пост';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - 11классники</title>
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

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Same sidebar styling as other dashboards */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--white);
            border-right: 1px solid var(--border);
            box-shadow: var(--shadow);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .brand-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }

        .nav {
            padding: 24px 0;
            height: calc(100vh - 100px);
            overflow-y: auto;
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
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: var(--secondary);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            border-right: 3px solid var(--primary);
        }

        .nav-icon {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
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
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            color: var(--secondary);
            transition: all 0.2s;
        }

        .toggle-btn:hover {
            background: rgba(0, 0, 0, 0.1);
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

        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--light);
            border-radius: 8px;
            color: var(--dark);
            cursor: pointer;
            transition: background 0.2s;
        }

        .user-menu:hover {
            background: var(--border);
        }

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
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .user-dropdown.active {
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
        }

        .dropdown-user-details h4 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .dropdown-user-details p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--secondary);
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
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: var(--light);
            color: var(--dark);
        }

        .dropdown-item.danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .dropdown-icon {
            font-size: 1rem;
            width: 20px;
            text-align: center;
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

        /* Content area */
        .content {
            padding: 32px;
            max-width: 1200px;
        }

        /* Content Type Selector */
        .type-selector {
            display: flex;
            gap: 16px;
            margin-bottom: 32px;
            background: var(--white);
            padding: 8px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .type-btn {
            flex: 1;
            padding: 12px 24px;
            background: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
            color: var(--secondary);
        }

        .type-btn.active {
            background: var(--primary);
            color: white;
        }

        .type-btn:not(.active):hover {
            background: var(--light);
        }

        /* Form Card */
        .form-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .form-card-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            background: var(--light);
        }

        .form-card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-card-body {
            padding: 32px;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-textarea {
            min-height: 200px;
            resize: vertical;
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 16px;
            justify-content: flex-start;
            margin-top: 32px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .content {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">11</div>
            <div class="brand-text">Admin Panel</div>
        </div>
        
        <nav class="nav">
            <div class="nav-section">
                <div class="nav-section-title">Основное</div>
                <a href="/dashboard" class="nav-item">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>
                <a href="/dashboard/users" class="nav-item">
                    <span class="nav-icon">👥</span>
                    Пользователи
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Контент</div>
                <a href="/create/news" class="nav-item <?= $contentType === 'news' ? 'active' : '' ?>">
                    <span class="nav-icon">📰</span>
                    Создать новость
                </a>
                <a href="/create/post" class="nav-item <?= $contentType === 'post' ? 'active' : '' ?>">
                    <span class="nav-icon">📝</span>
                    Создать пост
                </a>
                <a href="/news" class="nav-item">
                    <span class="nav-icon">📋</span>
                    Все новости
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
                <h1 class="page-title"><?= $pageTitle ?></h1>
            </div>
            
            <div class="header-right">
                <div class="user-menu" id="userMenu">
                    <div class="user-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
                    <span><?= htmlspecialchars($username) ?></span>
                    <span style="margin-left: 8px; font-size: 0.8rem;">▼</span>
                    
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
                                Мой аккаунт
                            </a>
                            <a href="/dashboard" class="dropdown-item">
                                <span class="dropdown-icon">📊</span>
                                Dashboard
                            </a>
                            <a href="/dashboard/users" class="dropdown-item">
                                <span class="dropdown-icon">👥</span>
                                Пользователи
                            </a>
                            <a href="/create/news" class="dropdown-item">
                                <span class="dropdown-icon">📰</span>
                                Создать новость
                            </a>
                            <a href="/create/post" class="dropdown-item">
                                <span class="dropdown-icon">📝</span>
                                Создать пост
                            </a>
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
            <!-- Content Type Selector -->
            <div class="type-selector">
                <a href="/create/news" class="type-btn <?= $contentType === 'news' ? 'active' : '' ?>">
                    📰 Создать новость
                </a>
                <a href="/create/post" class="type-btn <?= $contentType === 'post' ? 'active' : '' ?>">
                    📝 Создать пост
                </a>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <h2 class="form-card-title">
                        <?= $contentType === 'news' ? '📰 Новая новость' : '📝 Новый пост' ?>
                    </h2>
                </div>
                
                <div class="form-card-body">
                    <form action="/create/process" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="content_type" value="<?= htmlspecialchars($contentType) ?>">
                        
                        <div class="form-group">
                            <label class="form-label" for="title">Заголовок</label>
                            <input type="text" id="title" name="title" class="form-input" required 
                                   placeholder="<?= $contentType === 'news' ? 'Введите заголовок новости...' : 'Введите заголовок поста...' ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Краткое описание</label>
                            <textarea id="description" name="description" class="form-input" rows="3" required
                                      placeholder="Краткое описание для превью..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="content">Содержание</label>
                            <textarea id="content" name="content" class="form-input form-textarea" required
                                      placeholder="Основной текст <?= $contentType === 'news' ? 'новости' : 'поста' ?>..."></textarea>
                        </div>

                        <?php if ($contentType === 'news'): ?>
                        <div class="form-group">
                            <label class="form-label" for="news_type">Тип новости</label>
                            <select id="news_type" name="news_type" class="form-select">
                                <option value="education">Образование</option>
                                <option value="vpo">ВУЗы</option>
                                <option value="spo">СПО</option>
                                <option value="school">Школы</option>
                                <option value="general">Общие</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="form-label" for="image">Изображение (необязательно)</label>
                            <input type="file" id="image" name="image" class="form-input" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="status">Статус публикации</label>
                            <select id="status" name="status" class="form-select">
                                <option value="published">Опубликовать сразу</option>
                                <option value="draft">Сохранить как черновик</option>
                            </select>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                ✅ <?= $contentType === 'news' ? 'Создать новость' : 'Создать пост' ?>
                            </button>
                            <a href="/dashboard" class="btn btn-secondary">
                                ❌ Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Sidebar toggle
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // User dropdown menu
        const userMenu = document.getElementById('userMenu');
        const userDropdown = document.getElementById('userDropdown');

        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });

        // Close dropdown when pressing Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                userDropdown.classList.remove('active');
            }
        });

        // Auto-resize textarea
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();
            
            if (!title || !content) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
                return false;
            }
        });
    </script>
</body>
</html>
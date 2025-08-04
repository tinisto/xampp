<?php
// Unified Content Creation Dashboard - Uses same design as main dashboard
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

// Check for success or error messages
$success = $_GET['success'] ?? null;
$error = $_SESSION['error'] ?? null;
$successMsg = $_SESSION['success'] ?? null;
unset($_SESSION['error']);
unset($_SESSION['success']);

// Get counts for sidebar badges
$news_published = 0;
$news_drafts = 0;
$posts_total = 0;

// Count news
$news_count_sql = "SELECT approved, COUNT(*) as count FROM news GROUP BY approved";
$news_result = $connection->query($news_count_sql);
if ($news_result) {
    while ($row = $news_result->fetch_assoc()) {
        if ($row['approved'] == 1) {
            $news_published = $row['count'];
        } else {
            $news_drafts = $row['count'];
        }
    }
}

// Count posts (all posts since no status field)
$posts_count_sql = "SELECT COUNT(*) as count FROM posts";
$posts_result = $connection->query($posts_count_sql);
if ($posts_result) {
    $row = $posts_result->fetch_assoc();
    $posts_total = $row['count'];
}

// Copy the exact styles from dashboard-professional.php
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

        /* Dark mode variables */
        [data-theme="dark"] {
            --light: #1e293b;
            --dark: #f1f5f9;
            --white: #0f172a;
            --border: #334155;
            --secondary: #94a3b8;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3), 0 1px 2px -1px rgb(0 0 0 / 0.3);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.3), 0 4px 6px -4px rgb(0 0 0 / 0.3);
        }

        /* Theme Toggle */
        .theme-toggle {
            background: var(--light);
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.25rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: var(--border);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar - matching dashboard-professional.php exactly */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--white);
            box-shadow: var(--shadow);
            z-index: 40;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            transform: translateX(-100%); /* Start closed */
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .logo:hover {
            color: var(--primary-dark);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 2rem;
            line-height: 1;
            color: var(--secondary);
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .close-btn:hover {
            background: var(--light);
            color: var(--dark);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem 0;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--secondary);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.2s;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary);
        }

        .nav-item.active {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary);
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 0; /* Start with no margin since sidebar is closed */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.sidebar-open {
            margin-left: var(--sidebar-width);
        }

        .header {
            background: var(--white);
            padding: 0 2rem;
            height: var(--header-height);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toggle-btn {
            display: block; /* Always show toggle button */
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background 0.2s;
        }

        .toggle-btn:hover {
            background: var(--light);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
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

        /* Alert System */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 400px;
        }

        .custom-alert {
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            animation: slideIn 0.3s ease-out;
            position: relative;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .alert-error {
            background-color: #fee;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .alert-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        .alert-message {
            flex: 1;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: inherit;
            opacity: 0.5;
            padding: 0;
            margin-left: 10px;
        }

        .alert-close:hover {
            opacity: 1;
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
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
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
        @media (max-width: 1024px) {
            /* Sidebar already starts closed, no changes needed */
        }

        /* Overlay for mobile */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
            z-index: 30;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <!-- Alert Container -->
    <div id="alertContainer" class="alert-container"></div>
    
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
                <a href="/dashboard" class="nav-item">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>
                <a href="/dashboard/users" class="nav-item">
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
                <a href="/dashboard/news" class="nav-item">
                    <span class="nav-icon">📰</span>
                    Управление новостями
                    <?php if ($news_published > 0 || $news_drafts > 0): ?>
                    <span class="nav-badge" style="margin-left: auto; background: var(--primary); color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;"><?= $news_published ?>/<?= $news_drafts ?></span>
                    <?php endif; ?>
                </a>
                <a href="/dashboard/posts" class="nav-item">
                    <span class="nav-icon">📋</span>
                    Управление постами
                    <?php if ($posts_total > 0): ?>
                    <span class="nav-badge" style="margin-left: auto; background: var(--primary); color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;"><?= $posts_total ?></span>
                    <?php endif; ?>
                </a>
                <a href="/create/news" class="nav-item <?= $contentType === 'news' ? 'active' : '' ?>">
                    <span class="nav-icon">➕</span>
                    Создать новость
                </a>
                <a href="/create/post" class="nav-item <?= $contentType === 'post' ? 'active' : '' ?>">
                    <span class="nav-icon">📝</span>
                    Создать пост
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
                <h1 class="page-title"><?= $pageTitle ?></h1>
            </div>
            
            <div class="header-right">
                <button class="theme-toggle" id="themeToggle" title="Переключить тему">
                    <span class="theme-icon-light">🌞</span>
                    <span class="theme-icon-dark" style="display: none;">🌙</span>
                </button>
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
                                Мой профиль
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
            <?php if ($success && $successMsg): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showAlert('<?= htmlspecialchars($successMsg, ENT_QUOTES) ?>', 'success');
                });
            </script>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showAlert('<?= htmlspecialchars($error, ENT_QUOTES) ?>', 'error');
                });
            </script>
            <?php endif; ?>
            
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
                    <form action="/create-process.php" method="POST" enctype="multipart/form-data" id="contentForm">
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
                            <div id="editor-loading" style="background: var(--light); border: 1px solid var(--border); border-radius: 8px; padding: 40px; text-align: center; color: var(--secondary);">
                                <div style="font-size: 2rem; margin-bottom: 10px;">⏳</div>
                                <div>Загрузка редактора...</div>
                            </div>
                            <textarea id="content" name="content" class="form-input tinymce-editor" required
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
                            <input type="file" id="image" name="image" class="form-input" accept="image/*" onchange="previewImage(event)">
                            <div id="imagePreview" style="margin-top: 16px; display: none;">
                                <img id="preview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; box-shadow: var(--shadow);">
                                <button type="button" onclick="removeImage()" style="display: block; margin-top: 8px; padding: 8px 16px; background: var(--danger); color: white; border: none; border-radius: 6px; cursor: pointer;">
                                    ❌ Удалить изображение
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="status">Статус публикации</label>
                            <select id="status" name="status" class="form-select">
                                <option value="published">Опубликовать сразу</option>
                                <option value="draft">Сохранить как черновик</option>
                            </select>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <span id="submitBtnText">⏳ Загрузка редактора...</span>
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

    <!-- TinyMCE Cloud CDN - Free Version -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('overlay');
        const closeBtns = document.querySelectorAll('.close-btn');
        const userMenu = document.getElementById('userMenu');
        const userDropdown = document.getElementById('userDropdown');

        // Toggle sidebar
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('sidebar-open');
            if (window.innerWidth <= 1024) {
                overlay.classList.toggle('active');
            }
        });

        // Close sidebar on overlay click
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            mainContent.classList.remove('sidebar-open');
            overlay.classList.remove('active');
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                overlay.classList.remove('active');
            }
        });

        // User dropdown menu
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

        // Flag to track if TinyMCE is ready
        let editorReady = false;
        
        // Store content to prevent loss
        let lastContent = '';
        
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 400,
            license_key: 'gpl',
            skin: 'oxide',
            content_css: 'default',
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image link | code preview fullscreen | help',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;',
            
            // Fix line breaks - use BR instead of P for Enter key
            forced_root_block: false,
            force_br_newlines: true,
            force_p_newlines: false,
            
            // Image upload
            images_upload_url: '/upload-image.php',
            images_upload_credentials: true,
            images_reuse_filename: true,
            
            // File picker for images
            file_picker_types: 'image',
            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype === 'image') {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    
                    input.onchange = function() {
                        var file = this.files[0];
                        var formData = new FormData();
                        formData.append('file', file);
                        
                        fetch('/upload-image.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.location) {
                                callback(result.location, { alt: file.name });
                            } else {
                                showAlert('Ошибка загрузки изображения: ' + (result.error || 'Неизвестная ошибка'), 'error');
                            }
                        })
                        .catch(error => {
                            showAlert('Ошибка загрузки изображения: ' + error.message, 'error');
                        });
                    };
                    
                    input.click();
                }
            },
            
            // Custom styles for content
            content_style: `
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif; 
                    font-size: 16px; 
                    line-height: 1.6; 
                    color: #333; 
                    max-width: 100%; 
                    margin: 0; 
                    padding: 20px; 
                }
                img { 
                    max-width: 100%; 
                    height: auto; 
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                h1, h2, h3, h4, h5, h6 { 
                    color: #2563eb; 
                    margin-top: 1.5em; 
                    margin-bottom: 0.5em; 
                }
                p { 
                    margin-bottom: 1em; 
                }
                a { 
                    color: #2563eb; 
                    text-decoration: none; 
                }
                a:hover { 
                    text-decoration: underline; 
                }
                blockquote {
                    border-left: 4px solid #2563eb;
                    padding-left: 16px;
                    margin: 16px 0;
                    font-style: italic;
                    background: #f8fafc;
                    padding: 16px;
                    border-radius: 4px;
                }
            `,
            
            // Setup function
            setup: function(editor) {
                editor.on('init', function() {
                    // Hide loading placeholder
                    document.getElementById('editor-loading').style.display = 'none';
                    
                    // Remove required attribute as TinyMCE will handle validation
                    document.getElementById('content').removeAttribute('required');
                    
                    // Get any existing content from textarea
                    const existingContent = document.getElementById('content').value;
                    if (existingContent && existingContent.trim()) {
                        editor.setContent(existingContent);
                    }
                    
                    // Mark editor as ready
                    editorReady = true;
                    console.log('TinyMCE is ready!');
                    
                    // Update submit button
                    const submitBtn = document.getElementById('submitBtn');
                    const submitBtnText = document.getElementById('submitBtnText');
                    const contentType = '<?= $contentType ?>';
                    
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtnText.textContent = contentType === 'news' ? '✅ Создать новость' : '✅ Создать пост';
                    }
                });
                // Save on any change
                editor.on('change', function() {
                    console.log('TinyMCE change event');
                    editor.save();
                    // Store content
                    lastContent = editor.getContent();
                    console.log('Content saved on change:', lastContent);
                    // Also manually update textarea
                    document.getElementById('content').value = lastContent;
                });
                // Save on keyup for better reliability
                editor.on('keyup', function() {
                    editor.save();
                    lastContent = editor.getContent();
                    console.log('Content saved on keyup:', lastContent.substring(0, 50) + '...');
                });
                // Save when editor loses focus
                editor.on('blur', function() {
                    console.log('TinyMCE blur event');
                    editor.save();
                    lastContent = editor.getContent();
                    // Also manually update textarea
                    document.getElementById('content').value = lastContent;
                });
            },
            
            // Toolbar positioning
            toolbar_sticky: true,
            
            // Remove branding
            branding: false,
            
            // Paste settings
            paste_as_text: false,
            paste_data_images: true,
            
            // Link settings
            link_default_target: '_blank',
            link_title: false,
            
            // Table settings
            table_responsive_width: true,
            table_default_attributes: {
                border: '0'
            },
            table_default_styles: {
                'border-collapse': 'collapse',
                width: '100%'
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

        // Alert System Functions
        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alertContainer');
            if (!alertContainer) return;
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `custom-alert alert-${type}`;
            
            const icons = {
                error: '❌',
                success: '✅',
                warning: '⚠️'
            };
            
            alertDiv.innerHTML = `
                <span class="alert-icon">${icons[type]}</span>
                <span class="alert-message">${message}</span>
                <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            `;
            
            alertContainer.appendChild(alertDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                alertDiv.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }

        // Form validation
        const form = document.getElementById('contentForm');
        
        // Prevent default form submission and handle manually
        form.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMIT START ===');
            e.preventDefault(); // Always prevent default first
            
            // Check if TinyMCE is still loading
            if (!editorReady) {
                console.log('Editor not ready');
                showAlert('Пожалуйста, подождите загрузку редактора', 'warning');
                return false;
            }
            
            const title = document.getElementById('title').value.trim();
            console.log('Title:', title);
            
            // Get content from TinyMCE
            const contentEditor = tinymce.get('content');
            let content = '';
            
            console.log('Editor found:', !!contentEditor);
            console.log('Editor hidden:', contentEditor ? contentEditor.isHidden() : 'N/A');
            
            if (contentEditor) {
                console.log('Editor state:', {
                    isHidden: contentEditor.isHidden(),
                    isDirty: contentEditor.isDirty(),
                    initialized: contentEditor.initialized
                });
                
                // Force TinyMCE to save content to textarea
                contentEditor.save();
                
                // Try multiple methods to get content
                content = contentEditor.getContent() || '';
                
                // If editor is hidden, try to get content from DOM directly
                if (!content && contentEditor.isHidden()) {
                    const editorElement = contentEditor.getElement();
                    if (editorElement) {
                        content = editorElement.value || '';
                        console.log('Got content from hidden editor element:', content);
                    }
                }
                
                // Also check the textarea value as backup
                const textareaContent = document.getElementById('content').value || '';
                
                // Also check lastContent variable
                if (!content && lastContent) {
                    content = lastContent;
                    console.log('Using lastContent backup:', content);
                }
                
                // Debug all content sources
                console.log('Content validation debug:');
                console.log('TinyMCE editor found:', !!contentEditor);
                console.log('Editor hidden:', contentEditor.isHidden());
                console.log('Content from getContent():', content);
                console.log('Content from textarea:', textareaContent);
                console.log('Content from lastContent:', lastContent);
                console.log('Editor body innerHTML:', contentEditor.getBody() ? contentEditor.getBody().innerHTML : 'No body');
                
                // Use whichever has content
                if (!content && textareaContent) {
                    content = textareaContent;
                }
                
                // If still no content, try getting from editor body directly
                if (!content && contentEditor.getBody()) {
                    content = contentEditor.getBody().innerHTML || '';
                }
                
                // Remove HTML tags to check if there's actual content
                const textContent = content.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                
                console.log('Final content:', content);
                console.log('Text content:', textContent);
                console.log('Text length:', textContent.length);
                console.log('lastContent backup:', lastContent);
                
                if (!title) {
                    console.log('Title validation failed');
                    showAlert('Пожалуйста, введите заголовок', 'error');
                    document.getElementById('title').focus();
                    return false;
                }
                
                if (!textContent || textContent.length < 1) {
                    console.log('Content validation failed');
                    console.log('Attempting to restore content...');
                    
                    // Try to restore content from backup
                    if (lastContent) {
                        console.log('Restoring from lastContent:', lastContent);
                        contentEditor.setContent(lastContent);
                        // Try validation again
                        const restoredContent = contentEditor.getContent();
                        const restoredText = restoredContent.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                        if (restoredText && restoredText.length > 0) {
                            console.log('Content restored successfully, continuing submission');
                            document.getElementById('content').value = restoredContent;
                            // Continue with submission
                            console.log('=== FORM SUBMIT SUCCESS ===');
                            e.target.submit();
                            return;
                        }
                    }
                    
                    showAlert('Пожалуйста, введите содержание', 'error');
                    contentEditor.focus();
                    return false;
                }
                
                // Ensure the textarea has the content before submission
                document.getElementById('content').value = content;
                
                // All validation passed - manually submit the form
                console.log('=== FORM SUBMIT SUCCESS ===');
                console.log('Submitting with content:', content.substring(0, 100) + '...');
                e.target.submit();
            } else {
                // Fallback if TinyMCE is not loaded
                content = document.getElementById('content').value.trim();
                
                if (!title || !content) {
                    showAlert('Пожалуйста, заполните все обязательные поля', 'error');
                    return false;
                }
                
                // All validation passed - manually submit the form
                e.target.submit();
            }
        });

        // Dark mode toggle
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            const lightIcon = themeToggle.querySelector('.theme-icon-light');
            const darkIcon = themeToggle.querySelector('.theme-icon-dark');
            
            // Check for saved theme preference or default to 'light' mode
            const currentTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            updateThemeIcon(currentTheme);
            
            // Toggle theme
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });
            
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    lightIcon.style.display = 'none';
                    darkIcon.style.display = 'inline';
                } else {
                    lightIcon.style.display = 'inline';
                    darkIcon.style.display = 'none';
                }
            }
        }

        // Image preview functions
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        }
        
        function removeImage() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const preview = document.getElementById('preview');
            
            imageInput.value = '';
            preview.src = '';
            imagePreview.style.display = 'none';
        }
    </script>
</body>
</html>
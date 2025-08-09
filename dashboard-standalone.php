<?php
// Standalone Admin Dashboard - No header/footer template
// Clean, focused dashboard interface

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

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Get statistics
$stats = [
    'news_published' => 0,
    'news_drafts' => 0,
    'posts_total' => 0,
    'schools_total' => 0,
    'vpo_total' => 0,
    'spo_total' => 0,
    'users_total' => 0,
    'comments_total' => 0
];

// Count news
$query = "SELECT approved, COUNT(*) as count FROM news GROUP BY approved";
$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['approved'] == 1) {
            $stats['news_published'] = $row['count'];
        } else {
            $stats['news_drafts'] = $row['count'];
        }
    }
}

// Count other statistics
$tables = ['posts', 'schools', 'universities' => 'vpo', 'colleges' => 'spo', 'users', 'comments'];
foreach ($tables as $table => $key) {
    if (is_numeric($table)) {
        $table = $key;
        $key = $table;
    }
    $query = "SELECT COUNT(*) as count FROM $table";
    $result = $connection->query($query);
    if ($result) {
        $stats["{$key}_total"] = $result->fetch_assoc()['count'];
    }
}

// Get recent activities
$recent_news = [];
$query = "SELECT id, title_news, date_news, approved FROM news ORDER BY date_news DESC LIMIT 5";
$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_news[] = $row;
    }
}

// Get recent users
$recent_users = [];
$query = "SELECT id, email, first_name, last_name, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - 11-классники</title>
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
        
        /* Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }
        
        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .stat-header i {
            font-size: 24px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        
        .stat-info {
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .action-btn {
            background: var(--surface);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .action-btn i {
            font-size: 32px;
            margin-bottom: 12px;
            display: block;
        }
        
        /* Activity Table */
        .activity-section {
            background: var(--surface);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .view-all {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        
        .activity-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: var(--bg-light);
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .activity-row:hover {
            transform: translateX(4px);
        }
        
        .activity-content h4 {
            font-size: 16px;
            margin: 0 0 8px 0;
        }
        
        .activity-meta {
            display: flex;
            gap: 12px;
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--surface);
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .btn-icon:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .btn-icon.delete:hover {
            background: #dc3545;
            border-color: #dc3545;
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-content {
                padding: 20px;
            }
            
            .activity-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
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
                <a href="/dashboard" class="nav-item active">
                    <i class="fas fa-home"></i> Главная
                </a>
                <a href="/dashboard/news" class="nav-item">
                    <i class="fas fa-newspaper"></i> Новости
                </a>
                <a href="/dashboard/posts" class="nav-item">
                    <i class="fas fa-file-alt"></i> Статьи
                </a>
                <a href="/dashboard/users" class="nav-item">
                    <i class="fas fa-users"></i> Пользователи
                </a>
                <a href="/dashboard/comments" class="nav-item">
                    <i class="fas fa-comments"></i> Комментарии
                </a>
                <a href="/dashboard/schools" class="nav-item">
                    <i class="fas fa-school"></i> Школы
                </a>
                <a href="/dashboard/vpo" class="nav-item">
                    <i class="fas fa-graduation-cap"></i> ВУЗы
                </a>
                <a href="/dashboard/spo" class="nav-item">
                    <i class="fas fa-university"></i> СПО
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Top Bar -->
            <div class="dashboard-topbar">
                <div></div> <!-- Empty div for flex spacing -->
                
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
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="/create/news" class="action-btn">
                        <i class="fas fa-plus" style="color: #28a745;"></i>
                        <div>Создать новость</div>
                    </a>
                    <a href="/create/post" class="action-btn">
                        <i class="fas fa-pen" style="color: #17a2b8;"></i>
                        <div>Написать статью</div>
                    </a>
                    <a href="/dashboard/users" class="action-btn">
                        <i class="fas fa-users" style="color: #ffc107;"></i>
                        <div>Пользователи</div>
                    </a>
                    <a href="/dashboard/comments" class="action-btn">
                        <i class="fas fa-comments" style="color: #dc3545;"></i>
                        <div>Комментарии</div>
                    </a>
                </div>
                
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <i class="fas fa-newspaper" style="color: #17a2b8;"></i>
                            <span class="stat-label">Новости</span>
                        </div>
                        <div class="stat-value"><?= number_format($stats['news_published']) ?></div>
                        <div class="stat-info"><?= $stats['news_drafts'] ?> черновиков</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <i class="fas fa-file-alt" style="color: #ffc107;"></i>
                            <span class="stat-label">Статьи</span>
                        </div>
                        <div class="stat-value"><?= number_format($stats['posts_total']) ?></div>
                        <div class="stat-info">Всего опубликовано</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <i class="fas fa-graduation-cap" style="color: #6f42c1;"></i>
                            <span class="stat-label">Учебные заведения</span>
                        </div>
                        <div class="stat-value"><?= number_format($stats['schools_total'] + $stats['vpo_total'] + $stats['spo_total']) ?></div>
                        <div class="stat-info"><?= $stats['schools_total'] ?> школ, <?= $stats['vpo_total'] ?> ВУЗов, <?= $stats['spo_total'] ?> СПО</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <i class="fas fa-users" style="color: #28a745;"></i>
                            <span class="stat-label">Пользователи</span>
                        </div>
                        <div class="stat-value"><?= number_format($stats['users_total']) ?></div>
                        <div class="stat-info">Зарегистрировано</div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="activity-section">
                    <div class="section-header">
                        <h3 class="section-title">Последние новости</h3>
                        <a href="/dashboard/news" class="view-all">Все новости →</a>
                    </div>
                    
                    <?php foreach ($recent_news as $news): ?>
                    <div class="activity-row">
                        <div class="activity-content">
                            <h4><?= htmlspecialchars(mb_substr($news['title_news'], 0, 50)) ?>...</h4>
                            <div class="activity-meta">
                                <span><?= date('d.m.Y', strtotime($news['date_news'])) ?></span>
                                <span><?= $news['approved'] ? 'Опубликовано' : 'Черновик' ?></span>
                            </div>
                        </div>
                        <div>
                            <a href="/edit/news/<?= $news['id'] ?>" class="btn-icon" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteNews(<?= $news['id'] ?>)" class="btn-icon delete" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
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
            window.location.href = '/';
        }
        
        // Delete News
        function deleteNews(id) {
            if (confirm('Вы уверены, что хотите удалить эту новость?')) {
                // Add delete functionality
                console.log('Delete news:', id);
            }
        }
    </script>
</body>
</html>
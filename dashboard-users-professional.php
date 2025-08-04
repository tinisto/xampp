<?php
// Professional Users Dashboard - matches main dashboard design
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

$username = $_SESSION['username'] ?? $_SESSION['email'] ?? 'Admin';

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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Dashboard - 11классники</title>
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

        /* Sidebar */
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

        /* Navigation */
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

        .nav-item:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary);
            border-right: 3px solid transparent;
        }
        
        .nav-item.active {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            border-right: 3px solid var(--primary);
            font-weight: 600;
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
        }

        /* Users Table */
        .users-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .users-card-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .users-card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .users-stats {
            display: flex;
            gap: 16px;
            font-size: 0.875rem;
            color: var(--secondary);
        }

        .table-container {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            background: var(--light);
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border);
        }

        .users-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }

        .users-table tr:hover {
            background: rgba(37, 99, 235, 0.05);
        }

        .user-email {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .user-email:hover {
            text-decoration: underline;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .role-admin {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .role-user {
            background: rgba(100, 116, 139, 0.1);
            color: var(--secondary);
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .action-btn:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .action-btn.delete { color: var(--danger); }
        .action-btn.suspend { color: var(--warning); }
        .action-btn.unsuspend { color: var(--success); }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 24px;
        }

        .page-btn {
            padding: 8px 12px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--secondary);
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-btn:hover, .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.expanded {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="logo">11классники</a>
            <button class="close-btn" onclick="window.location.href='/'">×</button>
        </div>
        
        <nav class="nav">
            <div class="nav-section">
                <div class="nav-section-title">Управление</div>
                <a href="/dashboard" class="nav-item">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>
                <a href="/dashboard/users" class="nav-item active">
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
                <a href="/create/news" class="nav-item">
                    <span class="nav-icon">➕</span>
                    Создать новость
                </a>
                <a href="/create/post" class="nav-item">
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
                <h1 class="page-title">Пользователи</h1>
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
                            <a href="/admin-backup-tool.php" class="dropdown-item">
                                <span class="dropdown-icon">💾</span>
                                Backup
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
            <div class="users-card">
                <div class="users-card-header">
                    <h2 class="users-card-title">Все пользователи</h2>
                    <div class="users-stats">
                        <?php
                        $sqlCount = "SELECT COUNT(*) as total FROM users";
                        $resultCount = $connection->query($sqlCount);
                        $totalUsers = $resultCount ? $resultCount->fetch_assoc()["total"] : 0;
                        echo "<span>Всего: $totalUsers</span>";
                        ?>
                    </div>
                </div>
                
                <div class="table-container">
                    <?php
                    // Pagination variables
                    $perPage = 50;
                    $currentPage = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
                    $offset = ($currentPage - 1) * $perPage;

                    // Query to fetch paginated results
                    $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
                    $result = $connection->query($sql);

                    if ($result && $result->num_rows > 0) {
                        echo '<table class="users-table">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>ID</th>';
                        echo '<th>Имя</th>';
                        echo '<th>Email</th>';
                        echo '<th>Роль</th>';
                        echo '<th>Статус</th>';
                        echo '<th>Регистрация</th>';
                        echo '<th>Действия</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($row = $result->fetch_assoc()) {
                            $isSuspended = ($row["is_suspended"] ?? '0') === "1";
                            $isAdmin = $row["role"] === "admin";
                            
                            echo '<tr>';
                            echo '<td>' . $row["id"] . '</td>';
                            echo '<td>' . ($row["first_name"] ?? '') . ' ' . ($row["last_name"] ?? '') . '</td>';
                            echo '<td><a href="/pages/dashboard/users-dashboard/user.php?id=' . $row["id"] . '" class="user-email">' . $row["email"] . '</a></td>';
                            echo '<td><span class="role-badge role-' . $row["role"] . '">' . ucfirst($row["role"]) . '</span></td>';
                            echo '<td>' . ($isSuspended ? 'Заблокирован' : 'Активен') . '</td>';
                            echo '<td>' . ($row["created_at"] ?? $row["registration_date"] ?? '') . '</td>';
                            
                            if (!$isAdmin) {
                                echo '<td>';
                                echo '<button class="action-btn delete" onclick="deleteUser(' . $row["id"] . ', \'' . addslashes($row["email"]) . '\')" title="Удалить">🗑️</button>';
                                if ($isSuspended) {
                                    echo '<button class="action-btn unsuspend" onclick="unsuspendUser(' . $row["id"] . ', \'' . addslashes($row["email"]) . '\')" title="Разблокировать">✅</button>';
                                } else {
                                    echo '<button class="action-btn suspend" onclick="suspendUser(' . $row["id"] . ', \'' . addslashes($row["email"]) . '\')" title="Заблокировать">⏸️</button>';
                                }
                                echo '</td>';
                            } else {
                                echo '<td>—</td>';
                            }
                            
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';

                        // Pagination
                        $totalPages = ceil($totalUsers / $perPage);
                        if ($totalPages > 1) {
                            echo '<div class="pagination">';
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $activeClass = ($i == $currentPage) ? 'active' : '';
                                echo '<a href="?page=' . $i . '" class="page-btn ' . $activeClass . '">' . $i . '</a>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div style="padding: 40px; text-align: center; color: var(--secondary);">Пользователи не найдены</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

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

        // User actions
        function deleteUser(userId, userEmail) {
            if (confirm('Вы уверены, что хотите удалить пользователя? Email: ' + userEmail)) {
                window.location.href = '/pages/dashboard/users-dashboard/users-view/admin-user-delete.php?id=' + userId;
            }
        }

        function suspendUser(userId, userEmail) {
            if (confirm('Вы уверены, что хотите заблокировать пользователя? Email: ' + userEmail)) {
                window.location.href = '/dashboard/admin-user-suspend.php?id=' + userId;
            }
        }

        function unsuspendUser(userId, userEmail) {
            if (confirm('Вы уверены, что хотите разблокировать пользователя? Email: ' + userEmail)) {
                window.location.href = '/dashboard/admin-user-unsuspend.php?id=' + userId;
            }
        }
    
        // Dark mode toggle
        const themeToggle = document.getElementById('themeToggle');
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
    </script>
</body>
</html>
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

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
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
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--light);
            border-radius: 8px;
            color: var(--dark);
            text-decoration: none;
            transition: background 0.2s;
        }

        .user-menu:hover {
            background: var(--border);
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
                <a href="/dashboard/users" class="nav-item active">
                    <span class="nav-icon">👥</span>
                    Пользователи
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Контент</div>
                <a href="/news" class="nav-item">
                    <span class="nav-icon">📰</span>
                    Новости
                </a>
                <a href="/posts" class="nav-item">
                    <span class="nav-icon">📝</span>
                    Посты
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
                <div class="user-menu">
                    <div class="user-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
                    <span><?= htmlspecialchars($username) ?></span>
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
    </script>
</body>
</html>
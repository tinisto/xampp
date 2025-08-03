<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Check admin authentication
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

// Safe database query function
function safeQuery($connection, $query, $default = 0) {
    try {
        $result = $connection->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return $row;
        }
    } catch (Exception $e) {
        error_log("Dashboard query error: " . $e->getMessage());
    }
    return ['count' => $default];
}

// Get safe statistics
$stats = [
    'users' => safeQuery($connection, "SELECT COUNT(*) as count FROM users"),
    'news' => safeQuery($connection, "SELECT COUNT(*) as count FROM news WHERE status = 'approved'"),
    'schools' => safeQuery($connection, "SELECT COUNT(*) as count FROM schools"),
    'vpo' => safeQuery($connection, "SELECT COUNT(*) as count FROM vpo"),
    'spo' => safeQuery($connection, "SELECT COUNT(*) as count FROM spo"),
    'posts' => safeQuery($connection, "SELECT COUNT(*) as count FROM posts WHERE status = 'published'"),
    'comments' => safeQuery($connection, "SELECT COUNT(*) as count FROM comments WHERE status = 'approved'")
];

// Get pending approvals safely
$pending = [
    'news' => safeQuery($connection, "SELECT COUNT(*) as count FROM news WHERE status = 'pending'"),
    'schools' => safeQuery($connection, "SELECT COUNT(*) as count FROM schools WHERE status = 'pending'"),
    'comments' => safeQuery($connection, "SELECT COUNT(*) as count FROM comments WHERE status = 'pending'")
];
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
            --primary: #28a745;
            --primary-dark: #1e7e34;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --border: #dee2e6;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.5;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.5rem 0;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .btn-outline-light {
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid var(--primary);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.875rem;
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-description {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .pending-badge {
            background: var(--warning);
            color: var(--dark);
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: var(--white);
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-description {
            color: var(--secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            color: white;
        }

        .btn-info {
            background: var(--info);
            color: white;
        }

        .btn-info:hover {
            background: #138496;
            color: white;
        }

        .quick-stats {
            background: var(--white);
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .quick-stats h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .quick-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .quick-stat {
            text-align: center;
            padding: 1rem;
            border-radius: 0.375rem;
            background: var(--light);
        }

        .quick-stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .quick-stat-label {
            font-size: 0.875rem;
            color: var(--secondary);
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .dashboard-title {
                font-size: 1.5rem;
            }

            .stats-grid,
            .actions-grid {
                grid-template-columns: 1fr;
            }

            .stat-number {
                font-size: 2rem;
            }
        }

        .icon-users { background: rgba(23, 162, 184, 0.1); color: var(--info); }
        .icon-news { background: rgba(40, 167, 69, 0.1); color: var(--success); }
        .icon-schools { background: rgba(255, 193, 7, 0.1); color: var(--warning); }
        .icon-posts { background: rgba(108, 117, 125, 0.1); color: var(--secondary); }
        .icon-comments { background: rgba(220, 53, 69, 0.1); color: var(--danger); }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <div class="dashboard-title">
                    🎛️ Admin Dashboard
                </div>
                <div class="user-info">
                    <span>Добро пожаловать, <?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['email']) ?>!</span>
                    <a href="/logout" class="btn btn-outline-light">Выйти</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Пользователи</span>
                    <div class="stat-icon icon-users">👥</div>
                </div>
                <div class="stat-number"><?= number_format($stats['users']['count'] ?? 0) ?></div>
                <div class="stat-description">Всего зарегистрированных</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Новости</span>
                    <div class="stat-icon icon-news">📰</div>
                </div>
                <div class="stat-number"><?= number_format($stats['news']['count'] ?? 0) ?></div>
                <div class="stat-description">
                    Опубликованных статей
                    <?php if (($pending['news']['count'] ?? 0) > 0): ?>
                        <span class="pending-badge"><?= $pending['news']['count'] ?> на модерации</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Школы</span>
                    <div class="stat-icon icon-schools">🏫</div>
                </div>
                <div class="stat-number"><?= number_format($stats['schools']['count'] ?? 0) ?></div>
                <div class="stat-description">
                    Образовательных учреждений
                    <?php if (($pending['schools']['count'] ?? 0) > 0): ?>
                        <span class="pending-badge"><?= $pending['schools']['count'] ?> на модерации</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">ВУЗы</span>
                    <div class="stat-icon icon-schools">🎓</div>
                </div>
                <div class="stat-number"><?= number_format($stats['vpo']['count'] ?? 0) ?></div>
                <div class="stat-description">Высших учебных заведений</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">СПО</span>
                    <div class="stat-icon icon-schools">📚</div>
                </div>
                <div class="stat-number"><?= number_format($stats['spo']['count'] ?? 0) ?></div>
                <div class="stat-description">Средних профессиональных училищ</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Комментарии</span>
                    <div class="stat-icon icon-comments">💬</div>
                </div>
                <div class="stat-number"><?= number_format($stats['comments']['count'] ?? 0) ?></div>
                <div class="stat-description">
                    Одобренных комментариев
                    <?php if (($pending['comments']['count'] ?? 0) > 0): ?>
                        <span class="pending-badge"><?= $pending['comments']['count'] ?> на модерации</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="actions-grid">
            <div class="action-card">
                <div class="action-title">👥 Управление пользователями</div>
                <div class="action-description">
                    Просматривайте, редактируйте и управляйте учетными записями пользователей.
                </div>
                <div class="action-buttons">
                    <a href="/pages/dashboard/users-dashboard/users-view/users-view.php" class="btn btn-primary">Все пользователи</a>
                </div>
            </div>

            <div class="action-card">
                <div class="action-title">📰 Управление новостями</div>
                <div class="action-description">
                    Создавайте, редактируйте и модерируйте новостные статьи.
                </div>
                <div class="action-buttons">
                    <a href="/pages/common/news/news-create.php" class="btn btn-primary">Создать новость</a>
                    <a href="/news" class="btn btn-secondary">Все новости</a>
                </div>
            </div>

            <div class="action-card">
                <div class="action-title">🏫 Образовательные учреждения</div>
                <div class="action-description">
                    Управляйте базой данных школ, ВУЗов и колледжей.
                </div>
                <div class="action-buttons">
                    <a href="/schools-all-regions" class="btn btn-info">Школы</a>
                    <a href="/vpo-all-regions" class="btn btn-info">ВУЗы</a>
                    <a href="/spo-all-regions" class="btn btn-info">СПО</a>
                </div>
            </div>

            <div class="action-card">
                <div class="action-title">📝 Контент</div>
                <div class="action-description">
                    Создавайте и управляйте статьями, постами и другим контентом.
                </div>
                <div class="action-buttons">
                    <a href="/pages/common/create.php" class="btn btn-primary">Создать пост</a>
                    <a href="/write" class="btn btn-secondary">Написать статью</a>
                </div>
            </div>

            <div class="action-card">
                <div class="action-title">💬 Модерация комментариев</div>
                <div class="action-description">
                    Просматривайте и модерируйте пользовательские комментарии.
                </div>
                <div class="action-buttons">
                    <a href="/pages/dashboard/comments-dashboard/comments-view/comments-view.php" class="btn btn-secondary">Все комментарии</a>
                </div>
            </div>

            <div class="action-card">
                <div class="action-title">⚙️ Системные инструменты</div>
                <div class="action-description">
                    Диагностика, настройки и системная информация.
                </div>
                <div class="action-buttons">
                    <a href="/dashboard-debug.php" class="btn btn-secondary">Отладка</a>
                    <a href="#" class="btn btn-secondary" onclick="window.location.reload()">Обновить</a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <h3>📊 Быстрая статистика</h3>
            <div class="quick-stats-grid">
                <div class="quick-stat">
                    <div class="quick-stat-number"><?= $pending['news']['count'] ?? 0 ?></div>
                    <div class="quick-stat-label">Новости на модерации</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-number"><?= $pending['comments']['count'] ?? 0 ?></div>
                    <div class="quick-stat-label">Комментарии на модерации</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-number"><?= $pending['schools']['count'] ?? 0 ?></div>
                    <div class="quick-stat-label">Учреждения на модерации</div>
                </div>
                <div class="quick-stat">
                    <div class="quick-stat-number"><?= date('H:i') ?></div>
                    <div class="quick-stat-label">Текущее время</div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Auto-refresh every 5 minutes
    setTimeout(() => {
        window.location.reload();
    }, 300000);

    // Add loading states to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        if (btn.href && !btn.onclick) {
            btn.addEventListener('click', function() {
                this.style.opacity = '0.7';
                this.innerHTML = '⏳ ' + this.innerHTML;
            });
        }
    });
    </script>
</body>
</html>
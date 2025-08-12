<?php
// Comment Analytics Dashboard
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /unauthorized.php');
    exit;
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get date range
$start_date = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end'] ?? date('Y-m-d');

// Overall statistics
$statsQuery = "SELECT 
    COUNT(*) as total_comments,
    COUNT(DISTINCT user_id) as unique_users,
    COUNT(DISTINCT author_ip) as unique_ips,
    AVG(LENGTH(comment_text)) as avg_comment_length,
    SUM(likes) as total_likes,
    SUM(dislikes) as total_dislikes,
    COUNT(CASE WHEN parent_id IS NOT NULL THEN 1 END) as total_replies,
    COUNT(CASE WHEN edited_at IS NOT NULL THEN 1 END) as edited_comments
    FROM comments 
    WHERE created_at BETWEEN ? AND ?";

$stats = db_fetch_row($statsQuery, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

// Comments per day
$dailyQuery = "SELECT DATE(created_at) as day, COUNT(*) as count 
               FROM comments 
               WHERE created_at BETWEEN ? AND ?
               GROUP BY DATE(created_at)
               ORDER BY day";
$dailyData = db_fetch_all($dailyQuery, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

// Top commenters
$topUsersQuery = "SELECT author_of_comment, COUNT(*) as comment_count, 
                  SUM(likes) as total_likes, user_id
                  FROM comments 
                  WHERE created_at BETWEEN ? AND ?
                  GROUP BY author_of_comment, user_id
                  ORDER BY comment_count DESC
                  LIMIT 10";
$topUsers = db_fetch_all($topUsersQuery, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

// Most discussed entities
$hotEntitiesQuery = "SELECT entity_type, entity_id, COUNT(*) as comment_count
                     FROM comments 
                     WHERE created_at BETWEEN ? AND ?
                     GROUP BY entity_type, entity_id
                     ORDER BY comment_count DESC
                     LIMIT 10";
$hotEntities = db_fetch_all($hotEntitiesQuery, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

// Engagement by hour
$hourlyQuery = "SELECT strftime('%H', created_at) as hour, COUNT(*) as count
                FROM comments 
                WHERE created_at BETWEEN ? AND ?
                GROUP BY strftime('%H', created_at)
                ORDER BY hour";
$hourlyResults = db_fetch_all($hourlyQuery, [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$hourlyData = array_fill(0, 24, 0);
foreach ($hourlyResults as $row) {
    $hourlyData[(int)$row['hour']] = $row['count'];
}

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Аналитика комментариев';

// Build dashboard content
ob_start();
?>
<style>
.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.date-filter {
    display: flex;
    gap: 10px;
    align-items: center;
}

.date-input {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    background: var(--surface);
    color: var(--text-primary);
}

.btn-filter {
    padding: 8px 16px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-filter:hover {
    background: #0056b3;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    text-align: center;
}

.stat-icon {
    font-size: 36px;
    margin-bottom: 10px;
    opacity: 0.8;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 8px;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.chart-container {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    margin-bottom: 20px;
}

.chart-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: var(--text-primary);
}

.chart {
    height: 300px;
    position: relative;
}

.table-container {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    margin-bottom: 20px;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 13px;
    text-transform: uppercase;
}

.data-table tr:hover {
    background: var(--bg-light);
}

.entity-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.entity-posts { background: #e3f2fd; color: #1976d2; }
.entity-spo { background: #f3e5f5; color: #7b1fa2; }
.entity-vpo { background: #e8f5e9; color: #388e3c; }
.entity-school { background: #fff3e0; color: #f57c00; }

.engagement-score {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 12px;
}

.score-high { background: #c8e6c9; color: #2e7d32; }
.score-medium { background: #fff9c4; color: #f9a825; }
.score-low { background: #ffcdd2; color: #c62828; }

@media (max-width: 768px) {
    .analytics-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .date-filter {
        flex-direction: column;
        width: 100%;
    }
    
    .date-input {
        width: 100%;
    }
}
</style>

<div class="analytics-header">
    <h2>Аналитика комментариев</h2>
    <form method="GET" class="date-filter">
        <input type="date" name="start" value="<?= htmlspecialchars($start_date) ?>" class="date-input">
        <span>—</span>
        <input type="date" name="end" value="<?= htmlspecialchars($end_date) ?>" class="date-input">
        <button type="submit" class="btn-filter">Применить</button>
    </form>
</div>

<!-- Key Metrics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-value"><?= number_format($stats['total_comments']) ?></div>
        <div class="stat-label">Всего комментариев</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?= number_format($stats['unique_users']) ?></div>
        <div class="stat-label">Уникальных пользователей</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💕</div>
        <div class="stat-value"><?= number_format($stats['total_likes']) ?></div>
        <div class="stat-label">Лайков</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">↩️</div>
        <div class="stat-value"><?= number_format($stats['total_replies']) ?></div>
        <div class="stat-label">Ответов</div>
    </div>
</div>

<!-- Comments Timeline -->
<div class="chart-container">
    <h3 class="chart-title">Динамика комментариев</h3>
    <canvas id="commentsChart" class="chart"></canvas>
</div>

<!-- Activity by Hour -->
<div class="chart-container">
    <h3 class="chart-title">Активность по часам</h3>
    <canvas id="hourlyChart" class="chart"></canvas>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Top Commenters -->
    <div class="table-container">
        <h3 class="chart-title">Топ комментаторов</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Комментарии</th>
                    <th>Лайки</th>
                    <th>Вовлеченность</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topUsers as $user): 
                    $engagement = $user['comment_count'] > 0 ? round($user['total_likes'] / $user['comment_count'], 1) : 0;
                    $scoreClass = $engagement > 5 ? 'score-high' : ($engagement > 2 ? 'score-medium' : 'score-low');
                ?>
                <tr>
                    <td><?= htmlspecialchars($user['author_of_comment'] ?: 'Аноним') ?></td>
                    <td><?= $user['comment_count'] ?></td>
                    <td><?= $user['total_likes'] ?></td>
                    <td><span class="engagement-score <?= $scoreClass ?>"><?= $engagement ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Hot Topics -->
    <div class="table-container">
        <h3 class="chart-title">Популярные обсуждения</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Тип</th>
                    <th>ID</th>
                    <th>Комментарии</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotEntities as $entity): ?>
                <tr>
                    <td><span class="entity-badge entity-<?= htmlspecialchars($entity['entity_type']) ?>"><?= htmlspecialchars($entity['entity_type']) ?></span></td>
                    <td><?= $entity['entity_id'] ?></td>
                    <td><?= $entity['comment_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Comments Timeline Chart
const commentsCtx = document.getElementById('commentsChart').getContext('2d');
const commentsData = <?= json_encode($dailyData) ?>;

new Chart(commentsCtx, {
    type: 'line',
    data: {
        labels: commentsData.map(d => d.day),
        datasets: [{
            label: 'Комментарии',
            data: commentsData.map(d => d.count),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Hourly Activity Chart
const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
const hourlyData = <?= json_encode($hourlyData) ?>;

new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: Array.from({length: 24}, (_, i) => i + ':00'),
        datasets: [{
            label: 'Комментарии',
            data: hourlyData,
            backgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php
$dashboardContent = ob_get_clean();

// Set active menu
$activeMenu = 'analytics';

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>
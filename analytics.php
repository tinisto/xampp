<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

// Get date range for analytics
$dateFrom = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['to'] ?? date('Y-m-d');
$period = $_GET['period'] ?? '30days';

// Quick period selections
$periodDates = [
    'today' => [date('Y-m-d'), date('Y-m-d')],
    '7days' => [date('Y-m-d', strtotime('-7 days')), date('Y-m-d')],
    '30days' => [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')],
    '90days' => [date('Y-m-d', strtotime('-90 days')), date('Y-m-d')],
    '1year' => [date('Y-m-d', strtotime('-1 year')), date('Y-m-d')]
];

if (isset($periodDates[$period])) {
    [$dateFrom, $dateTo] = $periodDates[$period];
}

// Analytics data collection
$analytics = [
    'overview' => [],
    'content' => [],
    'users' => [],
    'engagement' => [],
    'events' => [],
    'performance' => []
];

// Overview Statistics
$analytics['overview'] = [
    'total_users' => db_fetch_column("SELECT COUNT(*) FROM users WHERE is_active = 1") ?: 0,
    'new_users' => db_fetch_column("SELECT COUNT(*) FROM users WHERE DATE(created_at) BETWEEN ? AND ?", [$dateFrom, $dateTo]) ?: 0,
    'total_content' => (
        (db_fetch_column("SELECT COUNT(*) FROM news WHERE is_published = 1") ?: 0) +
        (db_fetch_column("SELECT COUNT(*) FROM posts WHERE is_published = 1") ?: 0) +
        (db_fetch_column("SELECT COUNT(*) FROM events WHERE is_public = 1") ?: 0)
    ),
    'total_views' => (
        (db_fetch_column("SELECT SUM(views) FROM news") ?: 0) +
        (db_fetch_column("SELECT SUM(views) FROM posts") ?: 0) +
        (db_fetch_column("SELECT SUM(views) FROM events") ?: 0)
    ),
    'active_sessions' => db_fetch_column("SELECT COUNT(DISTINCT user_id) FROM notifications WHERE DATE(created_at) = ?", [date('Y-m-d')]) ?: 0
];

// User Analytics
$analytics['users'] = [
    'registrations_by_day' => db_fetch_all("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM users 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date
    ", [$dateFrom, $dateTo]),
    
    'user_activity' => db_fetch_all("
        SELECT 
            u.name,
            COUNT(DISTINCT f.id) as favorites_count,
            COUNT(DISTINCT c.id) as comments_count,
            COUNT(DISTINCT r.id) as ratings_count,
            COUNT(DISTINCT n.id) as notifications_count
        FROM users u
        LEFT JOIN favorites f ON u.id = f.user_id AND DATE(f.created_at) BETWEEN ? AND ?
        LEFT JOIN comments c ON u.id = c.user_id AND DATE(c.created_at) BETWEEN ? AND ?
        LEFT JOIN ratings r ON u.id = r.user_id AND DATE(r.created_at) BETWEEN ? AND ?
        LEFT JOIN notifications n ON u.id = n.user_id AND DATE(n.created_at) BETWEEN ? AND ?
        WHERE u.is_active = 1
        GROUP BY u.id, u.name
        ORDER BY (favorites_count + comments_count + ratings_count) DESC
        LIMIT 10
    ", [$dateFrom, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo]),
    
    'user_roles' => db_fetch_all("
        SELECT role, COUNT(*) as count 
        FROM users 
        WHERE is_active = 1 
        GROUP BY role
    ")
];

// Content Analytics
$analytics['content'] = [
    'popular_news' => db_fetch_all("
        SELECT title_news as title, views, created_at, url_news as slug
        FROM news 
        WHERE is_published = 1 AND DATE(created_at) BETWEEN ? AND ?
        ORDER BY views DESC 
        LIMIT 10
    ", [$dateFrom, $dateTo]),
    
    'popular_posts' => db_fetch_all("
        SELECT title_post as title, views, date_post as created_at, url_slug as slug
        FROM posts 
        WHERE is_published = 1 AND DATE(date_post) BETWEEN ? AND ?
        ORDER BY views DESC 
        LIMIT 10
    ", [$dateFrom, $dateTo]),
    
    'content_by_category' => db_fetch_all("
        SELECT 
            c.name as category,
            COUNT(CASE WHEN n.id_news IS NOT NULL THEN 1 END) as news_count,
            COUNT(CASE WHEN p.id IS NOT NULL THEN 1 END) as posts_count,
            SUM(COALESCE(n.views, 0) + COALESCE(p.views, 0)) as total_views
        FROM categories c
        LEFT JOIN news n ON c.id = n.category_id AND n.is_published = 1
        LEFT JOIN posts p ON c.id = p.category AND p.is_published = 1
        GROUP BY c.id, c.name
        ORDER BY total_views DESC
    "),
    
    'publishing_trends' => db_fetch_all("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as count,
            'news' as type
        FROM news 
        WHERE is_published = 1 AND DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        UNION ALL
        SELECT 
            DATE(date_post) as date,
            COUNT(*) as count,
            'posts' as type
        FROM posts 
        WHERE is_published = 1 AND DATE(date_post) BETWEEN ? AND ?
        GROUP BY DATE(date_post)
        ORDER BY date
    ", [$dateFrom, $dateTo, $dateFrom, $dateTo])
];

// Engagement Analytics  
$analytics['engagement'] = [
    'favorites_trends' => db_fetch_all("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM favorites 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date
    ", [$dateFrom, $dateTo]),
    
    'comments_trends' => db_fetch_all("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM comments 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date
    ", [$dateFrom, $dateTo]),
    
    'ratings_distribution' => db_fetch_all("
        SELECT rating, COUNT(*) as count
        FROM ratings
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY rating
        ORDER BY rating
    ", [$dateFrom, $dateTo]),
    
    'most_favorited' => db_fetch_all("
        SELECT 
            f.item_type,
            f.item_id,
            COUNT(*) as favorites_count,
            CASE 
                WHEN f.item_type = 'news' THEN n.title_news
                WHEN f.item_type = 'post' THEN p.title_post
            END as title
        FROM favorites f
        LEFT JOIN news n ON f.item_type = 'news' AND f.item_id = n.id_news
        LEFT JOIN posts p ON f.item_type = 'post' AND f.item_id = p.id
        WHERE DATE(f.created_at) BETWEEN ? AND ?
        GROUP BY f.item_type, f.item_id
        ORDER BY favorites_count DESC
        LIMIT 10
    ", [$dateFrom, $dateTo])
];

// Events Analytics
$analytics['events'] = [
    'events_by_type' => db_fetch_all("
        SELECT event_type, COUNT(*) as count
        FROM events 
        WHERE is_public = 1 AND DATE(created_at) BETWEEN ? AND ?
        GROUP BY event_type
        ORDER BY count DESC
    ", [$dateFrom, $dateTo]),
    
    'event_subscriptions' => db_fetch_all("
        SELECT 
            e.title,
            e.event_type,
            e.start_date,
            COUNT(es.id) as subscription_count
        FROM events e
        LEFT JOIN event_subscriptions es ON e.id = es.event_id
        WHERE e.is_public = 1 AND DATE(e.created_at) BETWEEN ? AND ?
        GROUP BY e.id
        ORDER BY subscription_count DESC
        LIMIT 10
    ", [$dateFrom, $dateTo]),
    
    'upcoming_events' => db_fetch_all("
        SELECT title, start_date, start_time, event_type, views
        FROM events 
        WHERE is_public = 1 AND start_date >= CURRENT_DATE
        ORDER BY start_date ASC
        LIMIT 10
    ")
];

// Performance Analytics
$analytics['performance'] = [
    'reading_lists_usage' => [
        'total_lists' => db_fetch_column("SELECT COUNT(*) FROM reading_lists") ?: 0,
        'active_lists' => db_fetch_column("
            SELECT COUNT(DISTINCT rl.id) 
            FROM reading_lists rl 
            JOIN reading_list_items rli ON rl.id = rli.list_id
        ") ?: 0,
        'average_items' => db_fetch_column("
            SELECT AVG(item_count) 
            FROM (
                SELECT COUNT(*) as item_count 
                FROM reading_list_items 
                GROUP BY list_id
            ) as list_counts
        ") ?: 0
    ],
    
    'notifications_stats' => [
        'total_sent' => db_fetch_column("SELECT COUNT(*) FROM notifications WHERE DATE(created_at) BETWEEN ? AND ?", [$dateFrom, $dateTo]) ?: 0,
        'read_rate' => db_fetch_column("
            SELECT ROUND(
                (COUNT(CASE WHEN is_read = 1 THEN 1 END) * 100.0) / COUNT(*), 2
            ) 
            FROM notifications 
            WHERE DATE(created_at) BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]) ?: 0,
        'by_type' => db_fetch_all("
            SELECT type, COUNT(*) as count
            FROM notifications 
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY type
            ORDER BY count DESC
        ", [$dateFrom, $dateTo])
    ]
];

$pageTitle = 'Аналитика и статистика';

// Section 1: Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 10px;">
        <i class="fas fa-chart-line"></i> Аналитика и статистика
    </h1>
    <p style="font-size: 18px; opacity: 0.9;">Подробная аналитика использования портала</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Filters and date range
ob_start();
?>
<div style="padding: 30px 20px; background: var(--bg-secondary);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <h2 style="margin: 0;">📊 Панель аналитики</h2>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <!-- Quick period buttons -->
                <div style="display: flex; gap: 5px;">
                    <?php foreach (['today' => 'Сегодня', '7days' => '7 дней', '30days' => '30 дней', '90days' => '90 дней', '1year' => 'Год'] as $p => $label): ?>
                    <a href="?period=<?= $p ?>" 
                       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 600;
                              background: <?= $period === $p ? '#6f42c1' : 'var(--bg-primary)' ?>; 
                              color: <?= $period === $p ? 'white' : 'var(--text-primary)' ?>; 
                              border: 1px solid var(--border-color);">
                        <?= $label ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Custom date range -->
                <form method="get" style="display: flex; gap: 10px; align-items: center;">
                    <input type="date" name="from" value="<?= $dateFrom ?>" 
                           style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    <span>—</span>
                    <input type="date" name="to" value="<?= $dateTo ?>" 
                           style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    <button type="submit" 
                            style="background: #6f42c1; color: white; border: none; padding: 8px 16px; 
                                   border-radius: 6px; font-weight: 600; cursor: pointer;">
                        Применить
                    </button>
                </form>
            </div>
        </div>
        
        <div style="margin-top: 20px; color: var(--text-secondary);">
            <i class="fas fa-calendar"></i> Период: <?= date('d.m.Y', strtotime($dateFrom)) ?> — <?= date('d.m.Y', strtotime($dateTo)) ?>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Overview Cards
ob_start();
?>
<div style="padding: 40px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <!-- Overview Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 40px;">
            <div style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-users" style="font-size: 36px; margin-bottom: 15px; opacity: 0.9;"></i>
                <div style="font-size: 32px; font-weight: 700; margin-bottom: 8px;">
                    <?= number_format($analytics['overview']['total_users']) ?>
                </div>
                <div style="font-size: 16px; opacity: 0.9;">Всего пользователей</div>
                <div style="font-size: 14px; margin-top: 10px; opacity: 0.8;">
                    +<?= $analytics['overview']['new_users'] ?> за период
                </div>
            </div>
            
            <div style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white; padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-file-alt" style="font-size: 36px; margin-bottom: 15px; opacity: 0.9;"></i>
                <div style="font-size: 32px; font-weight: 700; margin-bottom: 8px;">
                    <?= number_format($analytics['overview']['total_content']) ?>
                </div>
                <div style="font-size: 16px; opacity: 0.9;">Всего материалов</div>
                <div style="font-size: 14px; margin-top: 10px; opacity: 0.8;">
                    Новости + Статьи + События
                </div>
            </div>
            
            <div style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #212529; padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-eye" style="font-size: 36px; margin-bottom: 15px; opacity: 0.9;"></i>
                <div style="font-size: 32px; font-weight: 700; margin-bottom: 8px;">
                    <?= number_format($analytics['overview']['total_views']) ?>
                </div>
                <div style="font-size: 16px; opacity: 0.9;">Всего просмотров</div>
                <div style="font-size: 14px; margin-top: 10px; opacity: 0.8;">
                    Суммарно по всем материалам
                </div>
            </div>
            
            <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; border-radius: 12px; text-align: center;">
                <i class="fas fa-chart-line" style="font-size: 36px; margin-bottom: 15px; opacity: 0.9;"></i>
                <div style="font-size: 32px; font-weight: 700; margin-bottom: 8px;">
                    <?= number_format($analytics['overview']['active_sessions']) ?>
                </div>
                <div style="font-size: 16px; opacity: 0.9;">Активные сегодня</div>
                <div style="font-size: 14px; margin-top: 10px; opacity: 0.8;">
                    Пользователи с активностью
                </div>
            </div>
        </div>
        
        <!-- Charts and detailed analytics -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
            
            <!-- User Registrations Chart -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
                    <i class="fas fa-user-plus" style="color: #007bff;"></i> Регистрации по дням
                </h3>
                <canvas id="registrationsChart" style="max-height: 300px;"></canvas>
            </div>
            
            <!-- Content Publishing Trends -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
                    <i class="fas fa-newspaper" style="color: #28a745;"></i> Публикации контента
                </h3>
                <canvas id="publishingChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        
        <!-- Engagement Analytics -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
            
            <!-- Engagement Trends -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
                    <i class="fas fa-heart" style="color: #dc3545;"></i> Активность пользователей
                </h3>
                <canvas id="engagementChart" style="max-height: 300px;"></canvas>
            </div>
            
            <!-- Ratings Distribution -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
                    <i class="fas fa-star" style="color: #ffc107;"></i> Распределение оценок
                </h3>
                <canvas id="ratingsChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        
        <!-- Detailed Tables -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <!-- Popular Content -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
                    <i class="fas fa-fire" style="color: #fd7e14;"></i> Популярный контент
                </h3>
                
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 16px; color: var(--text-secondary);">Новости:</h4>
                    <?php if (empty($analytics['content']['popular_news'])): ?>
                    <p style="color: var(--text-secondary); font-style: italic;">Нет данных за период</p>
                    <?php else: ?>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <?php foreach (array_slice($analytics['content']['popular_news'], 0, 5) as $news): ?>
                        <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                            <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">
                                <?= htmlspecialchars(mb_substr($news['title'], 0, 50)) ?>...
                            </div>
                            <div style="font-size: 12px; color: var(--text-secondary);">
                                👁️ <?= number_format($news['views']) ?> просмотров
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <h4 style="margin: 0 0 10px 0; font-size: 16px; color: var(--text-secondary);">Статьи:</h4>
                    <?php if (empty($analytics['content']['popular_posts'])): ?>
                    <p style="color: var(--text-secondary); font-style: italic;">Нет данных за период</p>
                    <?php else: ?>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <?php foreach (array_slice($analytics['content']['popular_posts'], 0, 5) as $post): ?>
                        <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                            <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">
                                <?= htmlspecialchars(mb_substr($post['title'], 0, 50)) ?>...
                            </div>
                            <div style="font-size: 12px; color: var(--text-secondary);">
                                👁️ <?= number_format($post['views']) ?> просмотров
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Active Users -->
            <div style="background: var(--bg-primary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin: 0 0 20px 0; color: var(--text-primary);">
                    <i class="fas fa-medal" style="color: #ffc107;"></i> Самые активные пользователи
                </h3>
                
                <?php if (empty($analytics['users']['user_activity'])): ?>
                <p style="color: var(--text-secondary); font-style: italic;">Нет данных за период</p>
                <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($analytics['users']['user_activity'] as $index => $user): ?>
                    <?php 
                    $totalActivity = $user['favorites_count'] + $user['comments_count'] + $user['ratings_count'];
                    if ($totalActivity == 0) continue;
                    ?>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #007bff, #0056b3); 
                                   color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
                                   font-weight: 700; font-size: 12px;">
                            <?= $index + 1 ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; margin-bottom: 4px;">
                                <?= htmlspecialchars($user['name']) ?>
                            </div>
                            <div style="font-size: 12px; color: var(--text-secondary); display: flex; gap: 10px;">
                                <span>❤️ <?= $user['favorites_count'] ?></span>
                                <span>💬 <?= $user['comments_count'] ?></span>
                                <span>⭐ <?= $user['ratings_count'] ?></span>
                            </div>
                        </div>
                        <div style="font-size: 18px; font-weight: 700; color: #007bff;">
                            <?= $totalActivity ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js configuration
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text-secondary');

// User Registrations Chart
const registrationsData = <?= json_encode($analytics['users']['registrations_by_day']) ?>;
const registrationsCtx = document.getElementById('registrationsChart').getContext('2d');

new Chart(registrationsCtx, {
    type: 'line',
    data: {
        labels: registrationsData.map(d => new Date(d.date).toLocaleDateString('ru-RU')),
        datasets: [{
            label: 'Регистрации',
            data: registrationsData.map(d => d.count),
            borderColor: '#007bff',
            backgroundColor: '#007bff33',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Publishing Trends Chart  
const publishingData = <?= json_encode($analytics['content']['publishing_trends']) ?>;
const dates = [...new Set(publishingData.map(d => d.date))].sort();
const newsData = dates.map(date => {
    const item = publishingData.find(d => d.date === date && d.type === 'news');
    return item ? item.count : 0;
});
const postsData = dates.map(date => {
    const item = publishingData.find(d => d.date === date && d.type === 'posts');
    return item ? item.count : 0;
});

const publishingCtx = document.getElementById('publishingChart').getContext('2d');
new Chart(publishingCtx, {
    type: 'bar',
    data: {
        labels: dates.map(d => new Date(d).toLocaleDateString('ru-RU')),
        datasets: [{
            label: 'Новости',
            data: newsData,
            backgroundColor: '#007bff'
        }, {
            label: 'Статьи',
            data: postsData,
            backgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { stacked: true },
            y: { 
                stacked: true,
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Engagement Chart
const favoritesData = <?= json_encode($analytics['engagement']['favorites_trends']) ?>;
const commentsData = <?= json_encode($analytics['engagement']['comments_trends']) ?>;

const allDates = [...new Set([
    ...favoritesData.map(d => d.date),
    ...commentsData.map(d => d.date)
])].sort();

const favoritesCount = allDates.map(date => {
    const item = favoritesData.find(d => d.date === date);
    return item ? item.count : 0;
});

const commentsCount = allDates.map(date => {
    const item = commentsData.find(d => d.date === date);
    return item ? item.count : 0;
});

const engagementCtx = document.getElementById('engagementChart').getContext('2d');
new Chart(engagementCtx, {
    type: 'line',
    data: {
        labels: allDates.map(d => new Date(d).toLocaleDateString('ru-RU')),
        datasets: [{
            label: 'Избранное',
            data: favoritesCount,
            borderColor: '#dc3545',
            backgroundColor: '#dc354533',
            fill: false
        }, {
            label: 'Комментарии',
            data: commentsCount,
            borderColor: '#28a745',
            backgroundColor: '#28a74533',
            fill: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Ratings Distribution Chart
const ratingsData = <?= json_encode($analytics['engagement']['ratings_distribution']) ?>;
const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');

new Chart(ratingsCtx, {
    type: 'doughnut',
    data: {
        labels: ratingsData.map(d => `${d.rating} звезд`),
        datasets: [{
            data: ratingsData.map(d => d.count),
            backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#007bff']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
<?php
$greyContent3 = ob_get_clean();

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
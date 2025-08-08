<?php
// Modern Admin Dashboard with Responsive Design
// Enhanced UI/UX with tablet optimization

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

// Get statistics with error handling
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

// Count posts
$query = "SELECT COUNT(*) as count FROM posts";
$result = $connection->query($query);
if ($result) {
    $stats['posts_total'] = $result->fetch_assoc()['count'];
}

// Count educational institutions
$tables = ['schools', 'universities' => 'vpo', 'colleges' => 'spo', 'users', 'comments'];
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

// Section 1: Welcome Header
ob_start();
?>
<div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; padding: 40px 20px; border-radius: 16px; margin: 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="font-size: 32px; margin: 0 0 10px 0; font-weight: 600;">
            Панель управления
        </h1>
        <p style="font-size: 18px; opacity: 0.9; margin: 0;">
            Добро пожаловать, <?= htmlspecialchars($username) ?>! Управляйте контентом и пользователями вашего сайта.
        </p>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Quick Actions Grid
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <h2 style="font-size: 24px; margin-bottom: 20px; color: #333;">Быстрые действия</h2>
    
    <div class="quick-actions-grid">
        <a href="/create/news" class="action-card">
            <div class="action-icon" style="background: #28a745;">
                <i class="fas fa-plus"></i>
            </div>
            <h3>Создать новость</h3>
            <p>Добавить новую статью или объявление</p>
        </a>
        
        <a href="/create/post" class="action-card">
            <div class="action-icon" style="background: #17a2b8;">
                <i class="fas fa-pen"></i>
            </div>
            <h3>Создать статью</h3>
            <p>Написать новый материал для блога</p>
        </a>
        
        <a href="/dashboard/users" class="action-card">
            <div class="action-icon" style="background: #ffc107;">
                <i class="fas fa-users"></i>
            </div>
            <h3>Пользователи</h3>
            <p>Управление учетными записями</p>
        </a>
        
        <a href="/dashboard/comments" class="action-card">
            <div class="action-icon" style="background: #dc3545;">
                <i class="fas fa-comments"></i>
            </div>
            <h3>Комментарии</h3>
            <p>Модерация комментариев</p>
        </a>
    </div>
</div>

<style>
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.action-card {
    background: var(--surface, #fff);
    border-radius: 12px;
    padding: 24px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color, #e9ecef);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #007bff;
}

.action-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 16px;
}

.action-card h3 {
    font-size: 18px;
    margin: 0 0 8px 0;
    color: #333;
}

.action-card p {
    font-size: 14px;
    color: #666;
    margin: 0;
    line-height: 1.5;
}

/* Tablet optimization */
@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .action-card {
        padding: 20px;
    }
}

/* Mobile optimization */
@media (max-width: 480px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Statistics Dashboard
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <h2 style="font-size: 24px; margin-bottom: 20px; color: #333;">Статистика сайта</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-newspaper" style="color: #17a2b8;"></i>
                <span class="stat-label">Новости</span>
            </div>
            <div class="stat-value"><?= number_format($stats['news_published']) ?></div>
            <div class="stat-info">
                <span class="stat-badge"><?= $stats['news_drafts'] ?> черновиков</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-file-alt" style="color: #ffc107;"></i>
                <span class="stat-label">Статьи</span>
            </div>
            <div class="stat-value"><?= number_format($stats['posts_total']) ?></div>
            <div class="stat-info">
                <span class="stat-trend up">+12% за месяц</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-graduation-cap" style="color: #6f42c1;"></i>
                <span class="stat-label">Учебные заведения</span>
            </div>
            <div class="stat-value"><?= number_format($stats['schools_total'] + $stats['vpo_total'] + $stats['spo_total']) ?></div>
            <div class="stat-info">
                <span class="stat-detail"><?= $stats['schools_total'] ?> школ, <?= $stats['vpo_total'] ?> ВУЗов</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <i class="fas fa-users" style="color: #28a745;"></i>
                <span class="stat-label">Пользователи</span>
            </div>
            <div class="stat-value"><?= number_format($stats['users_total']) ?></div>
            <div class="stat-info">
                <span class="stat-trend up">+5 новых сегодня</span>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--surface, #fff);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color, #e9ecef);
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
    color: #666;
    font-weight: 500;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
}

.stat-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-badge {
    background: #f8f9fa;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    color: #666;
}

.stat-trend {
    font-size: 13px;
    font-weight: 500;
}

.stat-trend.up {
    color: #28a745;
}

.stat-trend.up::before {
    content: "↑ ";
}

.stat-detail {
    font-size: 13px;
    color: #666;
}

/* Dark mode support */
[data-theme="dark"] .stat-card {
    background: var(--surface-dark, #2d3748);
    border-color: var(--border-color-dark, #4a5568);
}

[data-theme="dark"] .stat-value {
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] .stat-badge {
    background: var(--surface-darker, #1a202c);
    color: var(--text-secondary, #a0aec0);
}

/* Tablet optimization */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .stat-value {
        font-size: 28px;
    }
}

/* Mobile optimization */
@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Empty
$greyContent4 = '';

// Section 5: Recent Activity Tables
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div class="activity-grid">
        <!-- Recent News -->
        <div class="activity-section">
            <div class="section-header">
                <h3>Последние новости</h3>
                <a href="/dashboard/news" class="view-all">Все новости →</a>
            </div>
            
            <div class="activity-table">
                <?php foreach ($recent_news as $news): ?>
                <div class="activity-row">
                    <div class="activity-content">
                        <h4><?= htmlspecialchars(mb_substr($news['title_news'], 0, 50)) ?>...</h4>
                        <div class="activity-meta">
                            <span class="date"><?= date('d.m.Y', strtotime($news['date_news'])) ?></span>
                            <span class="status <?= $news['approved'] ? 'published' : 'draft' ?>">
                                <?= $news['approved'] ? 'Опубликовано' : 'Черновик' ?>
                            </span>
                        </div>
                    </div>
                    <div class="activity-actions">
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
        
        <!-- Recent Users -->
        <div class="activity-section">
            <div class="section-header">
                <h3>Новые пользователи</h3>
                <a href="/dashboard/users" class="view-all">Все пользователи →</a>
            </div>
            
            <div class="activity-table">
                <?php foreach ($recent_users as $user): ?>
                <div class="activity-row">
                    <div class="activity-content">
                        <h4><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                        <div class="activity-meta">
                            <span class="email"><?= htmlspecialchars($user['email']) ?></span>
                            <span class="date"><?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
                        </div>
                    </div>
                    <div class="activity-actions">
                        <a href="/dashboard/users?id=<?= $user['id'] ?>" class="btn-icon" title="Просмотр">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
}

.activity-section {
    background: var(--surface, #fff);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color, #e9ecef);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    font-size: 20px;
    margin: 0;
    color: #333;
}

.view-all {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.3s;
}

.view-all:hover {
    color: #0056b3;
}

.activity-table {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.activity-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s;
}

.activity-row:hover {
    background: #e9ecef;
    transform: translateX(4px);
}

.activity-content h4 {
    font-size: 16px;
    margin: 0 0 8px 0;
    color: #333;
}

.activity-meta {
    display: flex;
    gap: 12px;
    font-size: 13px;
}

.activity-meta .date {
    color: #666;
}

.activity-meta .email {
    color: #007bff;
}

.status {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status.published {
    background: #d4edda;
    color: #155724;
}

.status.draft {
    background: #fff3cd;
    color: #856404;
}

.activity-actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    background: white;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s;
    cursor: pointer;
}

.btn-icon:hover {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-icon.delete:hover {
    background: #dc3545;
    border-color: #dc3545;
}

/* Dark mode support */
[data-theme="dark"] .activity-section {
    background: var(--surface-dark, #2d3748);
    border-color: var(--border-color-dark, #4a5568);
}

[data-theme="dark"] .section-header h3 {
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] .activity-row {
    background: var(--surface-darker, #1a202c);
}

[data-theme="dark"] .activity-row:hover {
    background: var(--surface-dark, #2d3748);
}

[data-theme="dark"] .activity-content h4 {
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] .btn-icon {
    background: var(--surface-dark, #2d3748);
    border-color: var(--border-color-dark, #4a5568);
}

/* Tablet optimization */
@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .activity-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .activity-actions {
        align-self: flex-end;
    }
}

/* Mobile optimization */
@media (max-width: 480px) {
    .activity-section {
        padding: 16px;
    }
    
    .section-header h3 {
        font-size: 18px;
    }
    
    .activity-content h4 {
        font-size: 14px;
    }
}
</style>

<script>
function deleteNews(id) {
    if (confirm('Вы уверены, что хотите удалить эту новость?')) {
        fetch(`/api/news/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?? '' ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка при удалении: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            alert('Ошибка при удалении: ' + error.message);
        });
    }
}
</script>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Панель управления - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
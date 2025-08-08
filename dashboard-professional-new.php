<?php
// Admin dashboard - migrated to use real_template.php

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

// Get statistics
$stats = [];

// Count news
$query = "SELECT approved, COUNT(*) as count FROM news GROUP BY approved";
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
    if ($row['approved'] == 1) {
        $stats['news_published'] = $row['count'];
    } else {
        $stats['news_drafts'] = $row['count'];
    }
}

// Count posts
$query = "SELECT COUNT(*) as count FROM posts";
$result = $connection->query($query);
$stats['posts_total'] = $result->fetch_assoc()['count'];

// Count schools
$query = "SELECT COUNT(*) as count FROM schools";
$result = $connection->query($query);
$stats['schools_total'] = $result->fetch_assoc()['count'];

// Count universities
$query = "SELECT COUNT(*) as count FROM universities";
$result = $connection->query($query);
$stats['vpo_total'] = $result->fetch_assoc()['count'];

// Count colleges
$query = "SELECT COUNT(*) as count FROM colleges";
$result = $connection->query($query);
$stats['spo_total'] = $result->fetch_assoc()['count'];

// Count users
$query = "SELECT COUNT(*) as count FROM users";
$result = $connection->query($query);
$stats['users_total'] = $result->fetch_assoc()['count'];

// Count comments (simplified - no status column exists)
$query = "SELECT COUNT(*) as count FROM comments";
$result = $connection->query($query);
if ($result) {
    $stats['comments_total'] = $result->fetch_assoc()['count'];
} else {
    $stats['comments_total'] = 0;
}

// Get recent activities
$recent_news = [];
$query = "SELECT id, title_news, date_news, approved FROM news ORDER BY date_news DESC LIMIT 5";
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
    $recent_news[] = $row;
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Панель управления', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Добро пожаловать, ' . htmlspecialchars($username)
]);
$greyContent1 = ob_get_clean();

// Section 2: Quick actions
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="/create/news" class="quick-action-btn">
            <i class="fas fa-plus"></i> Создать новость
        </a>
        <a href="/create/post" class="quick-action-btn">
            <i class="fas fa-plus"></i> Создать статью
        </a>
        <a href="/dashboard/users" class="quick-action-btn">
            <i class="fas fa-users"></i> Пользователи
        </a>
        <a href="/dashboard/comments" class="quick-action-btn">
            <i class="fas fa-comments"></i> Комментарии
        </a>
    </div>
</div>

<style>
.quick-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s;
}

.quick-action-btn:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Statistics grid
ob_start();
?>
<div style="padding: 20px;">
    <h3 style="text-align: center; margin-bottom: 30px; color: var(--text-primary, #333);">Статистика сайта</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto;">
        
        <div class="stat-card">
            <i class="fas fa-newspaper" style="color: #17a2b8;"></i>
            <h4><?= $stats['news_published'] ?? 0 ?></h4>
            <p>Опубликованных новостей</p>
            <small><?= $stats['news_drafts'] ?? 0 ?> черновиков</small>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-file-alt" style="color: #ffc107;"></i>
            <h4><?= $stats['posts_total'] ?? 0 ?></h4>
            <p>Статей</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-school" style="color: #dc3545;"></i>
            <h4><?= $stats['schools_total'] ?? 0 ?></h4>
            <p>Школ</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-graduation-cap" style="color: #6f42c1;"></i>
            <h4><?= $stats['vpo_total'] ?? 0 ?></h4>
            <p>ВПО</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-university" style="color: #e83e8c;"></i>
            <h4><?= $stats['spo_total'] ?? 0 ?></h4>
            <p>СПО</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-users" style="color: #20c997;"></i>
            <h4><?= $stats['users_total'] ?? 0 ?></h4>
            <p>Пользователей</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-comments" style="color: #28a745;"></i>
            <h4><?= $stats['comments_total'] ?? 0 ?></h4>
            <p>Всего комментариев</p>
        </div>
        
    </div>
</div>

<style>
.stat-card {
    background: var(--surface, #ffffff);
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card i {
    font-size: 36px;
    margin-bottom: 15px;
}

.stat-card h4 {
    color: var(--text-primary, #333);
    font-size: 32px;
    margin: 10px 0;
}

.stat-card p {
    color: var(--text-secondary, #666);
    margin: 5px 0;
}

.stat-card small {
    color: #999;
    font-size: 14px;
}

[data-theme="dark"] .stat-card {
    background: var(--surface-dark, #2d3748);
}
</style>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Empty
$greyContent4 = '';

// Section 5: Recent activity
ob_start();
?>
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    <h3 style="text-align: center; margin-bottom: 30px; color: var(--text-primary, #333);">Последние новости</h3>
    
    <div style="background: var(--surface, #ffffff); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; color: #666;">ID</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Заголовок</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Дата</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Статус</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_news as $news): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?= $news['id'] ?></td>
                    <td style="padding: 15px;">
                        <a href="/news/<?= $news['id'] ?>" style="color: #28a745; text-decoration: none;">
                            <?= htmlspecialchars($news['title_news']) ?>
                        </a>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= date('d.m.Y', strtotime($news['date_news'])) ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($news['approved'] == 1): ?>
                            <span style="color: #28a745;">Опубликовано</span>
                        <?php else: ?>
                            <span style="color: #ffc107;">Черновик</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <a href="/edit/news/<?= $news['id'] ?>" style="color: #007bff;">Редактировать</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="/dashboard/news" class="view-all-btn">
            Все новости <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<style>
.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.view-all-btn:hover {
    background: #0056b3;
    transform: translateX(5px);
}

[data-theme="dark"] table {
    background: var(--surface-dark, #2d3748);
}

[data-theme="dark"] tr[style*="background: #f8f9fa"] {
    background: var(--surface-darker, #1a202c) !important;
}

[data-theme="dark"] td,
[data-theme="dark"] th {
    color: var(--text-primary, #e4e6eb);
    border-color: #4a5568 !important;
}
</style>
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
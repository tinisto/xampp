<?php
// News management dashboard - migrated to use real_template.php

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

// Get filter and pagination
$filter = $_GET['filter'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Build filter condition
$filterCondition = '';
switch ($filter) {
    case 'published':
        $filterCondition = 'WHERE approved = 1';
        break;
    case 'drafts':
        $filterCondition = 'WHERE approved = 0';
        break;
}

// Search
$search = $_GET['search'] ?? '';
$searchCondition = '';
if (!empty($search)) {
    $searchLike = '%' . $connection->real_escape_string($search) . '%';
    $searchCondition = $filterCondition ? ' AND ' : 'WHERE ';
    $searchCondition .= "title_news LIKE '$searchLike'";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM news $filterCondition $searchCondition";
$countResult = $connection->query($countQuery);
$totalNews = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalNews / $limit);

// Get news
$query = "SELECT n.*, c.title_category 
          FROM news n
          LEFT JOIN categories c ON n.category_news = c.id_category
          $filterCondition $searchCondition
          ORDER BY n.date_news DESC 
          LIMIT $limit OFFSET $offset";
$result = $connection->query($query);
$newsItems = [];
while ($row = $result->fetch_assoc()) {
    $newsItems[] = $row;
}

// Get statistics
$stats = [];
$query = "SELECT approved, COUNT(*) as count FROM news GROUP BY approved";
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
    if ($row['approved'] == 1) {
        $stats['published'] = $row['count'];
    } else {
        $stats['drafts'] = $row['count'];
    }
}
$stats['total'] = ($stats['published'] ?? 0) + ($stats['drafts'] ?? 0);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Управление новостями', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'Всего новостей: ' . $stats['total']
]);
$greyContent1 = ob_get_clean();

// Section 2: Filters and actions
ob_start();
?>
<div style="padding: 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Filter tabs -->
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
            <a href="/dashboard/news" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                Все (<?= $stats['total'] ?>)
            </a>
            <a href="/dashboard/news?filter=published" class="filter-tab <?= $filter === 'published' ? 'active' : '' ?>">
                Опубликованные (<?= $stats['published'] ?? 0 ?>)
            </a>
            <a href="/dashboard/news?filter=drafts" class="filter-tab <?= $filter === 'drafts' ? 'active' : '' ?>">
                Черновики (<?= $stats['drafts'] ?? 0 ?>)
            </a>
        </div>
        
        <!-- Search and actions -->
        <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <form method="GET" style="flex: 1; max-width: 400px;">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Поиск новостей..." 
                           style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; 
                                                 border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <div style="display: flex; gap: 10px;">
                <a href="/create/news" class="action-btn primary">
                    <i class="fas fa-plus"></i> Создать новость
                </a>
                <a href="/dashboard" class="action-btn secondary">
                    <i class="fas fa-arrow-left"></i> К панели
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.filter-tab {
    padding: 8px 16px;
    background: var(--surface, #f8f9fa);
    color: var(--text-primary, #333);
    text-decoration: none;
    border-radius: 20px;
    transition: all 0.3s;
}

.filter-tab:hover {
    background: #e9ecef;
}

.filter-tab.active {
    background: #007bff;
    color: white;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
}

.action-btn.primary {
    background: #28a745;
    color: white;
}

.action-btn.primary:hover {
    background: #218838;
}

.action-btn.secondary {
    background: #6c757d;
    color: white;
}

.action-btn.secondary:hover {
    background: #5a6268;
}

[data-theme="dark"] .filter-tab {
    background: var(--surface-dark, #2d3748);
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: News table
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="background: var(--surface, #ffffff); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; color: #666;">ID</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Заголовок</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Категория</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Дата</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Просмотры</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Статус</th>
                    <th style="padding: 15px; text-align: center; color: #666;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsItems as $news): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?= $news['id'] ?></td>
                    <td style="padding: 15px;">
                        <a href="/news/<?= htmlspecialchars($news['url_slug']) ?>" target="_blank"
                           style="color: #007bff; text-decoration: none;">
                            <?= htmlspecialchars($news['title_news']) ?>
                        </a>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= htmlspecialchars($news['title_category'] ?? 'Без категории') ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= date('d.m.Y H:i', strtotime($news['date_news'])) ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= $news['view_news'] ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($news['approved'] == 1): ?>
                            <span style="color: #28a745;">
                                <i class="fas fa-check-circle"></i> Опубликовано
                            </span>
                        <?php else: ?>
                            <span style="color: #ffc107;">
                                <i class="fas fa-clock"></i> Черновик
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="/edit/news/<?= $news['id'] ?>" style="color: #007bff; text-decoration: none;"
                               title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($news['approved'] == 0): ?>
                            <a href="#" onclick="publishNews(<?= $news['id'] ?>); return false;" 
                               style="color: #28a745; text-decoration: none;" title="Опубликовать">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php else: ?>
                            <a href="#" onclick="unpublishNews(<?= $news['id'] ?>); return false;" 
                               style="color: #ffc107; text-decoration: none;" title="В черновики">
                                <i class="fas fa-times"></i>
                            </a>
                            <?php endif; ?>
                            <a href="#" onclick="deleteNews(<?= $news['id'] ?>); return false;" 
                               style="color: #dc3545; text-decoration: none;" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($newsItems)): ?>
        <div style="padding: 60px; text-align: center;">
            <i class="fas fa-newspaper" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="color: #666;">Новости не найдены</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function publishNews(newsId) {
    if (confirm('Опубликовать эту новость?')) {
        // TODO: Implement publish functionality
        window.location.href = '/dashboard/news/publish/' + newsId;
    }
}

function unpublishNews(newsId) {
    if (confirm('Перевести в черновики?')) {
        // TODO: Implement unpublish functionality
        window.location.href = '/dashboard/news/unpublish/' + newsId;
    }
}

function deleteNews(newsId) {
    if (confirm('Вы уверены, что хотите удалить эту новость?')) {
        // TODO: Implement delete functionality
        window.location.href = '/dashboard/news/delete/' + newsId;
    }
}
</script>

<style>
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

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    $baseUrl = '/dashboard/news?filter=' . urlencode($filter) . '&search=' . urlencode($search) . '&page=';
    renderPaginationModern($page, $totalPages, $baseUrl);
}
$greyContent6 = ob_get_clean();

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Управление новостями - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
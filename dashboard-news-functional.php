<?php
// News management dashboard - Functional version

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

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Filter by approval status
$statusFilter = $_GET['status'] ?? '';
$statusCondition = '';
if ($statusFilter === 'approved') {
    $statusCondition = "WHERE approved = 1";
} elseif ($statusFilter === 'pending') {
    $statusCondition = "WHERE approved = 0";
}

// Search
$search = $_GET['search'] ?? '';
$searchCondition = '';
if (!empty($search)) {
    $searchLike = '%' . $connection->real_escape_string($search) . '%';
    $searchCondition = ($statusCondition ? ' AND ' : 'WHERE ') . "(title_news LIKE '$searchLike' OR text_news LIKE '$searchLike' OR author_news LIKE '$searchLike')";
}

// Get total news count
$countQuery = "SELECT COUNT(*) as total FROM news $statusCondition $searchCondition";
$countResult = $connection->query($countQuery);
$totalNews = $countResult ? $countResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalNews / $limit);

// Get news
$query = "SELECT id, title_news, author_news, date_news, approved, url_slug 
          FROM news 
          $statusCondition $searchCondition
          ORDER BY date_news DESC 
          LIMIT $limit OFFSET $offset";
$result = $connection->query($query);
$news = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }
}

// Get statistics
$stats = [];
$stats['total'] = $totalNews;

// Count by approval status
$approvalQuery = "SELECT approved, COUNT(*) as count FROM news GROUP BY approved";
$approvalResult = $connection->query($approvalQuery);
$stats['approved'] = 0;
$stats['pending'] = 0;
if ($approvalResult) {
    while ($row = $approvalResult->fetch_assoc()) {
        if ($row['approved'] == 1) {
            $stats['approved'] = $row['count'];
        } else {
            $stats['pending'] = $row['count'];
        }
    }
}

// Recent news (last 30 days)
$recentQuery = "SELECT COUNT(*) as count FROM news WHERE date_news >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentResult = $connection->query($recentQuery);
$stats['recent'] = $recentResult ? $recentResult->fetch_assoc()['count'] : 0;

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Управление новостями';

// Build dashboard content
ob_start();
?>
<style>
.news-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.filters-row {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.search-input, .filter-select {
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    background: var(--surface);
    color: var(--text-primary);
}

.search-input {
    flex: 1;
    min-width: 250px;
}

.filter-select {
    min-width: 150px;
}

.btn-primary {
    padding: 12px 20px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
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

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 8px;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.news-table {
    background: var(--surface);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.news-table table {
    width: 100%;
    border-collapse: collapse;
}

.news-table th {
    background: var(--bg-light);
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-color);
}

.news-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: top;
}

.news-table tr:last-child td {
    border-bottom: none;
}

.news-table tr:hover {
    background: var(--bg-light);
}

.news-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.news-title a {
    color: var(--primary-color);
    text-decoration: none;
}

.news-title a:hover {
    text-decoration: underline;
}

.news-meta {
    font-size: 13px;
    color: var(--text-secondary);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-edit {
    background: #17a2b8;
    color: white;
}

.btn-edit:hover {
    background: #138496;
}

.btn-approve {
    background: #28a745;
    color: white;
}

.btn-approve:hover {
    background: #218838;
}

.btn-reject {
    background: #ffc107;
    color: #856404;
}

.btn-reject:hover {
    background: #e0a800;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.btn-view {
    background: #6c757d;
    color: white;
}

.btn-view:hover {
    background: #5a6268;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .filters-row {
        flex-direction: column;
    }
    
    .search-input {
        min-width: auto;
    }
    
    .news-table {
        overflow-x: auto;
    }
    
    .news-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>

<div class="news-header">
    <h2>Управление новостями</h2>
    <a href="/create/news" class="btn-primary">
        <i class="fas fa-plus"></i> Создать новость
    </a>
</div>

<!-- Filters and Search -->
<form method="GET" class="filters-row">
    <input type="text" name="search" placeholder="Поиск по заголовку, автору или тексту..." 
           value="<?= htmlspecialchars($search) ?>" class="search-input">
    
    <select name="status" class="filter-select">
        <option value="">Все новости</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Опубликованные</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>На модерации</option>
    </select>
    
    <button type="submit" class="btn-primary">
        <i class="fas fa-search"></i> Поиск
    </button>
    
    <?php if ($search || $statusFilter): ?>
        <a href="/dashboard/news" class="btn-primary" style="background: #6c757d;">
            <i class="fas fa-times"></i> Сбросить
        </a>
    <?php endif; ?>
</form>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['approved']) ?></div>
        <div class="stat-label">Опубликованных</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['pending']) ?></div>
        <div class="stat-label">На модерации</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['recent'] ?? 0 ?></div>
        <div class="stat-label">Новых за 30 дней</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalPages ?></div>
        <div class="stat-label">Страниц</div>
    </div>
</div>

<!-- News Table -->
<div class="news-table">
    <table>
        <thead>
            <tr>
                <th>Новость</th>
                <th>Автор</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($news)): ?>
                <?php foreach ($news as $newsItem): ?>
                <tr>
                    <td>
                        <div class="news-title">
                            <a href="/news/<?= htmlspecialchars($newsItem['url_slug'] ?? $newsItem['id']) ?>" target="_blank">
                                <?= htmlspecialchars($newsItem['title_news']) ?>
                            </a>
                        </div>
                        <div class="news-meta">
                            ID: <?= $newsItem['id'] ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($newsItem['author_news'] ?? 'Не указан') ?></td>
                    <td><?= date('d.m.Y', strtotime($newsItem['date_news'])) ?></td>
                    <td>
                        <span class="status-badge <?= $newsItem['approved'] ? 'status-approved' : 'status-pending' ?>">
                            <?= $newsItem['approved'] ? 'Опубликовано' : 'На модерации' ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/news/<?= htmlspecialchars($newsItem['url_slug'] ?? $newsItem['id']) ?>" 
                               class="btn-sm btn-view" target="_blank" title="Просмотр">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/edit/news/<?= $newsItem['id'] ?>" 
                               class="btn-sm btn-edit" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (!$newsItem['approved']): ?>
                                <button onclick="approveNews(<?= $newsItem['id'] ?>)" 
                                        class="btn-sm btn-approve" title="Одобрить">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="unapproveNews(<?= $newsItem['id'] ?>)" 
                                        class="btn-sm btn-reject" title="Снять с публикации">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                            <button onclick="deleteNews(<?= $newsItem['id'] ?>)" 
                                    class="btn-sm btn-delete" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <div>Новости не найдены</div>
                            <?php if ($search || $statusFilter): ?>
                                <p>Попробуйте изменить условия поиска</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div>
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/dashboard/news');
    ?>
</div>
<?php endif; ?>

<script>
function approveNews(newsId) {
    ModalManager.confirm('Одобрение новости', 'Одобрить эту новость для публикации?', () => {
        window.location.href = `/api/news/approve/${newsId}?redirect=/dashboard/news`;
    }, 'info');
}

function unapproveNews(newsId) {
    ModalManager.confirm('Отмена публикации', 'Снять эту новость с публикации?', () => {
        window.location.href = `/api/news/unapprove/${newsId}?redirect=/dashboard/news`;
    }, 'warning');
}

function deleteNews(newsId) {
    ModalManager.confirm('Удаление новости', 'Вы уверены, что хотите удалить эту новость? Это действие нельзя отменить.', () => {
        window.location.href = `/api/news/delete/${newsId}?redirect=/dashboard/news`;
    }, 'danger');
}
</script>

<?php
$dashboardContent = ob_get_clean();

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>
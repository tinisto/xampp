<?php
// Posts management dashboard - Functional version

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

// Filter by category and search parameters
$categoryFilter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$queryParams = [];
$whereClauses = [];

// Build WHERE clauses
if (!empty($categoryFilter) && is_numeric($categoryFilter)) {
    $whereClauses[] = "p.category = ?";
    $queryParams[] = (int)$categoryFilter;
}

if (!empty($search)) {
    $whereClauses[] = "(p.title_post LIKE ? OR p.text_post LIKE ? OR p.author_post LIKE ?)";
    $searchParam = '%' . $search . '%';
    $queryParams[] = $searchParam;
    $queryParams[] = $searchParam;
    $queryParams[] = $searchParam;
}

$whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Get total posts count with prepared statement
$countQuery = "SELECT COUNT(*) as total FROM posts p $whereClause";
if (!empty($queryParams)) {
    $stmt = $connection->prepare($countQuery);
    $types = '';
    foreach ($queryParams as $param) {
        $types .= is_int($param) ? 'i' : 's';
    }
    $stmt->bind_param($types, ...$queryParams);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalPosts = $result->fetch_assoc()['total'];
    $stmt->close();
} else {
    $result = $connection->query($countQuery);
    $totalPosts = $result ? $result->fetch_assoc()['total'] : 0;
}
$totalPages = ceil($totalPosts / $limit);

// Get posts with category info using prepared statement
$query = "SELECT p.id_post, p.title_post, p.author_post, p.date_post, p.url_slug, p.view_post, p.category,
                 c.name_category
          FROM posts p
          LEFT JOIN categories c ON p.category = c.id
          $whereClause
          ORDER BY p.date_post DESC 
          LIMIT ? OFFSET ?";

$stmt = $connection->prepare($query);
if (!empty($queryParams)) {
    $allParams = array_merge($queryParams, [$limit, $offset]);
    $types = '';
    foreach ($queryParams as $param) {
        $types .= is_int($param) ? 'i' : 's';
    }
    $types .= 'ii';
    $stmt->bind_param($types, ...$allParams);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();

// Get statistics
$stats = [];

// Total posts
$stats['total'] = $totalPosts;

// Posts by category (top categories)
$categoryQuery = "SELECT c.name_category, COUNT(p.id_post) as count 
                  FROM categories c 
                  LEFT JOIN posts p ON c.id = p.category 
                  GROUP BY c.id, c.name_category 
                  ORDER BY count DESC 
                  LIMIT 5";
$categoryResult = $connection->query($categoryQuery);
$topCategories = [];
while ($row = $categoryResult->fetch_assoc()) {
    if ($row['count'] > 0) {
        $topCategories[] = $row;
    }
}

// Recent posts (last 30 days)
$recentQuery = "SELECT COUNT(*) as count FROM posts WHERE date_post >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentResult = $connection->query($recentQuery);
$stats['recent'] = $recentResult->fetch_assoc()['count'];

// Most viewed posts
$viewsQuery = "SELECT SUM(view_post) as total_views FROM posts";
$viewsResult = $connection->query($viewsQuery);
$stats['total_views'] = $viewsResult->fetch_assoc()['total_views'] ?? 0;

// Get all categories for filter dropdown
$allCategoriesQuery = "SELECT id, name_category FROM categories ORDER BY name_category";
$allCategoriesResult = $connection->query($allCategoriesQuery);
$allCategories = [];
while ($row = $allCategoriesResult->fetch_assoc()) {
    $allCategories[] = $row;
}

// Get user info
$username = $_SESSION['first_name'] ?? $_SESSION['email'] ?? 'Admin';
$userInitial = strtoupper(mb_substr($username, 0, 1));

// Set dashboard title
$dashboardTitle = 'Управление статьями';

// Build dashboard content
ob_start();
?>
<style>
.posts-header {
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
    min-width: 180px;
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

.posts-table {
    background: var(--surface);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.posts-table table {
    width: 100%;
    border-collapse: collapse;
}

.posts-table th {
    background: var(--bg-light);
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-color);
}

.posts-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: top;
}

.posts-table tr:last-child td {
    border-bottom: none;
}

.posts-table tr:hover {
    background: var(--bg-light);
}

.post-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.post-title a {
    color: var(--primary-color);
    text-decoration: none;
}

.post-title a:hover {
    text-decoration: underline;
}

.post-meta {
    font-size: 13px;
    color: var(--text-secondary);
}

.category-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    background: var(--bg-light);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.views-count {
    font-size: 13px;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
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

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.btn-view {
    background: #28a745;
    color: white;
}

.btn-view:hover {
    background: #218838;
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

.top-categories {
    margin-top: 30px;
}

.category-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.category-item {
    background: var(--surface);
    padding: 15px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.category-name {
    font-weight: 500;
    color: var(--text-primary);
}

.category-count {
    color: var(--text-secondary);
    font-size: 14px;
}

@media (max-width: 768px) {
    .filters-row {
        flex-direction: column;
    }
    
    .search-input {
        min-width: auto;
    }
    
    .posts-table {
        overflow-x: auto;
    }
    
    .posts-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>

<div class="posts-header">
    <h2>Управление статьями</h2>
    <a href="/create/post" class="btn-primary">
        <i class="fas fa-plus"></i> Создать статью
    </a>
</div>

<!-- Filters and Search -->
<form method="GET" class="filters-row">
    <input type="text" name="search" placeholder="Поиск по заголовку, автору или тексту..." 
           value="<?= htmlspecialchars($search) ?>" class="search-input">
    
    <select name="category" class="filter-select">
        <option value="">Все категории</option>
        <?php foreach ($allCategories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name_category']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <button type="submit" class="btn-primary">
        <i class="fas fa-search"></i> Поиск
    </button>
    
    <?php if ($search || $categoryFilter): ?>
        <a href="/dashboard/posts" class="btn-primary" style="background: #6c757d;">
            <i class="fas fa-times"></i> Сбросить
        </a>
    <?php endif; ?>
</form>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
        <div class="stat-label">Всего статей</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['recent'] ?? 0 ?></div>
        <div class="stat-label">Новых за 30 дней</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= number_format($stats['total_views']) ?></div>
        <div class="stat-label">Всего просмотров</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= count($allCategories) ?></div>
        <div class="stat-label">Категорий</div>
    </div>
</div>

<!-- Posts Table -->
<div class="posts-table">
    <table>
        <thead>
            <tr>
                <th>Статья</th>
                <th>Автор</th>
                <th>Категория</th>
                <th>Дата</th>
                <th>Просмотры</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td>
                        <div class="post-title">
                            <a href="/post/<?= htmlspecialchars($post['url_slug']) ?>" target="_blank">
                                <?= htmlspecialchars($post['title_post']) ?>
                            </a>
                        </div>
                        <div class="post-meta">
                            ID: <?= $post['id_post'] ?> • 
                            <?= htmlspecialchars($post['url_slug']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($post['author_post'] ?? 'Не указан') ?></td>
                    <td>
                        <span class="category-badge">
                            <?= htmlspecialchars($post['name_category'] ?? 'Без категории') ?>
                        </span>
                    </td>
                    <td><?= date('d.m.Y', strtotime($post['date_post'])) ?></td>
                    <td>
                        <div class="views-count">
                            <i class="fas fa-eye"></i>
                            <?= number_format($post['view_post'] ?? 0) ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/post/<?= htmlspecialchars($post['url_slug']) ?>" 
                               class="btn-sm btn-view" target="_blank" title="Просмотр">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/edit/post/<?= $post['id_post'] ?>" 
                               class="btn-sm btn-edit" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deletePost(<?= $post['id_post'] ?>)" 
                                    class="btn-sm btn-delete" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <div>Статьи не найдены</div>
                            <?php if ($search || $categoryFilter): ?>
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
    renderPaginationModern($page, $totalPages, '/dashboard/posts');
    ?>
</div>
<?php endif; ?>

<!-- Top Categories Section -->
<?php if (!empty($topCategories)): ?>
<div class="top-categories">
    <h3>Популярные категории</h3>
    <div class="category-list">
        <?php foreach ($topCategories as $category): ?>
            <div class="category-item">
                <span class="category-name"><?= htmlspecialchars($category['name_category']) ?></span>
                <span class="category-count"><?= $category['count'] ?> статей</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
function deletePost(postId) {
    ModalManager.confirm('Удаление статьи', 'Вы уверены, что хотите удалить эту статью? Это действие нельзя отменить.', () => {
        // Simple redirect to a delete endpoint
        window.location.href = '/api/posts/delete/' + postId + '?redirect=/dashboard/posts';
    }, 'danger');
}
</script>
<?php
$dashboardContent = ob_get_clean();

// Include the dashboard template
include $_SERVER['DOCUMENT_ROOT'] . '/dashboard-template.php';
?>
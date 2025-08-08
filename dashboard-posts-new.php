<?php
// Posts management dashboard - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin') {
    header('Location: /unauthorized');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Search
$search = $_GET['search'] ?? '';
$searchCondition = '';
if (!empty($search)) {
    $searchLike = '%' . $connection->real_escape_string($search) . '%';
    $searchCondition = "WHERE title_post LIKE '$searchLike' OR text_post LIKE '$searchLike'";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM posts $searchCondition";
$countResult = $connection->query($countQuery);
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);

// Get posts
$query = "SELECT p.*, c.title_category, u.first_name, u.last_name 
          FROM posts p
          LEFT JOIN categories c ON p.id_category = c.id_category
          LEFT JOIN users u ON p.user_id = u.id
          $searchCondition
          ORDER BY p.date_post DESC 
          LIMIT $limit OFFSET $offset";
$result = $connection->query($query);
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Управление статьями', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'Всего статей: ' . $totalPosts
]);
$greyContent1 = ob_get_clean();

// Section 2: Search and actions
ob_start();
?>
<div style="padding: 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <form method="GET" style="flex: 1; max-width: 400px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Поиск статей..." 
                           style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; 
                                                 border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <div style="display: flex; gap: 10px;">
                <a href="/create/post" class="action-btn primary">
                    <i class="fas fa-plus"></i> Создать статью
                </a>
                <a href="/dashboard" class="action-btn secondary">
                    <i class="fas fa-arrow-left"></i> К панели
                </a>
            </div>
        </div>
    </div>
</div>

<style>
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
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Posts table
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
                    <th style="padding: 15px; text-align: left; color: #666;">Автор</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Дата</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Просмотры</th>
                    <th style="padding: 15px; text-align: center; color: #666;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?= $post['id_post'] ?></td>
                    <td style="padding: 15px;">
                        <a href="/post/<?= htmlspecialchars($post['url_post']) ?>" target="_blank"
                           style="color: #007bff; text-decoration: none;">
                            <?= htmlspecialchars($post['title_post']) ?>
                        </a>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= htmlspecialchars($post['title_category'] ?? 'Без категории') ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?php if ($post['first_name'] || $post['last_name']): ?>
                            <?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?>
                        <?php else: ?>
                            <span style="color: #999;">Неизвестно</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= date('d.m.Y', strtotime($post['date_post'])) ?>
                    </td>
                    <td style="padding: 15px; color: #666;">
                        <?= $post['view_post'] ?>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="/edit/post/<?= $post['id_post'] ?>" style="color: #007bff; text-decoration: none;"
                               title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" onclick="deletePost(<?= $post['id_post'] ?>); return false;" 
                               style="color: #dc3545; text-decoration: none;" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($posts)): ?>
        <div style="padding: 60px; text-align: center;">
            <i class="fas fa-file-alt" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="color: #666;">Статьи не найдены</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deletePost(postId) {
    if (confirm('Вы уверены, что хотите удалить эту статью?')) {
        // TODO: Implement delete functionality
        window.location.href = '/dashboard/posts/delete/' + postId;
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
    renderPaginationModern($page, $totalPages, '/dashboard/posts?search=' . urlencode($search) . '&page=');
}
$greyContent6 = ob_get_clean();

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Управление статьями - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
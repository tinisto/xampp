<?php
// Comments management dashboard - migrated to use real_template.php

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
    case 'approved':
        $filterCondition = 'WHERE c.approved = 1';
        break;
    case 'pending':
        $filterCondition = 'WHERE c.approved = 0';
        break;
}

// Search
$search = $_GET['search'] ?? '';
$searchParams = [];
$searchCondition = '';

// Build WHERE clause parts
$whereParts = [];
if ($filterCondition) {
    $whereParts[] = str_replace('WHERE ', '', $filterCondition);
}

if (!empty($search)) {
    $whereParts[] = "c.text_comment LIKE ?";
    $searchParams[] = '%' . $search . '%';
}

$whereClause = !empty($whereParts) ? 'WHERE ' . implode(' AND ', $whereParts) : '';

// Get total count with prepared statement
$countQuery = "SELECT COUNT(*) as total FROM comments c $whereClause";
if (!empty($searchParams)) {
    $stmt = $connection->prepare($countQuery);
    $stmt->bind_param(str_repeat('s', count($searchParams)), ...$searchParams);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalComments = $result->fetch_assoc()['total'];
    $stmt->close();
} else {
    $result = $connection->query($countQuery);
    $totalComments = $result->fetch_assoc()['total'];
}
$totalPages = ceil($totalComments / $limit);

// Get comments with related info using prepared statement
$query = "SELECT c.*, 
          u.first_name, u.last_name, u.email,
          n.title_news, n.url_slug as news_url,
          p.title_post, p.url_post
          FROM comments c
          LEFT JOIN users u ON c.user_id = u.id
          LEFT JOIN news n ON c.news_id = n.id
          LEFT JOIN posts p ON c.post_id = p.id_post
          $whereClause
          ORDER BY c.date DESC 
          LIMIT ? OFFSET ?";

$allParams = array_merge($searchParams, [$limit, $offset]);
$types = str_repeat('s', count($searchParams)) . 'ii';

$stmt = $connection->prepare($query);
if (!empty($allParams)) {
    $stmt->bind_param($types, ...$allParams);
}
$stmt->execute();
$result = $stmt->get_result();
$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}
$stmt->close();

// Get statistics
$stats = [];
$query = "SELECT approved, COUNT(*) as count FROM comments GROUP BY approved";
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
    if ($row['approved'] == 1) {
        $stats['approved'] = $row['count'];
    } else {
        $stats['pending'] = $row['count'];
    }
}
$stats['total'] = ($stats['approved'] ?? 0) + ($stats['pending'] ?? 0);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Управление комментариями', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'Всего комментариев: ' . $stats['total']
]);
$greyContent1 = ob_get_clean();

// Section 2: Filters and actions
ob_start();
?>
<div style="padding: 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Filter tabs -->
        <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
            <a href="/dashboard/comments" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                Все (<?= $stats['total'] ?>)
            </a>
            <a href="/dashboard/comments?filter=approved" class="filter-tab <?= $filter === 'approved' ? 'active' : '' ?>">
                Одобренные (<?= $stats['approved'] ?? 0 ?>)
            </a>
            <a href="/dashboard/comments?filter=pending" class="filter-tab <?= $filter === 'pending' ? 'active' : '' ?>">
                На модерации (<?= $stats['pending'] ?? 0 ?>)
            </a>
        </div>
        
        <!-- Search and actions -->
        <div style="display: flex; gap: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <form method="GET" style="flex: 1; max-width: 400px;">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Поиск комментариев..." 
                           style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; 
                                                 border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <div style="display: flex; gap: 10px;">
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

// Section 5: Comments table
ob_start();
?>
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="background: var(--surface, #ffffff); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; color: #666;">ID</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Комментарий</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Автор</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Контент</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Дата</th>
                    <th style="padding: 15px; text-align: left; color: #666;">Статус</th>
                    <th style="padding: 15px; text-align: center; color: #666;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 15px;"><?= $comment['id_comment'] ?></td>
                    <td style="padding: 15px; max-width: 300px;">
                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= htmlspecialchars($comment['text_comment']) ?>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($comment['first_name'] || $comment['last_name']): ?>
                            <div style="font-weight: 500;">
                                <?= htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) ?>
                            </div>
                            <div style="color: #666; font-size: 0.85rem;">
                                <?= htmlspecialchars($comment['email']) ?>
                            </div>
                        <?php else: ?>
                            <span style="color: #999;">Анонимный</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if (!empty($comment['news_id'])): ?>
                            <a href="/news/<?= htmlspecialchars($comment['news_url']) ?>" target="_blank"
                               style="color: #007bff; text-decoration: none; font-size: 0.85rem;">
                                <i class="fas fa-newspaper"></i> <?= htmlspecialchars(mb_substr($comment['title_news'], 0, 30)) ?>...
                            </a>
                        <?php elseif (!empty($comment['post_id'])): ?>
                            <a href="/post/<?= htmlspecialchars($comment['url_post']) ?>" target="_blank"
                               style="color: #007bff; text-decoration: none; font-size: 0.85rem;">
                                <i class="fas fa-file-alt"></i> <?= htmlspecialchars(mb_substr($comment['title_post'], 0, 30)) ?>...
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; color: #666; font-size: 0.85rem;">
                        <?= date('d.m.Y H:i', strtotime($comment['date'])) ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($comment['approved'] == 1): ?>
                            <span style="color: #28a745;">
                                <i class="fas fa-check-circle"></i> Одобрен
                            </span>
                        <?php else: ?>
                            <span style="color: #ffc107;">
                                <i class="fas fa-clock"></i> На модерации
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <?php if ($comment['approved'] == 0): ?>
                            <a href="#" onclick="approveComment(<?= $comment['id_comment'] ?>); return false;" 
                               style="color: #28a745; text-decoration: none;" title="Одобрить">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php else: ?>
                            <a href="#" onclick="unapproveComment(<?= $comment['id_comment'] ?>); return false;" 
                               style="color: #ffc107; text-decoration: none;" title="Снять одобрение">
                                <i class="fas fa-times"></i>
                            </a>
                            <?php endif; ?>
                            <a href="#" onclick="deleteComment(<?= $comment['id_comment'] ?>); return false;" 
                               style="color: #dc3545; text-decoration: none;" title="Удалить">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($comments)): ?>
        <div style="padding: 60px; text-align: center;">
            <i class="fas fa-comments" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="color: #666;">Комментарии не найдены</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function approveComment(commentId) {
    if (confirm('Одобрить этот комментарий?')) {
        // TODO: Implement approve functionality
        window.location.href = '/dashboard/comments/approve/' + commentId;
    }
}

function unapproveComment(commentId) {
    if (confirm('Снять одобрение с комментария?')) {
        // TODO: Implement unapprove functionality
        window.location.href = '/dashboard/comments/unapprove/' + commentId;
    }
}

function deleteComment(commentId) {
    if (confirm('Вы уверены, что хотите удалить этот комментарий?')) {
        // TODO: Implement delete functionality
        window.location.href = '/dashboard/comments/delete/' + commentId;
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
    $baseUrl = '/dashboard/comments?filter=' . urlencode($filter) . '&search=' . urlencode($search) . '&page=';
    renderPaginationModern($page, $totalPages, $baseUrl);
}
$greyContent6 = ob_get_clean();

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Управление комментариями - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config/loadEnv.php';
require_once __DIR__ . '/../database/db_connections.php';

$message = '';
$messageType = '';

// Function to get the correct URL for an entity
function getEntityUrl($entityType, $entityId, $urlsArray) {
    switch ($entityType) {
        case 'post':
            if (isset($urlsArray['posts'][$entityId])) {
                return "/post/" . $urlsArray['posts'][$entityId];
            }
            break;
            
        case 'news':
            if (isset($urlsArray['news'][$entityId])) {
                return "/news/" . $urlsArray['news'][$entityId];
            }
            break;
            
        case 'school':
            if (isset($urlsArray['schools'][$entityId])) {
                return "/school/" . $urlsArray['schools'][$entityId];
            }
            // Fallback to ID link if no slug found
            return "/school/$entityId";
            
        case 'vpo':
        case 'university':
            if (isset($urlsArray['vpo'][$entityId])) {
                return "/vpo/" . $urlsArray['vpo'][$entityId];
            }
            break;
            
        case 'spo':
        case 'college':
            if (isset($urlsArray['spo'][$entityId])) {
                return "/spo/" . $urlsArray['spo'][$entityId];
            }
            break;
    }
    
    return '#';
}

// Handle comment actions
if ($_POST && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'delete_comment':
                $commentId = (int)$_POST['comment_id'];
                $sql = "DELETE FROM comments WHERE id = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("i", $commentId);
                if ($stmt->execute()) {
                    $message = "✅ Comment deleted successfully!";
                    $messageType = "success";
                } else {
                    $message = "❌ Error deleting comment";
                    $messageType = "error";
                }
                break;
        }
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Get comments with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$sql = "SELECT c.*, u.first_name, u.last_name, u.email
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.date DESC 
        LIMIT ? OFFSET ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$comments = $stmt->get_result();

// Fetch all comments into array and collect entity IDs by type
$commentsArray = [];
$postIds = [];
$newsIds = [];
$schoolIds = [];
$schoolUrls = [];
$vpoIds = [];
$spoIds = [];

while ($comment = $comments->fetch_assoc()) {
    $commentsArray[] = $comment;
    
    if ($comment['entity_type'] === 'post' && $comment['id_entity']) {
        $postIds[] = $comment['id_entity'];
    } elseif ($comment['entity_type'] === 'news' && $comment['id_entity']) {
        $newsIds[] = $comment['id_entity'];
    } elseif ($comment['entity_type'] === 'school' && $comment['id_entity']) {
        $schoolIds[] = $comment['id_entity'];
    } elseif (($comment['entity_type'] === 'vpo' || $comment['entity_type'] === 'university') && $comment['id_entity']) {
        $vpoIds[] = $comment['id_entity'];
    } elseif (($comment['entity_type'] === 'spo' || $comment['entity_type'] === 'college') && $comment['id_entity']) {
        $spoIds[] = $comment['id_entity'];
    }
}

// Fetch URLs for each entity type
$postUrls = [];
if (!empty($postIds)) {
    $ids = implode(',', array_map('intval', $postIds));
    $result = $connection->query("SELECT id, url_slug FROM posts WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $postUrls[$row['id']] = $row['url_slug'];
    }
}

$newsUrls = [];
if (!empty($newsIds)) {
    $ids = implode(',', array_map('intval', $newsIds));
    $result = $connection->query("SELECT id, url_slug FROM news WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $newsUrls[$row['id']] = $row['url_slug'];
    }
}

$vpoUrls = [];
if (!empty($vpoIds)) {
    $ids = implode(',', array_map('intval', $vpoIds));
    $result = $connection->query("SELECT id, url_slug FROM vpo WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $vpoUrls[$row['id']] = $row['url_slug'];
    }
}

$spoUrls = [];
if (!empty($spoIds)) {
    $ids = implode(',', array_map('intval', $spoIds));
    $result = $connection->query("SELECT id, url_slug FROM spo WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $spoUrls[$row['id']] = $row['url_slug'];
    }
}

$schoolUrls = [];
if (!empty($schoolIds)) {
    $ids = implode(',', array_map('intval', $schoolIds));
    $result = $connection->query("SELECT id, url_slug FROM schools WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $schoolUrls[$row['id']] = $row['url_slug'];
    }
}

// Get total count
$countSql = "SELECT COUNT(*) as total FROM comments";
$countResult = $connection->query($countSql);
$totalComments = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalComments / $limit);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments Management - 11классники</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #2c3e50; color: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { background: #3498db; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 14px; margin: 5px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .alert { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .comments-table { width: 100%; border-collapse: collapse; margin: 20px 0; table-layout: fixed; }
        .comments-table th, .comments-table td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: top; }
        .comments-table th { background: #f8f9fa; font-weight: 600; }
        .comments-table td { word-wrap: break-word; }
        .comment-content { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .comments-table th:nth-child(1) { width: 60px; }
        .comments-table th:nth-child(2) { width: 150px; }
        .comments-table th:nth-child(3) { width: 35%; }
        .comments-table th:nth-child(4) { width: 150px; }
        .comments-table th:nth-child(5) { width: 120px; }
        .comments-table th:nth-child(6) { width: 100px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #3498db; }
        .stat-label { color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/dashboard" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <h1>💬 Comments Management</h1>
            <p>Manage user comments and moderation</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $totalComments ?></div>
                <div class="stat-label">Total Comments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalPages ?></div>
                <div class="stat-label">Pages</div>
            </div>
        </div>
        
        <div class="card">
            <h3>Recent Comments</h3>
            
            <?php if (!empty($commentsArray)): ?>
                <?php 
                // Prepare URLs array for the template
                $urlsArray = [
                    'posts' => $postUrls,
                    'news' => $newsUrls,
                    'schools' => $schoolUrls,
                    'vpo' => $vpoUrls,
                    'spo' => $spoUrls
                ];
                ?>
                <table class="comments-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Content</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commentsArray as $comment): ?>
                            <tr>
                                <td><?= $comment['id'] ?></td>
                                <td><?= htmlspecialchars($comment['author_of_comment'] ?? trim($comment['first_name'] . ' ' . $comment['last_name']) ?: $comment['email'] ?: 'Anonymous') ?></td>
                                <td class="comment-content" title="<?= htmlspecialchars($comment['comment_text']) ?>">
                                    <?= htmlspecialchars(substr($comment['comment_text'], 0, 100)) ?>...
                                </td>
                                <td>
                                    <?php 
                                    $entityType = $comment['entity_type'] ?? 'Unknown';
                                    $entityId = $comment['id_entity'] ?? 0;
                                    $viewUrl = getEntityUrl($entityType, $entityId, $urlsArray);
                                    ?>
                                    <div style="font-size: 12px;">
                                        <strong><?= htmlspecialchars($entityType) ?></strong>
                                        <small>(ID: <?= $entityId ?>)</small>
                                    </div>
                                    <?php if ($viewUrl !== '#'): ?>
                                        <a href="<?= $viewUrl ?>" target="_blank" class="btn" style="font-size: 12px; padding: 4px 8px; margin-top: 5px;">
                                            View →
                                        </a>
                                    <?php else: ?>
                                        <small>(ID: <?= $entityId ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('Y-m-d H:i', strtotime($comment['date'])) ?></td>
                                <td>
                                    <form method="post" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                        <button type="submit" name="action" value="delete_comment" class="btn btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($totalPages > 1): ?>
                    <?php
                    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
                    renderPaginationModern($page, $totalPages);
                    ?>
                <?php endif; ?>
                
            <?php else: ?>
                <p>No comments found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
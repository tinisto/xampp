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
        .comments-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .comments-table th, .comments-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .comments-table th { background: #f8f9fa; font-weight: 600; }
        .comment-content { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a { display: inline-block; padding: 8px 12px; margin: 0 4px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
        .pagination a.active { background: #2c3e50; }
        .pagination a:hover { background: #2980b9; }
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
            
            <?php if ($comments->num_rows > 0): ?>
                <table class="comments-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Content</th>
                            <th>Posted On</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($comment = $comments->fetch_assoc()): ?>
                            <tr>
                                <td><?= $comment['id'] ?></td>
                                <td><?= htmlspecialchars($comment['author_of_comment'] ?? trim($comment['first_name'] . ' ' . $comment['last_name']) ?: $comment['email'] ?: 'Anonymous') ?></td>
                                <td class="comment-content" title="<?= htmlspecialchars($comment['comment_text']) ?>">
                                    <?= htmlspecialchars(substr($comment['comment_text'], 0, 100)) ?>...
                                </td>
                                <td>
                                    <?= htmlspecialchars($comment['entity_type'] ?? 'Unknown') ?>
                                    (ID: <?= $comment['id_entity'] ?? 'N/A' ?>)
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
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" <?= $i == $page ? 'class="active"' : '' ?>>
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <p>No comments found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
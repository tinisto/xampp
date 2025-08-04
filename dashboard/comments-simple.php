<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

$message = '';
$messageType = '';
$comments = [];
$totalComments = 0;
$totalPages = 1;

try {
    // Get comments with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    // Check if comments table exists and get data
    $checkTable = $connection->query("SHOW TABLES LIKE 'comments'");
    if ($checkTable && $checkTable->num_rows > 0) {
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM comments";
        $countResult = $connection->query($countSql);
        if ($countResult) {
            $totalComments = $countResult->fetch_assoc()['total'];
            $totalPages = ceil($totalComments / $limit);
        }

        // Get comments - simplified query
        $sql = "SELECT * FROM comments ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = $connection->query($sql);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }
        }
    } else {
        $message = "Comments table not found. Please run database migrations.";
        $messageType = "warning";
    }
} catch (Exception $e) {
    $message = "Database error: " . $e->getMessage();
    $messageType = "error";
}

// Handle comment deletion
if ($_POST && isset($_POST['delete_comment_id'])) {
    try {
        $commentId = (int)$_POST['delete_comment_id'];
        $sql = "DELETE FROM comments WHERE id = $commentId";
        if ($connection->query($sql)) {
            header('Location: /dashboard/comments-simple.php?deleted=1');
            exit;
        }
    } catch (Exception $e) {
        $message = "Error deleting comment: " . $e->getMessage();
        $messageType = "error";
    }
}

if (isset($_GET['deleted'])) {
    $message = "✅ Comment deleted successfully!";
    $messageType = "success";
}
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
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .comments-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .comments-table th, .comments-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .comments-table th { background: #f8f9fa; font-weight: 600; }
        .comment-content { max-width: 400px; }
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
            
            <?php if (!empty($comments)): ?>
                <table class="comments-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Content</th>
                            <th>User ID</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?= $comment['id'] ?></td>
                                <td class="comment-content">
                                    <?= htmlspecialchars(substr($comment['comment_text'] ?? '', 0, 100)) ?>...
                                </td>
                                <td><?= $comment['user_id'] ?? 'N/A' ?></td>
                                <td><?= isset($comment['created_at']) ? date('Y-m-d H:i', strtotime($comment['created_at'])) : 'N/A' ?></td>
                                <td>
                                    <form method="post" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                        <input type="hidden" name="delete_comment_id" value="<?= $comment['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                <p>No comments found or comments table doesn't exist.</p>
                <p><a href="/dashboard/run-migrations.php" class="btn">Run Database Migrations</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
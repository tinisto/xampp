<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once dirname(__DIR__) . '/config/loadEnv.php';
require_once dirname(__DIR__) . '/database/db_connections.php';

$message = '';
$messageType = '';

// Get comments with simple query first
try {
    $sql = "SELECT * FROM comments ORDER BY date DESC LIMIT 20";
    $result = $connection->query($sql);
    
    if (!$result) {
        die("Query failed: " . $connection->error);
    }
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    
    // Get total count
    $countResult = $connection->query("SELECT COUNT(*) as total FROM comments");
    $totalComments = $countResult->fetch_assoc()['total'];
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
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
        .back-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/dashboard-professional.php" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <h1>💬 Comments Management</h1>
            <p>Total comments: <?= $totalComments ?></p>
        </div>
        
        <div class="card">
            <h3>Recent Comments (Simple View)</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Author</th>
                        <th>Comment</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><?= $comment['id'] ?></td>
                            <td><?= htmlspecialchars($comment['author_of_comment'] ?? 'Anonymous') ?></td>
                            <td><?= htmlspecialchars(substr($comment['comment_text'] ?? '', 0, 50)) ?>...</td>
                            <td><?= $comment['entity_type'] ?? 'Unknown' ?> (<?= $comment['id_entity'] ?? 'N/A' ?>)</td>
                            <td><?= date('Y-m-d H:i', strtotime($comment['date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
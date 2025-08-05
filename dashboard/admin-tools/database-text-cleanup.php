<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$message = '';
$messageType = '';

if ($_POST && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'cleanup_all':
                $cleanupCount = 0;
                
                // Tables and text columns to clean
                $tables = [
                    'news' => ['title', 'short_content', 'content', 'meta_description'],
                    'posts' => ['title', 'content', 'summary', 'meta_description'],
                    'comments' => ['comment_text'],
                    'users' => ['username', 'first_name', 'last_name', 'about'],
                    'schools' => ['name', 'address', 'description'],
                    'universities' => ['name', 'address', 'description'],
                    'colleges' => ['name', 'address', 'description']
                ];
                
                foreach ($tables as $table => $columns) {
                    // Check if table exists
                    $checkTable = $connection->query("SHOW TABLES LIKE '$table'");
                    if ($checkTable->num_rows == 0) continue;
                    
                    foreach ($columns as $column) {
                        // Check if column exists
                        $checkColumn = $connection->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
                        if ($checkColumn->num_rows == 0) continue;
                        
                        // Clean the text: trim + normalize spaces
                        $sql = "UPDATE `$table` SET `$column` = 
                                TRIM(REGEXP_REPLACE(`$column`, '[[:space:]]+', ' ')) 
                                WHERE `$column` IS NOT NULL AND `$column` != ''";
                        
                        if ($connection->query($sql)) {
                            $cleanupCount += $connection->affected_rows;
                        }
                    }
                }
                
                $message = "✅ Database text cleanup completed! Cleaned $cleanupCount records.";
                $messageType = "success";
                break;
                
            case 'find_issues':
                $issues = [];
                
                // Find records with multiple spaces
                $tables = [
                    'news' => ['id', 'title', 'content'],
                    'posts' => ['id', 'title', 'content'],
                    'comments' => ['id', 'comment_text']
                ];
                
                foreach ($tables as $table => $columns) {
                    $checkTable = $connection->query("SHOW TABLES LIKE '$table'");
                    if ($checkTable->num_rows == 0) continue;
                    
                    $idColumn = array_shift($columns);
                    foreach ($columns as $column) {
                        $checkColumn = $connection->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
                        if ($checkColumn->num_rows == 0) continue;
                        
                        $sql = "SELECT `$idColumn`, `$column` FROM `$table` 
                                WHERE `$column` REGEXP '[[:space:]]{2,}' 
                                   OR `$column` LIKE ' %' 
                                   OR `$column` LIKE '% ' 
                                LIMIT 10";
                        
                        $result = $connection->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $issues[] = [
                                    'table' => $table,
                                    'column' => $column,
                                    'id' => $row[$idColumn],
                                    'text' => substr($row[$column], 0, 100) . '...'
                                ];
                            }
                        }
                    }
                }
                
                if (empty($issues)) {
                    $message = "✅ No text formatting issues found!";
                    $messageType = "success";
                } else {
                    $message = "Found " . count($issues) . " text formatting issues. Use 'Clean All Text' to fix them.";
                    $messageType = "warning";
                }
                break;
        }
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Text Cleanup - 11классники</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #2c3e50; color: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { background: #3498db; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .alert { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .issues-list { max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        .issue-item { padding: 8px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧹 Database Text Cleanup</h1>
            <p>Clean up text formatting in database records</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($issues) && !empty($issues)): ?>
            <div class="card">
                <h3>Text Issues Found:</h3>
                <div class="issues-list">
                    <?php foreach ($issues as $issue): ?>
                        <div class="issue-item">
                            <strong><?= $issue['table'] ?>.<?= $issue['column'] ?> #<?= $issue['id'] ?>:</strong><br>
                            <?= htmlspecialchars($issue['text']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Text Cleanup Actions</h3>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This will modify text content in your database. 
                Make sure you have a backup before proceeding.
            </div>
            
            <p>This tool will:</p>
            <ul style="margin: 15px 0 15px 30px;">
                <li>Remove leading and trailing whitespace (trim)</li>
                <li>Replace multiple spaces with single spaces</li>
                <li>Clean text in news, posts, comments, users, and schools</li>
            </ul>
            
            <form method="post" style="margin: 20px 0;">
                <button type="submit" name="action" value="find_issues" class="btn btn-warning">
                    🔍 Find Text Issues
                </button>
                
                <button type="submit" name="action" value="cleanup_all" class="btn btn-success" 
                        onclick="return confirm('Are you sure you want to clean all text in the database?')">
                    🧹 Clean All Text
                </button>
            </form>
        </div>
        
        <a href="/dashboard" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$message = '';
$messageType = '';

if ($_POST && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_tables':
            try {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
                
                // Create comment_reactions table
                $sql1 = "CREATE TABLE IF NOT EXISTS comment_reactions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    comment_id INT NOT NULL,
                    user_id INT NOT NULL,
                    reaction_type ENUM('like', 'dislike') NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_reaction (comment_id, user_id)
                )";
                $connection->query($sql1);
                
                // Create failed_logins table
                $sql2 = "CREATE TABLE IF NOT EXISTS failed_logins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_email (email),
                    INDEX idx_ip (ip_address)
                )";
                $connection->query($sql2);
                
                // Create remember_tokens table
                $sql3 = "CREATE TABLE IF NOT EXISTS remember_tokens (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_token (token),
                    INDEX idx_user (user_id)
                )";
                $connection->query($sql3);
                
                // Create password_resets table
                $sql4 = "CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_email (email),
                    INDEX idx_token (token)
                )";
                $connection->query($sql4);
                
                $message = "✅ All migration tables created successfully!";
                $messageType = "success";
                
            } catch (Exception $e) {
                $message = "❌ Error: " . $e->getMessage();
                $messageType = "error";
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migrations - 11классники</title>
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
        .alert { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Database Migrations</h1>
            <p>Create database tables for new features</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Available Migrations</h3>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This will create new database tables. Make sure you have a backup before proceeding.
            </div>
            
            <p>This migration will create the following tables:</p>
            <ul style="margin: 15px 0 15px 30px;">
                <li><strong>comment_reactions</strong> - Like/dislike system for comments</li>
                <li><strong>failed_logins</strong> - Track failed login attempts</li>
                <li><strong>remember_tokens</strong> - "Remember me" functionality</li>
                <li><strong>password_resets</strong> - Password reset tokens</li>
            </ul>
            
            <form method="post">
                <button type="submit" name="action" value="create_tables" class="btn btn-success" 
                        onclick="return confirm('Are you sure you want to create the migration tables?')">
                    🗄️ Create Migration Tables
                </button>
            </form>
        </div>
        
        <a href="/dashboard" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
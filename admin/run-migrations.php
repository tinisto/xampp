<?php
/**
 * Web Interface for Running Database Migrations
 * Admin-only tool to run migrations through browser
 */

session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$output = '';
$success = false;

if ($_POST && isset($_POST['run_migrations'])) {
    // Run migrations
    ob_start();
    
    // Change to the correct directory
    $rootDir = dirname(__DIR__);
    chdir($rootDir);
    
    // Execute migration command
    $command = 'php database/migrate.php migrate 2>&1';
    $output = shell_exec($command);
    
    if ($output) {
        $success = strpos($output, '✅') !== false || strpos($output, 'success') !== false;
    }
    
    ob_end_clean();
}

// Get migration status
require_once __DIR__ . '/../config/loadEnv.php';
require_once __DIR__ . '/../database/db_connections.php';
require_once __DIR__ . '/../includes/database/migration_manager.php';

try {
    $migrationManager = new MigrationManager($connection);
    $status = $migrationManager->getStatus();
} catch (Exception $e) {
    $status = ['error' => $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migrations - 11классники</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .output {
            background: #1e1e1e;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            margin: 15px 0;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .status-table th,
        .status-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .status-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .status-applied {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-pending {
            color: #f39c12;
            font-weight: bold;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Database Migrations</h1>
            <p>Manage database schema changes and updates</p>
        </div>
        
        <?php if ($output): ?>
            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
                <?= $success ? '✅ Migrations executed successfully!' : '❌ Migration execution had issues' ?>
            </div>
            
            <div class="card">
                <h3>Migration Output:</h3>
                <div class="output"><?= htmlspecialchars($output) ?></div>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Current Migration Status</h3>
            
            <?php if (isset($status['error'])): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?= htmlspecialchars($status['error']) ?>
                </div>
            <?php else: ?>
                <p><strong>Total migrations:</strong> <?= $status['total_migrations'] ?></p>
                <p><strong>Applied:</strong> <?= $status['applied_migrations'] ?></p>
                <p><strong>Pending:</strong> <?= $status['pending_migrations'] ?></p>
                
                <?php if (!empty($status['migrations'])): ?>
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th>Migration</th>
                                <th>Status</th>
                                <th>Applied At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status['migrations'] as $migration): ?>
                                <tr>
                                    <td><?= htmlspecialchars($migration['name']) ?></td>
                                    <td class="<?= $migration['applied'] ? 'status-applied' : 'status-pending' ?>">
                                        <?= $migration['applied'] ? '✅ Applied' : '⏳ Pending' ?>
                                    </td>
                                    <td><?= $migration['applied_at'] ?: '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h3>Run Migrations</h3>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> Running migrations will modify your database structure. 
                Make sure you have a backup before proceeding.
            </div>
            
            <form method="post">
                <p>This will run all pending database migrations to update your database with new features:</p>
                <ul>
                    <li>Enhanced comment system with reactions</li>
                    <li>User tracking for failed logins</li>
                    <li>Remember me functionality</li>
                    <li>Password reset tokens</li>
                </ul>
                
                <button type="submit" name="run_migrations" class="btn btn-success" 
                        onclick="return confirm('Are you sure you want to run database migrations? This will modify your database structure.')">
                    🗄️ Run Migrations
                </button>
            </form>
        </div>
        
        <a href="/admin/cache-management.php" class="back-link">← Back to Admin Tools</a>
    </div>
</body>
</html>
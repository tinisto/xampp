<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check admin authentication
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

$message = '';
$messageType = '';

// Handle backup actions
if ($_POST['action'] ?? '') {
    switch ($_POST['action']) {
        case 'create_db_backup':
            $message = createDatabaseBackup();
            $messageType = 'success';
            break;
        case 'cleanup_old_backups':
            $message = cleanupOldBackups();
            $messageType = 'info';
            break;
    }
}

function createDatabaseBackup() {
    global $connection;
    
    try {
        // Get database name from connection
        $dbName = $_ENV['DB_DATABASE'] ?? '11klassniki_claude';
        $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/backups';
        
        // Create backups directory if it doesn't exist
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/db_backup_' . $timestamp . '.sql';
        
        // Get all tables
        $tables = [];
        $result = $connection->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        
        $output = "-- Database Backup Created: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Database: $dbName\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $result = $connection->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_row();
            $output .= "\n-- Table: $table\n";
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            $output .= $row[1] . ";\n\n";
            
            // Get table data
            $result = $connection->query("SELECT * FROM `$table`");
            if ($result->num_rows > 0) {
                $output .= "-- Data for table `$table`\n";
                while ($row = $result->fetch_assoc()) {
                    $columns = array_keys($row);
                    $values = array_map(function($value) use ($connection) {
                        return $value === null ? 'NULL' : "'" . $connection->real_escape_string($value) . "'";
                    }, array_values($row));
                    
                    $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            }
        }
        
        file_put_contents($backupFile, $output);
        
        return "✅ Database backup created successfully: " . basename($backupFile) . " (" . formatBytes(filesize($backupFile)) . ")";
        
    } catch (Exception $e) {
        error_log("Backup error: " . $e->getMessage());
        return "❌ Backup failed: " . $e->getMessage();
    }
}

function cleanupOldBackups() {
    $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/backups';
    $deleted = 0;
    $totalSize = 0;
    
    if (is_dir($backupDir)) {
        $files = glob($backupDir . '/db_backup_*.sql');
        
        // Sort by modification time, newest first
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Keep only the 5 most recent backups
        $filesToDelete = array_slice($files, 5);
        
        foreach ($filesToDelete as $file) {
            $totalSize += filesize($file);
            unlink($file);
            $deleted++;
        }
    }
    
    return "🗑️ Cleaned up $deleted old backup files, freed " . formatBytes($totalSize);
}

function getBackupsList() {
    $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/backups';
    $backups = [];
    
    if (is_dir($backupDir)) {
        $files = glob($backupDir . '/db_backup_*.sql');
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => formatBytes(filesize($file)),
                'date' => date('d.m.Y H:i', filemtime($file)),
                'age' => timeAgo(filemtime($file))
            ];
        }
        
        // Sort by date, newest first
        usort($backups, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
    }
    
    return $backups;
}

function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

function timeAgo($timestamp) {
    $diff = time() - $timestamp;
    if ($diff < 60) return $diff . ' сек назад';
    if ($diff < 3600) return floor($diff/60) . ' мин назад';
    if ($diff < 86400) return floor($diff/3600) . ' ч назад';
    return floor($diff/86400) . ' дн назад';
}

$backups = getBackupsList();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup Tool - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2.2rem; margin-bottom: 10px; }
        .content { padding: 30px; }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #28a745;
        }
        .card h3 {
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            margin: 5px 5px 5px 0;
        }
        .btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .backups-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .backups-table th,
        .backups-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .backups-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .no-backups {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .info-box h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💾 Database Backup Tool</h1>
            <p>Manage MySQL database backups</p>
        </div>
        
        <div class="content">
            <a href="/dashboard" class="back-link">← Back to Dashboard</a>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <h4>📝 About Database Backups</h4>
                <p><strong>Code backups:</strong> Your PHP files are backed up on GitHub ✅</p>
                <p><strong>Database backups:</strong> MySQL data needs separate backup (users, news, schools, etc.)</p>
                <p><strong>Recommendation:</strong> Create weekly database backups, keep 5 most recent</p>
            </div>
            
            <div class="card">
                <h3>🔄 Create New Backup</h3>
                <p>Create a complete backup of the MySQL database including all tables and data.</p>
                <form method="post" style="margin-top: 15px;">
                    <input type="hidden" name="action" value="create_db_backup">
                    <button type="submit" class="btn" onclick="this.innerHTML='⏳ Creating backup...'; this.disabled=true;">
                        📦 Create Database Backup
                    </button>
                </form>
            </div>
            
            <div class="card">
                <h3>🗂️ Existing Backups</h3>
                <?php if (empty($backups)): ?>
                    <div class="no-backups">
                        📭 No backups found. Create your first backup above.
                    </div>
                <?php else: ?>
                    <table class="backups-table">
                        <thead>
                            <tr>
                                <th>📄 Filename</th>
                                <th>📊 Size</th>
                                <th>📅 Created</th>
                                <th>⏰ Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($backup['name']) ?></code></td>
                                <td><?= $backup['size'] ?></td>
                                <td><?= $backup['date'] ?></td>
                                <td><?= $backup['age'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if (count($backups) > 5): ?>
                    <form method="post" style="margin-top: 20px;">
                        <input type="hidden" name="action" value="cleanup_old_backups">
                        <button type="submit" class="btn btn-danger">
                            🗑️ Clean Up Old Backups (keep 5 newest)
                        </button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h3>ℹ️ Backup Information</h3>
                <ul style="color: #666; line-height: 1.8; padding-left: 20px;">
                    <li><strong>Location:</strong> <code>/backups/</code> directory on server</li>
                    <li><strong>Format:</strong> SQL dump files (.sql)</li>
                    <li><strong>Contents:</strong> All tables, structure, and data</li>
                    <li><strong>Automatic cleanup:</strong> Keeps 5 most recent backups</li>
                    <li><strong>File naming:</strong> <code>db_backup_YYYY-MM-DD_HH-mm-ss.sql</code></li>
                </ul>
            </div>
            
            <div style="text-align: center; padding: 20px;">
                <a href="/dashboard" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
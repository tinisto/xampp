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
        case 'download_backup':
            downloadBackup($_POST['filename']);
            exit();
    }
}

// Handle direct download
if ($_GET['download'] ?? '') {
    downloadBackup($_GET['download']);
    exit();
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
        $output .= "-- Database: $dbName\n";
        $output .= "-- Server: " . $_SERVER['HTTP_HOST'] . "\n";
        $output .= "-- Tables: " . count($tables) . "\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $result = $connection->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_row();
            $output .= "\n-- ========================================\n";
            $output .= "-- Table: $table\n";
            $output .= "-- ========================================\n";
            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            $output .= $row[1] . ";\n\n";
            
            // Get table data
            $result = $connection->query("SELECT * FROM `$table`");
            if ($result->num_rows > 0) {
                $output .= "-- Data for table `$table` (" . $result->num_rows . " rows)\n";
                while ($row = $result->fetch_assoc()) {
                    $columns = array_keys($row);
                    $values = array_map(function($value) use ($connection) {
                        return $value === null ? 'NULL' : "'" . $connection->real_escape_string($value) . "'";
                    }, array_values($row));
                    
                    $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            } else {
                $output .= "-- No data for table `$table`\n\n";
            }
        }
        
        file_put_contents($backupFile, $output);
        
        return "✅ Database backup created: " . basename($backupFile) . " (" . formatBytes(filesize($backupFile)) . ")";
        
    } catch (Exception $e) {
        error_log("Backup error: " . $e->getMessage());
        return "❌ Backup failed: " . $e->getMessage();
    }
}

function downloadBackup($filename) {
    $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/backups';
    $filePath = $backupDir . '/' . basename($filename); // Security: only filename, no path traversal
    
    if (!file_exists($filePath) || !is_file($filePath)) {
        http_response_code(404);
        die('Backup file not found');
    }
    
    // Security check: only allow .sql files
    if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'sql') {
        http_response_code(403);
        die('Invalid file type');
    }
    
    // Send file for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache, must-revalidate');
    
    readfile($filePath);
    exit();
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
                'size_bytes' => filesize($file),
                'date' => date('d.m.Y H:i', filemtime($file)),
                'timestamp' => filemtime($file),
                'age' => timeAgo(filemtime($file))
            ];
        }
        
        // Sort by timestamp, newest first
        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
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
$totalBackupSize = array_sum(array_column($backups, 'size_bytes'));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup Manager - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
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
        .btn-small {
            padding: 6px 12px;
            font-size: 0.875rem;
        }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-info { background: #17a2b8; }
        .btn-info:hover { background: #138496; }
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
        .backups-table tr:hover {
            background: #f8f9fa;
        }
        .no-backups {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #28a745;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
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
        .filename { font-family: 'Monaco', 'Consolas', monospace; font-size: 0.9rem; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💾 Database Backup Manager</h1>
            <p>Create, download, and manage MySQL database backups</p>
        </div>
        
        <div class="content">
            <a href="/dashboard" class="back-link">← Back to Dashboard</a>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number"><?= count($backups) ?></div>
                    <div class="stat-label">Total Backups</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?= formatBytes($totalBackupSize) ?></div>
                    <div class="stat-label">Total Size</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?= !empty($backups) ? $backups[0]['age'] : 'Never' ?></div>
                    <div class="stat-label">Last Backup</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">/backups/</div>
                    <div class="stat-label">Server Location</div>
                </div>
            </div>
            
            <div class="info-box">
                <h4>📋 Backup Strategy</h4>
                <p><strong>✅ Code Backups:</strong> Your PHP files are automatically backed up on GitHub</p>
                <p><strong>💾 Database Backups:</strong> Create manually or set up cron job for automation</p>
                <p><strong>📥 Downloads:</strong> Click "Download" to save backup files to your computer</p>
                <p><strong>🔄 Auto-cleanup:</strong> Keeps 5 most recent backups to save disk space</p>
            </div>
            
            <div class="card">
                <h3>🔄 Create New Backup</h3>
                <p>Create a complete backup of all database tables and data. This includes users, news, schools, comments, and all other data.</p>
                <form method="post" style="margin-top: 15px;">
                    <input type="hidden" name="action" value="create_db_backup">
                    <button type="submit" class="btn" onclick="this.innerHTML='⏳ Creating backup...'; this.disabled=true;">
                        📦 Create Database Backup
                    </button>
                </form>
            </div>
            
            <div class="card">
                <h3>🗂️ Backup Files</h3>
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
                                <th>🔧 Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td class="filename"><?= htmlspecialchars($backup['name']) ?></td>
                                <td><?= $backup['size'] ?></td>
                                <td><?= $backup['date'] ?></td>
                                <td><?= $backup['age'] ?></td>
                                <td class="actions">
                                    <a href="?download=<?= urlencode($backup['name']) ?>" 
                                       class="btn btn-info btn-small"
                                       onclick="this.innerHTML='⏳ Downloading...'">
                                        📥 Download
                                    </a>
                                </td>
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
                <h3>🤖 Automation Options</h3>
                <p>For automatic backups, you can set up a cron job on your server:</p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 15px 0; font-family: monospace; font-size: 0.9rem;">
                    # Weekly backup every Sunday at 2 AM<br>
                    0 2 * * 0 /usr/bin/curl https://11klassniki.ru/admin-backup-tool.php?action=create_db_backup
                </div>
                <p><small>Contact your hosting provider to set up automated backups.</small></p>
            </div>
            
            <div style="text-align: center; padding: 20px;">
                <a href="/dashboard" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/../config/loadEnv.php';
require_once __DIR__ . '/../database/db_connections.php';
require_once __DIR__ . '/../includes/cache/page_cache.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$message = '';
$messageType = '';

// Handle cache actions
if ($_POST) {
    switch ($_POST['action'] ?? '') {
        case 'clear_all':
            $deleted = PageCache::clear();
            $message = "Cleared {$deleted} cache files";
            $messageType = 'success';
            break;
            
        case 'cleanup':
            $maxAge = intval($_POST['max_age'] ?? 86400);
            $cleaned = PageCache::cleanup($maxAge);
            $message = "Cleaned {$cleaned} expired cache files";
            $messageType = 'success';
            break;
            
        case 'invalidate_content':
            $type = $_POST['content_type'] ?? 'all';
            $id = $_POST['content_id'] ?? null;
            $deleted = PageCache::invalidateContent($type, $id);
            $message = "Invalidated {$deleted} cache files for {$type}";
            $messageType = 'success';
            break;
            
        case 'toggle_cache':
            $enabled = $_POST['enabled'] === '1';
            PageCache::setEnabled($enabled);
            $message = 'Cache ' . ($enabled ? 'enabled' : 'disabled');
            $messageType = 'success';
            break;
            
        case 'warm_up':
            $urls = explode("\n", trim($_POST['urls'] ?? ''));
            $urls = array_filter(array_map('trim', $urls));
            
            if (!empty($urls)) {
                $results = PageCache::warmUp($urls);
                $successful = count(array_filter($results, function($r) { return $r['success']; }));
                $message = "Warmed up {$successful}/" . count($urls) . " URLs";
                $messageType = 'success';
            } else {
                $message = "No URLs provided for warm-up";
                $messageType = 'error';
            }
            break;
    }
}

$stats = PageCache::getStats();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Management - 11классники</title>
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
            max-width: 1200px;
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
        
        .header h1 {
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .action-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .action-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-enabled {
            background: #27ae60;
        }
        
        .status-disabled {
            background: #e74c3c;
        }
        
        .cache-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 25px;
            background: #ccc;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .toggle-switch.active {
            background: #27ae60;
        }
        
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 21px;
            height: 21px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }
        
        .toggle-switch.active::after {
            transform: translateX(25px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📄 Cache Management</h1>
            <p>Manage page caching for optimal performance</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <!-- Cache Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Cache Status</h3>
                <div class="cache-toggle">
                    <span class="status-indicator <?= $stats['enabled'] ? 'status-enabled' : 'status-disabled' ?>"></span>
                    <span><?= $stats['enabled'] ? 'Enabled' : 'Disabled' ?></span>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>Cached Files</h3>
                <div class="stat-value"><?= number_format($stats['total_files']) ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Cache Size</h3>
                <div class="stat-value"><?= $stats['total_size_formatted'] ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Cache Directory</h3>
                <div style="font-size: 0.9em; word-break: break-all;">
                    <?= htmlspecialchars($stats['cache_dir']) ?>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="actions-grid">
            <!-- Toggle Cache -->
            <div class="action-card">
                <h3>🔧 Cache Settings</h3>
                <form method="post">
                    <input type="hidden" name="action" value="toggle_cache">
                    <div class="form-group">
                        <label>Cache Status</label>
                        <select name="enabled">
                            <option value="1" <?= $stats['enabled'] ? 'selected' : '' ?>>Enabled</option>
                            <option value="0" <?= !$stats['enabled'] ? 'selected' : '' ?>>Disabled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Update Settings</button>
                </form>
            </div>
            
            <!-- Clear All Cache -->
            <div class="action-card">
                <h3>🗑️ Clear All Cache</h3>
                <p>Remove all cached files. Use with caution as this will impact performance until cache is rebuilt.</p>
                <form method="post" onsubmit="return confirm('Are you sure you want to clear all cache?')">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="btn btn-danger">Clear All Cache</button>
                </form>
            </div>
            
            <!-- Cleanup Expired -->
            <div class="action-card">
                <h3>🧹 Cleanup Expired</h3>
                <form method="post">
                    <input type="hidden" name="action" value="cleanup">
                    <div class="form-group">
                        <label>Max Age (seconds)</label>
                        <select name="max_age">
                            <option value="3600">1 Hour</option>
                            <option value="86400" selected>24 Hours</option>
                            <option value="604800">1 Week</option>
                            <option value="2592000">30 Days</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Cleanup Old Files</button>
                </form>
            </div>
            
            <!-- Invalidate Content -->
            <div class="action-card">
                <h3>♻️ Invalidate Content</h3>
                <form method="post">
                    <input type="hidden" name="action" value="invalidate_content">
                    <div class="form-group">
                        <label>Content Type</label>
                        <select name="content_type">
                            <option value="all">All Content</option>
                            <option value="news">News</option>
                            <option value="post">Posts</option>
                            <option value="user">User Profiles</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Content ID (optional)</label>
                        <input type="number" name="content_id" placeholder="Leave empty for all">
                    </div>
                    <button type="submit" class="btn">Invalidate Cache</button>
                </form>
            </div>
            
            <!-- Cache Warm-up -->
            <div class="action-card">
                <h3>🔥 Cache Warm-up</h3>
                <form method="post">
                    <input type="hidden" name="action" value="warm_up">
                    <div class="form-group">
                        <label>URLs to Warm Up (one per line)</label>
                        <textarea name="urls" placeholder="/
/news
/posts
/category/education"><?= isset($_POST['urls']) ? htmlspecialchars($_POST['urls']) : '' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Warm Up Cache</button>
                </form>
            </div>
            
            <!-- Cache Analytics -->
            <div class="action-card">
                <h3>📊 Cache Analytics</h3>
                <?php if ($stats['total_files'] > 0): ?>
                    <p><strong>Oldest Cache:</strong> <?= $stats['oldest_file'] ?><br>
                    <small>Created: <?= date('Y-m-d H:i:s', $stats['oldest_time']) ?></small></p>
                    
                    <p><strong>Newest Cache:</strong> <?= $stats['newest_file'] ?><br>
                    <small>Created: <?= date('Y-m-d H:i:s', $stats['newest_time']) ?></small></p>
                <?php else: ?>
                    <p>No cache files found.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($results)): ?>
            <div class="action-card" style="margin-top: 20px;">
                <h3>📈 Warm-up Results</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">URL</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Status</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Duration</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($result['url']) ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;">
                                    <?= $result['success'] ? '✅ Success' : '❌ Failed' ?>
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= $result['duration_ms'] ?>ms</td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= number_format($result['size']) ?> bytes</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
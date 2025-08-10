<?php
/**
 * System Health Check
 * Real-time monitoring of application performance and database status
 */

require_once __DIR__ . '/database/db_modern.php';

$startTime = microtime(true);
$checks = [];
$overallStatus = 'healthy';

// Function to add check result
function addCheck($name, $status, $message = '', $responseTime = null) {
    global $checks;
    $checks[] = [
        'name' => $name,
        'status' => $status, // healthy, warning, error
        'message' => $message,
        'response_time' => $responseTime
    ];
}

// Check database connectivity
try {
    $dbStart = microtime(true);
    $result = db_query("SELECT 1 as test");
    $dbTime = (microtime(true) - $dbStart) * 1000;
    
    if ($result) {
        addCheck('Database Connection', 'healthy', 'Connected successfully', $dbTime);
    } else {
        addCheck('Database Connection', 'error', 'Connection failed');
        $overallStatus = 'error';
    }
} catch (Exception $e) {
    addCheck('Database Connection', 'error', $e->getMessage());
    $overallStatus = 'error';
}

// Check database tables
$requiredTables = ['news', 'posts', 'users', 'events', 'favorites', 'comments'];
$tableCheck = [];

foreach ($requiredTables as $table) {
    try {
        $count = db_fetch_column("SELECT COUNT(*) FROM $table");
        $tableCheck[$table] = $count;
    } catch (Exception $e) {
        $tableCheck[$table] = 'ERROR: ' . $e->getMessage();
        $overallStatus = 'error';
    }
}

addCheck('Database Tables', 'healthy', implode(', ', array_map(function($table, $count) {
    return "$table: $count";
}, array_keys($tableCheck), $tableCheck)));

// Check file system permissions
$writableDirs = [
    'images/uploads/',
    'cache/',
    'logs/'
];

$permissionIssues = [];
foreach ($writableDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    if (!is_writable($dir)) {
        $permissionIssues[] = $dir;
    }
}

if (empty($permissionIssues)) {
    addCheck('File Permissions', 'healthy', 'All directories writable');
} else {
    addCheck('File Permissions', 'warning', 'Non-writable: ' . implode(', ', $permissionIssues));
    if ($overallStatus === 'healthy') $overallStatus = 'warning';
}

// Check memory usage
$memoryUsage = memory_get_usage(true);
$memoryLimit = ini_get('memory_limit');
$memoryLimitBytes = $memoryLimit === '-1' ? PHP_INT_MAX : 
    (int)$memoryLimit * (strpos($memoryLimit, 'M') ? 1024*1024 : 
    (strpos($memoryLimit, 'G') ? 1024*1024*1024 : 1));

$memoryPercent = ($memoryUsage / $memoryLimitBytes) * 100;

if ($memoryPercent < 70) {
    addCheck('Memory Usage', 'healthy', sprintf('%.1f%% used (%s / %s)', 
        $memoryPercent, formatBytes($memoryUsage), $memoryLimit));
} elseif ($memoryPercent < 90) {
    addCheck('Memory Usage', 'warning', sprintf('%.1f%% used (%s / %s)', 
        $memoryPercent, formatBytes($memoryUsage), $memoryLimit));
    if ($overallStatus === 'healthy') $overallStatus = 'warning';
} else {
    addCheck('Memory Usage', 'error', sprintf('%.1f%% used (%s / %s)', 
        $memoryPercent, formatBytes($memoryUsage), $memoryLimit));
    $overallStatus = 'error';
}

// Check PHP version and extensions
$phpVersion = PHP_VERSION;
$requiredExtensions = ['pdo', 'pdo_sqlite', 'json', 'mbstring', 'curl'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (empty($missingExtensions)) {
    addCheck('PHP Extensions', 'healthy', "PHP $phpVersion with all required extensions");
} else {
    addCheck('PHP Extensions', 'error', "Missing extensions: " . implode(', ', $missingExtensions));
    $overallStatus = 'error';
}

// Check recent errors (if log file exists)
$errorLogPath = 'logs/error.log';
$recentErrors = 0;
if (file_exists($errorLogPath)) {
    $logContent = file_get_contents($errorLogPath);
    $lines = explode("\n", $logContent);
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    foreach ($lines as $line) {
        if (strpos($line, $yesterday) !== false || strpos($line, date('Y-m-d')) !== false) {
            $recentErrors++;
        }
    }
}

if ($recentErrors == 0) {
    addCheck('Error Log', 'healthy', 'No recent errors');
} elseif ($recentErrors < 10) {
    addCheck('Error Log', 'warning', "$recentErrors errors in last 24h");
    if ($overallStatus === 'healthy') $overallStatus = 'warning';
} else {
    addCheck('Error Log', 'error', "$recentErrors errors in last 24h");
    $overallStatus = 'error';
}

// Calculate total response time
$totalTime = (microtime(true) - $startTime) * 1000;

// Format bytes helper function
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Output format based on request
$format = $_GET['format'] ?? 'html';

if ($format === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $overallStatus,
        'timestamp' => date('c'),
        'response_time' => round($totalTime, 2),
        'checks' => $checks,
        'system_info' => [
            'php_version' => $phpVersion,
            'memory_usage' => formatBytes($memoryUsage),
            'memory_limit' => $memoryLimit,
            'server_time' => date('Y-m-d H:i:s')
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

// HTML Output
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Check - 11klassniki.ru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-healthy { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-error { color: #dc3545; }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-healthy { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-error { background: #f8d7da; color: #721c24; }
        .refresh-indicator {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .metric-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .metric-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="mb-0">
                    <i class="fas fa-heartbeat me-2"></i>
                    System Health Check
                </h1>
                <p class="text-muted">Real-time monitoring of 11klassniki.ru system status</p>
            </div>
            <div class="col-auto">
                <button onclick="location.reload()" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh
                </button>
                <a href="?format=json" class="btn btn-outline-secondary">
                    <i class="fas fa-code"></i> JSON
                </a>
            </div>
        </div>

        <!-- Overall Status -->
        <div class="row mb-4">
            <div class="col">
                <div class="card metric-card">
                    <div class="card-body text-center">
                        <div class="display-6 mb-2">
                            <?php if ($overallStatus === 'healthy'): ?>
                                <i class="fas fa-check-circle status-healthy"></i>
                            <?php elseif ($overallStatus === 'warning'): ?>
                                <i class="fas fa-exclamation-triangle status-warning"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle status-error"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="status-<?= $overallStatus ?>">
                            <?= ucfirst($overallStatus) ?>
                        </h3>
                        <p class="text-muted mb-0">
                            System check completed in <?= round($totalTime, 2) ?>ms
                        </p>
                        <small class="text-muted">
                            Last updated: <?= date('Y-m-d H:i:s') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Checks -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>
                            Health Checks (<?= count($checks) ?>)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($checks as $check): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <strong class="me-2"><?= htmlspecialchars($check['name']) ?></strong>
                                        <span class="status-badge badge-<?= $check['status'] ?>">
                                            <?= $check['status'] ?>
                                        </span>
                                    </div>
                                    <?php if ($check['message']): ?>
                                    <div class="text-muted small">
                                        <?= htmlspecialchars($check['message']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($check['response_time']): ?>
                                <span class="badge bg-secondary">
                                    <?= round($check['response_time'], 2) ?>ms
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>PHP Version:</strong></td>
                                        <td><?= PHP_VERSION ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Memory Usage:</strong></td>
                                        <td><?= formatBytes($memoryUsage) ?> / <?= $memoryLimit ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Server Time:</strong></td>
                                        <td><?= date('Y-m-d H:i:s T') ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Database:</strong></td>
                                        <td>SQLite (Local Development)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Environment:</strong></td>
                                        <td>Development</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Response Time:</strong></td>
                                        <td><?= round($totalTime, 2) ?>ms</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home me-1"></i> Back to Homepage
                </a>
                <a href="/admin" class="btn btn-outline-secondary">
                    <i class="fas fa-cog me-1"></i> Admin Panel
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        let autoRefresh = setInterval(() => {
            const refreshIcon = document.getElementById('refreshIcon');
            refreshIcon.classList.add('refresh-indicator');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }, 30000);

        // Manual refresh with animation
        function refreshWithAnimation() {
            const refreshIcon = document.getElementById('refreshIcon');
            refreshIcon.classList.add('refresh-indicator');
            clearInterval(autoRefresh);
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    </script>
</body>
</html>
<?php
// Log health check if needed
$logData = [
    'timestamp' => date('c'),
    'status' => $overallStatus,
    'response_time' => round($totalTime, 2),
    'checks_count' => count($checks)
];

// Could write to log file here if needed
// file_put_contents('logs/health.log', json_encode($logData) . "\n", FILE_APPEND);
?>
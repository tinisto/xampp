<?php
/**
 * Monitoring Dashboard for 11klassniki
 * Admin interface for viewing logs and metrics
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security/security_bootstrap.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/monitoring/error_logger.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/monitoring/performance_monitor.php';

// Require admin access
SecurityBootstrap::requireAdmin();

$tab = $_GET['tab'] ?? 'overview';
$timeframe = (int)($_GET['timeframe'] ?? 24); // hours

// Get data based on tab
$data = [];
switch ($tab) {
    case 'errors':
        $data['errors'] = ErrorLogger::getRecentErrors(100);
        $data['stats'] = ErrorLogger::getErrorStats($timeframe);
        break;
    case 'performance':
        $data['metrics'] = PerformanceMonitor::getCurrentMetrics();
        $data['health'] = PerformanceMonitor::getSystemHealth();
        break;
    case 'overview':
    default:
        $data['errors'] = ErrorLogger::getRecentErrors(20);
        $data['error_stats'] = ErrorLogger::getErrorStats($timeframe);
        $data['metrics'] = PerformanceMonitor::getCurrentMetrics();
        $data['health'] = PerformanceMonitor::getSystemHealth();
        break;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Dashboard - 11классники</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), #1d4ed8);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .metric-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-ok { background: #d1fae5; color: #065f46; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-error { background: #fee2e2; color: #991b1b; }
        
        .log-entry {
            border-left: 4px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f9fafb;
            border-radius: 0 0.25rem 0.25rem 0;
        }
        
        .log-entry.error { border-left-color: var(--danger); }
        .log-entry.warning { border-left-color: var(--warning); }
        .log-entry.critical { border-left-color: #dc2626; }
        
        .log-timestamp {
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        pre {
            background: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            overflow-x: auto;
        }
        
        .chart-container {
            height: 200px;
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Monitoring Dashboard
                    </h1>
                    <p class="mb-0 opacity-75">System health and error monitoring</p>
                </div>
                <div class="col-auto">
                    <a href="/dashboard" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'overview' ? 'active' : '' ?>" href="?tab=overview">
                    <i class="fas fa-tachometer-alt me-1"></i>
                    Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'errors' ? 'active' : '' ?>" href="?tab=errors">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Errors
                    <?php if (isset($data['error_stats']['total']) && $data['error_stats']['total'] > 0): ?>
                        <span class="badge bg-danger ms-1"><?= $data['error_stats']['total'] ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'performance' ? 'active' : '' ?>" href="?tab=performance">
                    <i class="fas fa-bolt me-1"></i>
                    Performance
                </a>
            </li>
        </ul>

        <!-- Overview Tab -->
        <?php if ($tab === 'overview'): ?>
            <div class="row">
                <!-- System Health -->
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-heart text-<?= $data['health']['status'] === 'healthy' ? 'success' : ($data['health']['status'] === 'warning' ? 'warning' : 'danger') ?> fa-2x"></i>
                            </div>
                            <div>
                                <div class="metric-value text-<?= $data['health']['status'] === 'healthy' ? 'success' : ($data['health']['status'] === 'warning' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($data['health']['status']) ?>
                                </div>
                                <div class="metric-label">System Health</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Count -->
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                            </div>
                            <div>
                                <div class="metric-value text-warning">
                                    <?= $data['error_stats']['total'] ?? 0 ?>
                                </div>
                                <div class="metric-label">Errors (24h)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-memory text-info fa-2x"></i>
                            </div>
                            <div>
                                <div class="metric-value text-info">
                                    <?= round($data['metrics']['memory_usage'] / 1024 / 1024, 1) ?>MB
                                </div>
                                <div class="metric-label">Memory Usage</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Query Count -->
                <div class="col-md-6 col-lg-3">
                    <div class="metric-card">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-database text-primary fa-2x"></i>
                            </div>
                            <div>
                                <div class="metric-value text-primary">
                                    <?= $data['metrics']['query_count'] ?>
                                </div>
                                <div class="metric-label">DB Queries</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            <?php if (!empty($data['errors'])): ?>
                <div class="metric-card">
                    <h5 class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Recent Errors
                    </h5>
                    <?php foreach (array_slice($data['errors'], 0, 5) as $error): ?>
                        <div class="log-entry <?= strtolower($error['level']) ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?= SecurityBootstrap::out($error['message']) ?></strong>
                                    <?php if ($error['file']): ?>
                                        <br><small class="text-muted">
                                            <?= SecurityBootstrap::out(basename($error['file'])) ?>:<?= $error['line'] ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <span class="status-badge status-<?= strtolower($error['level']) === 'critical' ? 'error' : (strtolower($error['level']) === 'warning' ? 'warning' : 'ok') ?>">
                                        <?= strtoupper($error['level']) ?>
                                    </span>
                                    <br><small class="log-timestamp"><?= $error['timestamp'] ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="?tab=errors" class="btn btn-outline-primary btn-sm">View All Errors</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Errors Tab -->
        <?php if ($tab === 'errors'): ?>
            <!-- Error Statistics -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="metric-card">
                        <h5 class="mb-3">Error Statistics (Last <?= $timeframe ?> hours)</h5>
                        <div class="row">
                            <?php foreach ($data['stats']['by_level'] ?? [] as $level => $count): ?>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center p-2">
                                        <div class="h4 text-<?= $level === 'critical' ? 'danger' : ($level === 'warning' ? 'warning' : 'info') ?>">
                                            <?= $count ?>
                                        </div>
                                        <div class="small text-muted"><?= ucfirst($level) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <h5 class="mb-3">Total Errors</h5>
                        <div class="text-center">
                            <div class="h2 text-warning"><?= $data['stats']['total'] ?></div>
                            <div class="small text-muted">Last <?= $timeframe ?> hours</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error List -->
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Recent Errors</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="?tab=errors&timeframe=1" class="btn btn-outline-primary <?= $timeframe === 1 ? 'active' : '' ?>">1h</a>
                        <a href="?tab=errors&timeframe=6" class="btn btn-outline-primary <?= $timeframe === 6 ? 'active' : '' ?>">6h</a>
                        <a href="?tab=errors&timeframe=24" class="btn btn-outline-primary <?= $timeframe === 24 ? 'active' : '' ?>">24h</a>
                        <a href="?tab=errors&timeframe=168" class="btn btn-outline-primary <?= $timeframe === 168 ? 'active' : '' ?>">7d</a>
                    </div>
                </div>

                <?php if (empty($data['errors'])): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>No errors found!</h5>
                        <p>Your application is running smoothly.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['errors'] as $error): ?>
                        <div class="log-entry <?= strtolower($error['level']) ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <strong><?= SecurityBootstrap::out($error['message']) ?></strong>
                                    <?php if ($error['file']): ?>
                                        <br><small class="text-muted">
                                            File: <?= SecurityBootstrap::out($error['file']) ?>:<?= $error['line'] ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if ($error['url'] !== 'CLI'): ?>
                                        <br><small class="text-muted">
                                            URL: <?= SecurityBootstrap::out($error['url']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="status-badge status-<?= strtolower($error['level']) === 'critical' ? 'error' : (strtolower($error['level']) === 'warning' ? 'warning' : 'ok') ?>">
                                        <?= strtoupper($error['level']) ?>
                                    </span>
                                    <br><small class="log-timestamp"><?= $error['timestamp'] ?></small>
                                    <?php if ($error['user_id']): ?>
                                        <br><small class="text-muted">User: <?= $error['user_id'] ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($error['context'])): ?>
                                <details class="mt-2">
                                    <summary class="btn btn-link btn-sm p-0">Show Context</summary>
                                    <pre class="mt-2"><?= SecurityBootstrap::out(json_encode($error['context'], JSON_PRETTY_PRINT)) ?></pre>
                                </details>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Performance Tab -->
        <?php if ($tab === 'performance'): ?>
            <div class="row">
                <!-- System Health Details -->
                <div class="col-md-6">
                    <div class="metric-card">
                        <h5 class="mb-3">
                            <i class="fas fa-heartbeat text-success me-2"></i>
                            System Health
                        </h5>
                        <?php foreach ($data['health']['checks'] as $check => $info): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <div>
                                    <strong><?= ucwords(str_replace('_', ' ', $check)) ?></strong>
                                    <?php if (isset($info['usage_percent'])): ?>
                                        <br><small class="text-muted"><?= $info['usage_percent'] ?>% used</small>
                                    <?php endif; ?>
                                </div>
                                <span class="status-badge status-<?= $info['status'] ?>">
                                    <?= strtoupper($info['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Current Metrics -->
                <div class="col-md-6">
                    <div class="metric-card">
                        <h5 class="mb-3">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i>
                            Current Metrics
                        </h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-2">
                                    <div class="h5 text-info"><?= round($data['metrics']['memory_usage'] / 1024 / 1024, 1) ?>MB</div>
                                    <div class="small text-muted">Memory Usage</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2">
                                    <div class="h5 text-primary"><?= $data['metrics']['query_count'] ?></div>
                                    <div class="small text-muted">DB Queries</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2">
                                    <div class="h5 text-warning"><?= round($data['metrics']['query_time'] * 1000, 1) ?>ms</div>
                                    <div class="small text-muted">Query Time</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2">
                                    <div class="h5 text-success"><?= count($data['metrics']['active_timers']) ?></div>
                                    <div class="small text-muted">Active Timers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <?php if (!empty($data['metrics']['completed_metrics'])): ?>
                <div class="metric-card">
                    <h5 class="mb-3">
                        <i class="fas fa-stopwatch text-info me-2"></i>
                        Recent Performance Metrics
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Operation</th>
                                    <th>Duration</th>
                                    <th>Memory Used</th>
                                    <th>Peak Memory</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['metrics']['completed_metrics'] as $metric): ?>
                                    <tr>
                                        <td><?= SecurityBootstrap::out($metric['name']) ?></td>
                                        <td>
                                            <span class="<?= $metric['duration'] > 1.0 ? 'text-danger' : ($metric['duration'] > 0.5 ? 'text-warning' : 'text-success') ?>">
                                                <?= round($metric['duration'] * 1000, 2) ?>ms
                                            </span>
                                        </td>
                                        <td><?= round($metric['memory_used'] / 1024, 1) ?>KB</td>
                                        <td><?= round($metric['memory_peak'] / 1024 / 1024, 1) ?>MB</td>
                                        <td><?= date('H:i:s', $metric['timestamp']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);

        // Add refresh button behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Add a refresh indicator
            const refreshIndicator = document.createElement('div');
            refreshIndicator.className = 'position-fixed bottom-0 end-0 m-3';
            refreshIndicator.innerHTML = '<small class="text-muted"><i class="fas fa-sync-alt fa-spin"></i> Auto-refreshing in 30s</small>';
            document.body.appendChild(refreshIndicator);
        });
    </script>
</body>
</html>
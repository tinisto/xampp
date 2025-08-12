<?php
/**
 * System Overview Dashboard
 * Central hub showing all system components and their status
 */

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /unauthorized.php');
    exit;
}

require_once __DIR__ . '/database/db_modern.php';

// Get system statistics
$stats = [
    'content' => [
        'news' => db_fetch_column("SELECT COUNT(*) FROM news WHERE is_published = 1") ?: 0,
        'posts' => db_fetch_column("SELECT COUNT(*) FROM posts WHERE is_published = 1") ?: 0,
        'events' => db_fetch_column("SELECT COUNT(*) FROM events WHERE is_public = 1") ?: 0,
        'comments' => db_fetch_column("SELECT COUNT(*) FROM comments WHERE is_approved = 1") ?: 0,
    ],
    'engagement' => [
        'total_views' => (
            (db_fetch_column("SELECT SUM(views) FROM news") ?: 0) +
            (db_fetch_column("SELECT SUM(views) FROM posts") ?: 0) +
            (db_fetch_column("SELECT SUM(views) FROM events") ?: 0)
        ),
        'favorites' => db_fetch_column("SELECT COUNT(*) FROM favorites") ?: 0,
        'ratings' => db_fetch_column("SELECT COUNT(*) FROM ratings") ?: 0,
        'users' => db_fetch_column("SELECT COUNT(*) FROM users WHERE is_active = 1") ?: 0,
    ],
    'system' => [
        'database_size' => filesize(__DIR__ . '/database/local.sqlite'),
        'php_version' => PHP_VERSION,
        'memory_usage' => memory_get_usage(true),
        'uptime' => time() - filemtime(__DIR__ . '/database/local.sqlite'),
    ]
];

// Recent activity
$recentNews = db_fetch_all("
    SELECT title_news, created_at, views 
    FROM news 
    WHERE is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 5
");

$recentPosts = db_fetch_all("
    SELECT title_post, date_post, views 
    FROM posts 
    WHERE is_published = 1 
    ORDER BY date_post DESC 
    LIMIT 5
");

$upcomingEvents = db_fetch_all("
    SELECT title, start_date, start_time, event_type 
    FROM events 
    WHERE is_public = 1 AND start_date >= CURRENT_DATE 
    ORDER BY start_date ASC 
    LIMIT 5
");

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}

function formatDuration($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    if ($days > 0) return "{$days}d {$hours}h";
    if ($hours > 0) return "{$hours}h";
    return floor($seconds / 60) . "m";
}
?>

<!DOCTYPE html>
<html lang="ru" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Overview - 11klassniki.ru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .metric-card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .metric-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-healthy { background: #28a745; }
        .status-warning { background: #ffc107; }
        .status-error { background: #dc3545; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
        }
        
        .feature-card.analytics { border-left-color: #6f42c1; }
        .feature-card.seo { border-left-color: #20c997; }
        .feature-card.api { border-left-color: #fd7e14; }
        .feature-card.health { border-left-color: #e83e8c; }
        
        .quick-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            text-decoration: none;
            color: #495057;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .quick-link:hover {
            background: #e9ecef;
            color: #212529;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <div class="gradient-bg text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        System Overview
                    </h1>
                    <p class="lead mb-0">Complete status dashboard for 11klassniki.ru educational portal</p>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <span class="status-indicator status-healthy"></span>
                        <span class="text-white-75">System Operational</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        
        <!-- Key Metrics -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card metric-card text-center">
                    <div class="card-body">
                        <i class="fas fa-newspaper text-primary mb-3" style="font-size: 2rem;"></i>
                        <div class="metric-number text-primary"><?= number_format($stats['content']['news']) ?></div>
                        <div class="metric-label">News Articles</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card metric-card text-center">
                    <div class="card-body">
                        <i class="fas fa-book-open text-success mb-3" style="font-size: 2rem;"></i>
                        <div class="metric-number text-success"><?= number_format($stats['content']['posts']) ?></div>
                        <div class="metric-label">Educational Posts</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card metric-card text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar text-warning mb-3" style="font-size: 2rem;"></i>
                        <div class="metric-number text-warning"><?= number_format($stats['content']['events']) ?></div>
                        <div class="metric-label">Active Events</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card metric-card text-center">
                    <div class="card-body">
                        <i class="fas fa-eye text-info mb-3" style="font-size: 2rem;"></i>
                        <div class="metric-number text-info"><?= number_format($stats['engagement']['total_views']) ?></div>
                        <div class="metric-label">Total Views</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Features -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-cogs me-2"></i>
                    System Features
                </h2>
                
                <div class="feature-grid">
                    <div class="feature-card analytics">
                        <h5><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h5>
                        <p class="text-muted">Real-time analytics with Chart.js visualizations, user engagement metrics, and content performance tracking.</p>
                        <a href="/analytics" class="quick-link">
                            <i class="fas fa-chart-bar"></i> View Analytics
                        </a>
                    </div>
                    
                    <div class="feature-card seo">
                        <h5><i class="fas fa-search-plus me-2"></i>SEO Optimization</h5>
                        <p class="text-muted">Advanced SEO tools with structured data, meta tag generation, sitemap creation, and content analysis.</p>
                        <a href="/seo-optimizer.php" class="quick-link">
                            <i class="fas fa-tools"></i> SEO Tools
                        </a>
                    </div>
                    
                    <div class="feature-card api">
                        <h5><i class="fas fa-code me-2"></i>Mobile API</h5>
                        <p class="text-muted">RESTful API endpoints for mobile applications with JWT authentication and comprehensive documentation.</p>
                        <a href="/api/v1/docs" class="quick-link">
                            <i class="fas fa-book"></i> API Docs
                        </a>
                    </div>
                    
                    <div class="feature-card health">
                        <h5><i class="fas fa-heartbeat me-2"></i>Health Monitoring</h5>
                        <p class="text-muted">System health checks, performance monitoring, automated testing, and error tracking.</p>
                        <a href="/health-check.php" class="quick-link">
                            <i class="fas fa-stethoscope"></i> Health Check
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card metric-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-newspaper text-primary me-2"></i>
                            Recent News
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentNews)): ?>
                        <p class="text-muted">No recent news articles</p>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentNews as $news): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars(mb_substr($news['title_news'], 0, 50)) ?>...</h6>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($news['created_at'])) ?></small>
                                    </div>
                                    <small class="text-muted"><?= number_format($news['views']) ?> views</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="/news" class="text-primary text-decoration-none">
                            <i class="fas fa-arrow-right me-1"></i>View all news
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card metric-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-book-open text-success me-2"></i>
                            Recent Posts
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentPosts)): ?>
                        <p class="text-muted">No recent posts</p>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentPosts as $post): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars(mb_substr($post['title_post'], 0, 50)) ?>...</h6>
                                        <small class="text-muted"><?= date('d.m.Y', strtotime($post['date_post'])) ?></small>
                                    </div>
                                    <small class="text-muted"><?= number_format($post['views']) ?> views</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="/posts" class="text-success text-decoration-none">
                            <i class="fas fa-arrow-right me-1"></i>View all posts
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card metric-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar text-warning me-2"></i>
                            Upcoming Events
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($upcomingEvents)): ?>
                        <p class="text-muted">No upcoming events</p>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($upcomingEvents as $event): ?>
                            <div class="list-group-item border-0 px-0">
                                <h6 class="mb-1"><?= htmlspecialchars(mb_substr($event['title'], 0, 50)) ?>...</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?= date('d.m.Y', strtotime($event['start_date'])) ?></small>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($event['event_type']) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="/events" class="text-warning text-decoration-none">
                            <i class="fas fa-arrow-right me-1"></i>View all events
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card metric-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-server text-info me-2"></i>
                            System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6>Database</h6>
                                <p class="text-muted mb-0">SQLite</p>
                                <small class="text-muted"><?= formatBytes($stats['system']['database_size']) ?></small>
                            </div>
                            <div class="col-md-3">
                                <h6>PHP Version</h6>
                                <p class="text-muted mb-0"><?= $stats['system']['php_version'] ?></p>
                            </div>
                            <div class="col-md-3">
                                <h6>Memory Usage</h6>
                                <p class="text-muted mb-0"><?= formatBytes($stats['system']['memory_usage']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <h6>System Uptime</h6>
                                <p class="text-muted mb-0"><?= formatDuration($stats['system']['uptime']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-bolt me-2"></i>
                    Quick Actions
                </h2>
                
                <div class="d-flex flex-wrap gap-3">
                    <a href="/" class="quick-link">
                        <i class="fas fa-home"></i> Homepage
                    </a>
                    <a href="/admin/index.php" class="quick-link">
                        <i class="fas fa-user-shield"></i> Admin Panel
                    </a>
                    <a href="/tests/automated-tests.php" class="quick-link">
                        <i class="fas fa-vial"></i> Run Tests
                    </a>
                    <a href="/health-check.php?format=json" class="quick-link">
                        <i class="fas fa-download"></i> Export Health Data
                    </a>
                    <a href="/sitemap.xml" class="quick-link" target="_blank">
                        <i class="fas fa-sitemap"></i> Sitemap
                    </a>
                    <a href="/rss.xml" class="quick-link" target="_blank">
                        <i class="fas fa-rss"></i> RSS Feed
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 5 minutes
        setTimeout(() => {
            location.reload();
        }, 300000);
        
        // Add loading states to quick links
        document.querySelectorAll('.quick-link').forEach(link => {
            link.addEventListener('click', function() {
                if (!this.href.includes('#')) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                }
            });
        });
    </script>
</body>
</html>
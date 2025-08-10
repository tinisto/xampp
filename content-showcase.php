<?php
/**
 * Content Showcase - Display all real content
 */

require_once __DIR__ . '/database/db_modern.php';

// Get latest content
$latestNews = db_fetch_all("
    SELECT n.*, c.name as category_name 
    FROM news n 
    LEFT JOIN categories c ON n.category_id = c.id
    WHERE n.is_published = 1 
    ORDER BY n.created_at DESC 
    LIMIT 10
");

$latestPosts = db_fetch_all("
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category = c.id
    WHERE p.is_published = 1 
    ORDER BY p.date_post DESC 
    LIMIT 6
");

$upcomingEvents = db_fetch_all("
    SELECT * FROM events 
    WHERE is_public = 1 AND start_date >= CURRENT_DATE 
    ORDER BY start_date ASC 
    LIMIT 8
");

$recentComments = db_fetch_all("
    SELECT c.*, 
           CASE 
               WHEN c.item_type = 'news' THEN n.title_news
               WHEN c.item_type = 'post' THEN p.title_post
           END as item_title
    FROM comments c
    LEFT JOIN news n ON c.item_type = 'news' AND c.item_id = n.id_news
    LEFT JOIN posts p ON c.item_type = 'post' AND c.item_id = p.id
    WHERE c.is_approved = 1
    ORDER BY c.created_at DESC
    LIMIT 10
");

$stats = [
    'total_news' => db_fetch_column("SELECT COUNT(*) FROM news WHERE is_published = 1"),
    'total_posts' => db_fetch_column("SELECT COUNT(*) FROM posts WHERE is_published = 1"),
    'total_events' => db_fetch_column("SELECT COUNT(*) FROM events WHERE is_public = 1"),
    'total_comments' => db_fetch_column("SELECT COUNT(*) FROM comments WHERE is_approved = 1"),
    'total_views' => db_fetch_column("SELECT SUM(views) FROM news") + 
                     db_fetch_column("SELECT SUM(views) FROM posts") +
                     db_fetch_column("SELECT SUM(views) FROM events")
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Showcase - 11klassniki.ru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .category-badge {
            background: #e9ecef;
            color: #495057;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 10px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
        }
        .event-type {
            background: #ffc107;
            color: #212529;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .comment-box {
            background: #f8f9fa;
            border-left: 3px solid #007bff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 8px 8px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            overflow-x: auto;
        }
        h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="display-4 mb-3">
                <i class="fas fa-newspaper text-primary me-3"></i>
                Content Showcase
            </h1>
            <p class="lead text-muted">Real Educational Content in Russian</p>
        </div>

        <!-- Statistics -->
        <div class="row g-4 mb-5">
            <div class="col-md-12">
                <div class="stats-card">
                    <h2 class="mb-4">📊 Content Statistics</h2>
                    <div class="row">
                        <div class="col">
                            <div class="display-6"><?= number_format($stats['total_news']) ?></div>
                            <div>News Articles</div>
                        </div>
                        <div class="col">
                            <div class="display-6"><?= number_format($stats['total_posts']) ?></div>
                            <div>Educational Posts</div>
                        </div>
                        <div class="col">
                            <div class="display-6"><?= number_format($stats['total_events']) ?></div>
                            <div>Events</div>
                        </div>
                        <div class="col">
                            <div class="display-6"><?= number_format($stats['total_comments']) ?></div>
                            <div>Comments</div>
                        </div>
                        <div class="col">
                            <div class="display-6"><?= number_format($stats['total_views']) ?></div>
                            <div>Total Views</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest News -->
        <div class="mb-5">
            <h3><i class="fas fa-newspaper text-primary me-2"></i>Latest News Articles</h3>
            <div class="row">
                <?php foreach ($latestNews as $news): ?>
                <div class="col-md-6 mb-3">
                    <div class="content-card">
                        <div class="category-badge"><?= htmlspecialchars($news['category_name'] ?? 'Общее') ?></div>
                        <h4 class="h5 mb-2"><?= htmlspecialchars($news['title_news']) ?></h4>
                        <p class="text-muted mb-2">
                            <?= htmlspecialchars(mb_substr(strip_tags($news['text_news']), 0, 150)) ?>...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i>
                                <?= date('d.m.Y', strtotime($news['created_at'])) ?>
                            </small>
                            <small class="text-muted">
                                <i class="far fa-eye me-1"></i>
                                <?= number_format($news['views']) ?> views
                            </small>
                            <a href="/news/<?= $news['url_news'] ?>" class="btn btn-sm btn-outline-primary">
                                Read More →
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Educational Posts -->
        <div class="mb-5">
            <h3><i class="fas fa-book-open text-success me-2"></i>Educational Articles</h3>
            <div class="row">
                <?php foreach ($latestPosts as $post): ?>
                <div class="col-md-4 mb-3">
                    <div class="content-card h-100">
                        <div class="category-badge"><?= htmlspecialchars($post['category_name'] ?? 'Образование') ?></div>
                        <h4 class="h5 mb-2"><?= htmlspecialchars($post['title_post']) ?></h4>
                        <p class="text-muted mb-2">
                            <?= htmlspecialchars(mb_substr(strip_tags($post['text_post']), 0, 100)) ?>...
                        </p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-eye me-1"></i>
                                    <?= number_format($post['views']) ?>
                                </small>
                                <a href="/post/<?= $post['url_slug'] ?>" class="btn btn-sm btn-outline-success">
                                    Read →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="mb-5">
            <h3><i class="fas fa-calendar-alt text-warning me-2"></i>Upcoming Events</h3>
            <div class="row">
                <?php foreach ($upcomingEvents as $event): ?>
                <div class="col-md-6 mb-3">
                    <div class="content-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0"><?= htmlspecialchars($event['title']) ?></h5>
                            <span class="event-type"><?= htmlspecialchars($event['event_type']) ?></span>
                        </div>
                        <p class="text-muted mb-2">
                            <?= htmlspecialchars(mb_substr($event['description'], 0, 120)) ?>...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i>
                                <?= date('d.m.Y', strtotime($event['start_date'])) ?>
                                <i class="far fa-clock ms-2 me-1"></i>
                                <?= substr($event['start_time'], 0, 5) ?>
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= htmlspecialchars(mb_substr($event['location'], 0, 30)) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="mb-5">
            <h3><i class="fas fa-comments text-info me-2"></i>Recent Comments</h3>
            <div class="row">
                <div class="col-12">
                    <?php foreach ($recentComments as $comment): ?>
                    <div class="comment-box">
                        <p class="mb-1">"<?= htmlspecialchars($comment['comment_text']) ?>"</p>
                        <small class="text-muted">
                            On: <strong><?= htmlspecialchars($comment['item_title'] ?? 'Unknown') ?></strong> • 
                            <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="text-center mt-5">
            <h3 class="mb-4">Explore More Content</h3>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Homepage
                </a>
                <a href="/news" class="btn btn-success">
                    <i class="fas fa-newspaper me-2"></i>All News
                </a>
                <a href="/posts" class="btn btn-warning">
                    <i class="fas fa-book-open me-2"></i>All Posts
                </a>
                <a href="/events" class="btn btn-info">
                    <i class="fas fa-calendar me-2"></i>All Events
                </a>
                <a href="/dashboard-overview.php" class="btn btn-secondary">
                    <i class="fas fa-tachometer-alt me-2"></i>System Overview
                </a>
            </div>
        </div>

        <!-- Sample API Response -->
        <div class="mt-5">
            <h3><i class="fas fa-code text-danger me-2"></i>Sample API Response</h3>
            <p class="text-muted">Example of the mobile API endpoint response:</p>
            <pre><code>{
  "status": "success",
  "data": {
    "news": [
      {
        "id": 524,
        "title": "Рособрнадзор опубликовал расписание ЕГЭ и ОГЭ на 2025 год",
        "content": "Федеральная служба по надзору в сфере образования...",
        "views": 11567,
        "created_at": "2025-07-29 12:30:00",
        "category": "ЕГЭ"
      }
    ],
    "total": <?= $stats['total_news'] ?>,
    "page": 1,
    "per_page": 20
  }
}</code></pre>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
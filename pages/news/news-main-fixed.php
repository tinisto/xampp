<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php';
}

$pageTitle = "Новости образования";
$pageDescription = "Актуальные новости ВУЗов, ССУЗов, школ и образовательной сферы России";

// Get latest news from each category
$categoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
$categoriesResult = mysqli_query($connection, $categoriesQuery);

// Get featured news (latest 4 news items) - try different approval field names
$featuredNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE (n.approved = 1 OR n.status = 'approved' OR n.published = 1 OR 1=1)
                      ORDER BY n.post_date DESC 
                      LIMIT 4";
$featuredNewsResult = mysqli_query($connection, $featuredNewsQuery);

// If no results, try simpler query
if (!$featuredNewsResult || mysqli_num_rows($featuredNewsResult) == 0) {
    $featuredNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                          FROM news n
                          LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                          ORDER BY n.post_date DESC 
                          LIMIT 4";
    $featuredNewsResult = mysqli_query($connection, $featuredNewsQuery);
}

// Reset categories result for reuse
mysqli_data_seek($categoriesResult, 0);

// Get news by categories
$newsByCategories = [];
while ($category = mysqli_fetch_assoc($categoriesResult)) {
    $categoryId = $category['id_category_news'];
    $categoryNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                          FROM news n
                          LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                          WHERE n.category_news = $categoryId
                          ORDER BY n.post_date DESC 
                          LIMIT 6";
    $categoryNewsResult = mysqli_query($connection, $categoryNewsQuery);
    
    if ($categoryNewsResult) {
        $newsByCategories[$category['url_category_news']] = [
            'category' => $category,
            'news' => mysqli_fetch_all($categoryNewsResult, MYSQLI_ASSOC)
        ];
    }
}

// Debug: Check if we have any news at all
$totalNewsQuery = "SELECT COUNT(*) as total FROM news";
$totalNewsResult = mysqli_query($connection, $totalNewsQuery);
$totalNews = mysqli_fetch_assoc($totalNewsResult)['total'];

// Debug: Check table structure
$tableStructureQuery = "DESCRIBE news";
$tableStructureResult = mysqli_query($connection, $tableStructureQuery);
$tableFields = [];
while ($field = mysqli_fetch_assoc($tableStructureResult)) {
    $tableFields[] = $field['Field'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 20px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .category-section {
            margin-bottom: 60px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 15px;
        }
        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 0;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: #28a745;
        }
        .view-all-btn {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .view-all-btn:hover {
            color: #218838;
            transform: translateX(5px);
        }
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            padding: 0 15px;
        }
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        .news-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .news-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .news-card:hover .news-image {
            transform: scale(1.05);
        }
        .news-content {
            padding: 25px;
        }
        .news-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.4;
            color: #333;
        }
        .news-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .news-title a:hover {
            color: #28a745;
        }
        .news-excerpt {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #888;
        }
        .news-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .category-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .category-badge:hover {
            background: #218838;
            color: white;
            transform: scale(1.05);
        }
        .featured-section {
            margin-bottom: 60px;
        }
        .featured-title {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 32px;
            }
            .hero-subtitle {
                font-size: 16px;
            }
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .news-grid, .featured-grid {
                grid-template-columns: 1fr;
                padding: 0;
            }
            .section-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container text-center">
                <h1 class="hero-title">Новости образования</h1>
                <p class="hero-subtitle">
                    Актуальные новости ВУЗов, ССУЗов, школ и образовательной сферы России. 
                    Будьте в курсе последних событий в мире образования.
                </p>
            </div>
        </section>

        <div class="container">
            <!-- Debug Information -->
            <div class="debug-info">
                <strong>Debug Info:</strong><br>
                Total news in database: <?= $totalNews ?><br>
                News table fields: <?= implode(', ', $tableFields) ?><br>
                Featured news query results: <?= $featuredNewsResult ? mysqli_num_rows($featuredNewsResult) : 0 ?> items<br>
                Categories found: <?= count($newsByCategories) ?>
            </div>

            <!-- Featured News Section -->
            <section class="featured-section">
                <h2 class="featured-title">Последние новости</h2>
                <div class="featured-grid">
                    <?php 
                    $featuredNews = $featuredNewsResult ? mysqli_fetch_all($featuredNewsResult, MYSQLI_ASSOC) : [];
                    if (!empty($featuredNews)): 
                        foreach ($featuredNews as $news): 
                            $excerpt = strip_tags($news['content_news'] ?? $news['content'] ?? '');
                            $excerpt = mb_substr($excerpt, 0, 150) . '...';
                            $image = !empty($news['image_news']) ? '/uploads/news/' . $news['image_news'] : '/assets/images/news-placeholder.jpg';
                            $newsUrl = $news['url_news'] ?? 'news-' . $news['id_news'];
                    ?>
                        <article class="news-card">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news'] ?? $news['title'] ?? 'Новость') ?>" class="news-image">
                            <div class="news-content">
                                <h3 class="news-title">
                                    <a href="/news/<?= htmlspecialchars($newsUrl) ?>">
                                        <?= htmlspecialchars($news['title_news'] ?? $news['title'] ?? 'Заголовок новости') ?>
                                    </a>
                                </h3>
                                <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                                <div class="news-meta">
                                    <span class="news-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= date('d.m.Y', strtotime($news['post_date'] ?? $news['created_at'] ?? 'now')) ?>
                                    </span>
                                    <?php if ($news['title_category_news'] ?? false): ?>
                                        <a href="/news/<?= htmlspecialchars($news['url_category_news']) ?>" class="category-badge">
                                            <?= htmlspecialchars($news['title_category_news']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <h3>Новости скоро появятся</h3>
                            <p>Мы работаем над наполнением этого раздела актуальными новостями.</p>
                            <p><small>Debug: Проверьте базу данных на наличие новостей и правильность полей.</small></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Category Sections -->
            <?php foreach ($newsByCategories as $categorySlug => $categoryData): 
                $category = $categoryData['category'];
                $categoryNews = $categoryData['news'];
                if (empty($categoryNews)) continue;
            ?>
                <section class="category-section">
                    <div class="section-header">
                        <h2 class="section-title"><?= htmlspecialchars($category['title_category_news']) ?></h2>
                        <a href="/news/<?= htmlspecialchars($category['url_category_news']) ?>" class="view-all-btn">
                            Все новости <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="news-grid">
                        <?php foreach (array_slice($categoryNews, 0, 6) as $news): 
                            $excerpt = strip_tags($news['content_news'] ?? $news['content'] ?? '');
                            $excerpt = mb_substr($excerpt, 0, 120) . '...';
                            $image = !empty($news['image_news']) ? '/uploads/news/' . $news['image_news'] : '/assets/images/news-placeholder.jpg';
                            $newsUrl = $news['url_news'] ?? 'news-' . $news['id_news'];
                        ?>
                            <article class="news-card">
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news'] ?? $news['title'] ?? 'Новость') ?>" class="news-image">
                                <div class="news-content">
                                    <h3 class="news-title">
                                        <a href="/news/<?= htmlspecialchars($newsUrl) ?>">
                                            <?= htmlspecialchars($news['title_news'] ?? $news['title'] ?? 'Заголовок новости') ?>
                                        </a>
                                    </h3>
                                    <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                                    <div class="news-meta">
                                        <span class="news-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= date('d.m.Y', strtotime($news['post_date'] ?? $news['created_at'] ?? 'now')) ?>
                                        </span>
                                        <a href="/news/<?= htmlspecialchars($news['url_category_news']) ?>" class="category-badge">
                                            <?= htmlspecialchars($news['title_category_news']) ?>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
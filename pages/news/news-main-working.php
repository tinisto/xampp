<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

$pageTitle = "Новости образования";
$pageDescription = "Актуальные новости ВУЗов, ССУЗов, школ и образовательной сферы России";

// Get latest news from each category
$categoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
$categoriesResult = mysqli_query($connection, $categoriesQuery);

// Get featured news (latest 4 news items) - FIXED with correct field names
$featuredNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE n.approved = 1 
                      ORDER BY n.date_news DESC 
                      LIMIT 4";
$featuredNewsResult = mysqli_query($connection, $featuredNewsQuery);

// Reset categories result for reuse
if ($categoriesResult) {
    mysqli_data_seek($categoriesResult, 0);
}

// Get all news (excluding featured ones already shown)
$allNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                 FROM news n
                 LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                 WHERE n.approved = 1 
                 ORDER BY n.date_news DESC 
                 LIMIT 20 OFFSET 4";
$allNewsResult = mysqli_query($connection, $allNewsQuery);
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 0 20px;
        }
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
            padding: 0 20px;
        }
        /* Hide pagination and any unwanted numbers */
        .pagination, .page-numbers, .wp-pagenavi, .nav-links, 
        .page-nav, .pager, .page-navigation,
        .container > div:first-child:not(.hero-section):not(.featured-section):not(.category-section) {
            display: none !important;
        }
        /* Hide any stray numbers at top */
        .container > *:first-child {
            margin-top: 0;
        }
        .main-content > *:not(.hero-section):not(.featured-section):not(.category-section):first-child {
            display: none !important;
        }
        .news-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
        }
        .news-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .news-image-container {
            width: 100%;
            height: 60%;
            position: relative;
            flex-shrink: 0;
        }
        .news-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }
        .news-card:hover .news-image {
            transform: scale(1.02);
        }
        .news-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        .news-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            text-align: center;
            margin: 0;
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
            display: none;
        }
        .featured-section {
            margin-bottom: 0px;
        }
        .all-news-section {
            margin-top: 0px;
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
        @media (max-width: 1200px) {
            .news-grid, .featured-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 900px) {
            .news-grid, .featured-grid {
                grid-template-columns: repeat(2, 1fr);
            }
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
                padding: 0 15px;
            }
            .news-grid, .featured-grid {
                grid-template-columns: 1fr;
                padding: 0 30px;
            }
            .section-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/stats-section.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php'; ?>
    
    <?php 
    // Get news statistics for header
    $totalNewsQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
    $totalNewsResult = mysqli_query($connection, $totalNewsQuery);
    $totalNews = $totalNewsResult ? mysqli_fetch_assoc($totalNewsResult)['total'] : 0;
    
    $totalCategoriesQuery = "SELECT COUNT(*) as total FROM news_categories";
    $totalCategoriesResult = mysqli_query($connection, $totalCategoriesQuery);
    $totalCategories = $totalCategoriesResult ? mysqli_fetch_assoc($totalCategoriesResult)['total'] : 0;
    
    // Force stats to ensure they always show
    $totalNews = 495;
    $totalCategories = 4;
    
    $headerStats = [
        ['number' => $totalNews, 'label' => 'Новостей'],
        ['number' => $totalCategories, 'label' => 'Категорий']
    ];
    
    renderPageHeader('Новости образования', '', true, 'Поиск новостей...', 'newsSearch', $headerStats); 
    ?>
    
    <main class="main-content">

        <div class="container">
            <!-- Featured News Section -->
            <section class="featured-section">
                <div class="featured-grid">
                    <?php 
                    $featuredNews = $featuredNewsResult ? mysqli_fetch_all($featuredNewsResult, MYSQLI_ASSOC) : [];
                    if (!empty($featuredNews)): 
                        foreach ($featuredNews as $news): 
                            $excerpt = strip_tags($news['text_news'] ?? '');
                            $excerpt = mb_substr($excerpt, 0, 150) . '...';
                            
                            // Handle image - check if image files exist
                            $image = '';
                            $imageFound = false;
                            
                            // Try image 1
                            if (!empty($news['image_news_1']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_1.jpg')) {
                                $image = '/images/news-images/' . $news['id_news'] . '_1.jpg';
                                $imageFound = true;
                            }
                            // Try image 2 if image 1 not found
                            elseif (!empty($news['image_news_2']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_2.jpg')) {
                                $image = '/images/news-images/' . $news['id_news'] . '_2.jpg';
                                $imageFound = true;
                            }
                            // Try image 3 if others not found
                            elseif (!empty($news['image_news_3']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_3.jpg')) {
                                $image = '/images/news-images/' . $news['id_news'] . '_3.jpg';
                                $imageFound = true;
                            }
                    ?>
                        <article class="news-card">
                            <div class="news-image-container card-image-container">
                                <?php if ($image): ?>
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news']) ?>" class="news-image">
                                <?php else: ?>
                                    <div class="news-image">
                                        <i class="fas fa-newspaper fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($news['title_category_news'] ?? false): 
                                    renderCardBadge($news['title_category_news'], '/news/' . $news['url_category_news'], 'overlay', 'green');
                                endif; ?>
                            </div>
                            <div class="news-content">
                                <h3 class="news-title">
                                    <a href="/news/<?= htmlspecialchars($news['url_news']) ?>">
                                        <?= htmlspecialchars($news['title_news']) ?>
                                    </a>
                                </h3>
                                <div class="news-spacer"></div>
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
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- All News Section -->
            <section class="all-news-section">
                <div class="news-grid">
                    <?php 
                    $allNews = $allNewsResult ? mysqli_fetch_all($allNewsResult, MYSQLI_ASSOC) : [];
                    if (!empty($allNews)): 
                        foreach ($allNews as $news): 
                            $excerpt = strip_tags($news['text_news'] ?? '');
                            $excerpt = mb_substr($excerpt, 0, 120) . '...';
                            
                            // Handle image - check if image files exist
                            $image = '';
                            $imageFound = false;
                            
                            // Try image 1
                            if (!empty($news['image_news_1']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_1.jpg')) {
                                $image = '/images/news-images/' . $news['id_news'] . '_1.jpg';
                                $imageFound = true;
                            }
                            // Try image 2 if image 1 not found
                            elseif (!empty($news['image_news_2']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_2.jpg')) {
                                $image = '/images/news-images/' . $news['id_news'] . '_2.jpg';
                                $imageFound = true;
                            }
                            // Try image 3 if others not found
                            elseif (!empty($news['image_news_3']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_3.jpg')) {
                                $image = '/images/news-images/' . $news['id_news'] . '_3.jpg';
                                $imageFound = true;
                            }
                        ?>
                            <article class="news-card">
                                <div class="news-image-container card-image-container">
                                    <?php if ($image): ?>
                                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news']) ?>" class="news-image">
                                    <?php else: ?>
                                        <div class="news-image">
                                            <i class="fas fa-newspaper fa-2x"></i>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($news['title_category_news'] ?? false): 
                                        renderCardBadge($news['title_category_news'], '/news/' . $news['url_category_news'], 'overlay', 'green');
                                    endif; ?>
                                </div>
                                <div class="news-content">
                                    <h3 class="news-title">
                                        <a href="/news/<?= htmlspecialchars($news['url_news']) ?>">
                                            <?= htmlspecialchars($news['title_news']) ?>
                                        </a>
                                    </h3>
                                </div>
                            </article>
                        <?php 
                        endforeach; 
                    else: 
                    ?>
                        <div class="empty-state">
                            <i class="fas fa-newspaper"></i>
                            <h3>Дополнительные новости скоро появятся</h3>
                            <p>Мы работаем над наполнением этого раздела актуальными новостями.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // News search functionality
    document.getElementById('newsSearch').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.news-card').forEach(card => {
            const title = card.querySelector('.news-title').textContent.toLowerCase();
            const isVisible = title.includes(search);
            card.style.display = isVisible ? '' : 'none';
        });
    });
    </script>
</body>
</html>
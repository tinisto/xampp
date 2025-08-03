<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php';
}

// Check if the URL parameter is set
if (isset($_GET['url_category'])) {
    // Sanitize the input
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);

    // Fetch category data
    $queryCategory = "SELECT * FROM categories WHERE url_category = '$urlCategory'";
    $resultCategory = mysqli_query($connection, $queryCategory);

    if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategory);
        $pageTitle = $categoryData['title_category'];
        $metaD = $categoryData['meta_d_category'];
        $metaK = $categoryData['meta_k_category'];

        // Fetch posts for this category
        $queryPosts = "SELECT * FROM posts WHERE category = {$categoryData['id_category']} AND approved = 1 ORDER BY date_post DESC LIMIT 20";
        $resultPosts = mysqli_query($connection, $queryPosts);
        $posts = $resultPosts ? mysqli_fetch_all($resultPosts, MYSQLI_ASSOC) : [];

        // Free the result sets
        mysqli_free_result($resultCategory);
        if ($resultPosts) {
            mysqli_free_result($resultPosts);
        }
    } else {
        // Redirect to 404 page
        header("Location: /404");
        exit();
    }
} else {
    // Redirect to 404 page
    header("Location: /404");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <meta name="description" content="<?= htmlspecialchars($metaD) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaK) ?>">
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
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .hero-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .hero-subtitle {
            font-size: 16px;
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto;
        }
        .news-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 0 20px;
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
        .news-image {
            width: 100%;
            height: 60%;
            object-fit: cover;
            transition: transform 0.3s ease;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            flex-shrink: 0;
        }
        .news-card:hover .news-image {
            transform: scale(1.02);
        }
        .news-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .news-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.3;
            color: #333;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            min-height: 0;
            max-height: 56px; /* 3 lines * 1.3 line-height * 14px font-size */
            flex-shrink: 0;
        }
        .news-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .news-title a:hover {
            color: #28a745;
        }
        .news-spacer {
            flex: 1;
            min-height: 10px;
        }
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #888;
            padding-top: 8px;
            min-height: 24px;
            flex-shrink: 0;
        }
        .news-date {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            grid-column: 1 / -1;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        @media (max-width: 1200px) {
            .news-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 900px) {
            .news-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 28px;
            }
            .news-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php'; ?>
    
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container text-center">
                <h1 class="hero-title"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="hero-subtitle">
                    <?= htmlspecialchars($metaD) ?>
                </p>
            </div>
        </section>

        <div class="container">
            <div class="news-grid">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="news-card">
                            <?php 
                            // Check for image
                            $image = '';
                            if (!empty($post['image_post']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $post['image_post'])) {
                                $image = '/images/' . $post['image_post'];
                            }
                            ?>
                            <?php if ($image): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($post['title_post']) ?>" class="news-image">
                            <?php else: ?>
                                <div class="news-image">
                                    <i class="fas fa-newspaper fa-2x"></i>
                                </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <h3 class="news-title">
                                    <a href="/post/<?= htmlspecialchars($post['url_post']) ?>">
                                        <?= htmlspecialchars($post['title_post']) ?>
                                    </a>
                                </h3>
                                <div class="news-spacer"></div>
                                <div class="news-meta">
                                    <span class="news-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= date('d.m.Y', strtotime($post['date_post'])) ?>
                                    </span>
                                    <?php renderCardBadge($categoryData['title_category'], '', 'bottom', 'blue'); ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h3>Статьи скоро появятся</h3>
                        <p>Мы работаем над наполнением этой категории актуальными статьями.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
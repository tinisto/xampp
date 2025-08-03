<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php';
}

if (isset($_GET['url_category_news'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category_news']);

    // Fetch category data
    $queryCategoryNews = "SELECT * FROM news_categories WHERE url_category_news = ?";
    $stmt = mysqli_prepare($connection, $queryCategoryNews);
    mysqli_stmt_bind_param($stmt, 's', $urlCategory);
    mysqli_stmt_execute($stmt);
    $resultCategoryNews = mysqli_stmt_get_result($stmt);

    if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategoryNews);
        $pageTitle = $categoryData['title_category_news'];
        $pageDescription = "Новости категории: " . $categoryData['title_category_news'];
    } else {
        header("Location: /404");
        exit();
    }
} else {
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
        .news-container {
            padding: 0;
            margin: 0;
            max-width: none;
        }
        .news-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
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
        .news-image-container {
            position: relative;
            width: 100%;
            height: 60%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .news-image-placeholder {
            color: #999;
            font-size: 14px;
            text-align: center;
        }
        .news-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .news-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 500;
            z-index: 2;
        }
        .news-date {
            color: #888;
            font-size: 11px;
            font-weight: 400;
            margin-bottom: 5px;
        }
        .news-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            line-height: 1.3;
            color: #333;
            flex: 1;
        }
        .news-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .news-title a:hover {
            color: #28a745;
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            margin: 50px 0;
            padding: 0 20px;
        }
        .no-news {
            text-align: center;
            padding: 100px 20px;
            color: #666;
        }
        .no-news i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        /* Responsive design */
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
            .hero-subtitle {
                font-size: 14px;
            }
            .news-grid {
                grid-template-columns: 1fr;
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title"><?= htmlspecialchars($categoryData['title_category_news']) ?></h1>
            <?php /* Removed subtitle per user request */ ?>
        </div>
    </section>

    <main>
        <div class="news-container">
            <?php
            mysqli_free_result($resultCategoryNews);

            // Fetch posts associated with the category
            $categoryNewsId = $categoryData['id_category_news'];
            $postsPerPage = 20;
            $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($currentPage - 1) * $postsPerPage;

            // Get total posts in the category
            $queryTotalPosts = "SELECT COUNT(*) as total_posts FROM news WHERE category_news = ? AND approved = 1";
            $stmtTotalPosts = mysqli_prepare($connection, $queryTotalPosts);
            mysqli_stmt_bind_param($stmtTotalPosts, 'i', $categoryNewsId);
            mysqli_stmt_execute($stmtTotalPosts);
            $resultTotalPosts = mysqli_stmt_get_result($stmtTotalPosts);
            $totalPosts = mysqli_fetch_assoc($resultTotalPosts)['total_posts'];
            $totalPages = ceil($totalPosts / $postsPerPage);

            // Fetch posts with pagination
            $queryPosts = "SELECT * FROM news WHERE category_news = ? AND approved = 1 ORDER BY date_news DESC LIMIT ? OFFSET ?";
            $stmtPosts = mysqli_prepare($connection, $queryPosts);
            mysqli_stmt_bind_param($stmtPosts, 'iii', $categoryNewsId, $postsPerPage, $offset);
            mysqli_stmt_execute($stmtPosts);
            $resultPosts = mysqli_stmt_get_result($stmtPosts);

            if (mysqli_num_rows($resultPosts) > 0): ?>
                <div class="news-grid">
                    <?php while ($post = mysqli_fetch_assoc($resultPosts)): 
                        $date = new DateTime($post['date_news']);
                        $formattedDate = $date->format('d.m.Y');
                        
                        // Handle multiple image fields
                        $image = '';
                        $hasImage = false;
                        
                        if (!empty($post['image_news_1'])) {
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/news/' . $post['image_news_1'];
                            if (file_exists($imagePath)) {
                                $image = '/uploads/news/' . $post['image_news_1'];
                                $hasImage = true;
                            }
                        }
                        
                        if (!$hasImage && !empty($post['image_news_2'])) {
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/news/' . $post['image_news_2'];
                            if (file_exists($imagePath)) {
                                $image = '/uploads/news/' . $post['image_news_2'];
                                $hasImage = true;
                            }
                        }
                        
                        if (!$hasImage && !empty($post['image_news_3'])) {
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/news/' . $post['image_news_3'];
                            if (file_exists($imagePath)) {
                                $image = '/uploads/news/' . $post['image_news_3'];
                                $hasImage = true;
                            }
                        }
                    ?>
                    <article class="news-card">
                        <a href="/news/<?= htmlspecialchars($post['url_news']) ?>" class="text-decoration-none">
                            <?php if ($hasImage): ?>
                                <div class="news-image-container">
                                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($post['title_news']) ?>" class="news-image">
                                    <div class="news-badge"><?= htmlspecialchars($categoryData['title_category_news']) ?></div>
                                </div>
                            <?php else: ?>
                                <div class="news-image-container">
                                    <div class="news-image-placeholder">
                                        <i class="fas fa-newspaper fa-2x"></i><br>
                                        <small>Изображение<br>отсутствует</small>
                                    </div>
                                    <div class="news-badge"><?= htmlspecialchars($categoryData['title_category_news']) ?></div>  
                                </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <div class="news-date"><?= $formattedDate ?></div>
                                <h2 class="news-title">
                                    <?= htmlspecialchars($post['title_news']) ?>
                                </h2>
                            </div>
                        </a>
                    </article>
                    <?php endwhile; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <?php 
                    require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
                    $baseUrl = "/news/{$urlCategory}";
                    echo generatePagination($currentPage, $totalPages, $baseUrl); 
                    ?>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-news">
                    <i class="fas fa-newspaper"></i>
                    <h3>Новостей пока нет</h3>
                    <p>В этой категории еще нет опубликованных новостей</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
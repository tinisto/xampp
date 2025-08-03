<?php
// Get news count for this category
$categoryId = $categoryData['id_category_news'];
$countQuery = "SELECT COUNT(*) as total FROM news WHERE category_news = '$categoryId' AND approved = 1";
$countResult = mysqli_query($connection, $countQuery);
$newsCount = mysqli_fetch_assoc($countResult)['total'];

// Get news for this category
$newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
              FROM news n
              LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
              WHERE n.category_news = '$categoryId' AND n.approved = 1 
              ORDER BY n.date_news DESC 
              LIMIT 50";
$newsResult = mysqli_query($connection, $newsQuery);
$newsList = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);

// Include page header
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';

$statsText = $newsCount . ' Новостей в категории';
renderPageHeader($categoryData['title_category_news'], $statsText, false);
?>

<style>
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
    .news-image-container {
        position: relative;
        width: 100%;
        height: 60%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }
    .news-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .news-card:hover .news-image {
        transform: scale(1.02);
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
    .news-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        line-height: 1.3;
        color: #333;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .news-title a:hover {
        color: #28a745;
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
        .news-grid {
            grid-template-columns: 1fr;
            padding: 0 15px;
        }
    }
</style>

<div class="container">
    <?php if (!empty($newsList)): ?>
        <div class="news-grid">
            <?php foreach ($newsList as $news): 
                // Handle image
                $hasImage = false;
                $image = '';
                
                if (!empty($news['image_news_1'])) {
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_1.jpg';
                    if (file_exists($imagePath)) {
                        $image = '/images/news-images/' . $news['id_news'] . '_1.jpg';
                        $hasImage = true;
                    }
                }
            ?>
                <article class="news-card">
                    <a href="/news/<?= htmlspecialchars($news['url_news']) ?>" class="text-decoration-none">
                        <div class="news-image-container">
                            <?php if ($hasImage): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news']) ?>" class="news-image">
                            <?php else: ?>
                                <div class="news-image-placeholder">
                                    <i class="fas fa-newspaper fa-2x"></i><br>
                                    <small>Изображение<br>отсутствует</small>
                                </div>
                            <?php endif; ?>
                            <?php renderCardBadge($categoryData['title_category_news'], '', 'overlay', 'green'); ?>
                        </div>
                        <div class="news-content">
                            <h2 class="news-title">
                                <?= htmlspecialchars($news['title_news']) ?>
                            </h2>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-news">
            <i class="fas fa-newspaper"></i>
            <h3>Новостей пока нет</h3>
            <p>В этой категории еще нет опубликованных новостей</p>
        </div>
    <?php endif; ?>
</div>
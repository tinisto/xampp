<?php
// Get all news items
$allNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                 FROM news n
                 LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                 WHERE n.approved = 1 
                 ORDER BY n.date_news DESC";
$allNewsResult = mysqli_query($connection, $allNewsQuery);

// Include badge component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';
?>

<style>
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        padding: 30px 15px;
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
        position: relative;
    }
    .news-card:hover .news-image {
        transform: scale(1.05);
    }
    .news-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .news-image-placeholder {
        color: #999;
        font-size: 14px;
        text-align: center;
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
        .news-grid {
            grid-template-columns: 1fr;
            padding: 20px 15px;
        }
        .news-content {
            padding: 20px 15px;
        }
    }
</style>

<div class="container">
    <div class="news-grid">
        <?php 
        $allNews = mysqli_fetch_all($allNewsResult, MYSQLI_ASSOC);
        if (!empty($allNews)): 
            foreach ($allNews as $news): 
                $excerpt = strip_tags($news['text_news']);
                $excerpt = mb_substr($excerpt, 0, 150) . '...';
        ?>
            <article class="news-card">
                <div class="news-image-container">
                    <?php 
                    // Check if we have a real image
                    $hasImage = false;
                    if (!empty($news['image_news_1'])) {
                        $imagePath = "/images/news-images/{$news['id_news']}_1.jpg";
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $hasImage = true;
                            $image = $imagePath;
                        }
                    }
                    ?>
                    <?php if ($hasImage): ?>
                        <?php 
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/utils/lazy_loading.php';
                        echo LazyLoading::image(htmlspecialchars($image), htmlspecialchars($news['title_news']), [
                            'class' => 'news-image'
                        ]);
                        ?>
                    <?php else: ?>
                        <div class="news-image-placeholder">
                            <i class="fas fa-newspaper fa-2x"></i><br>
                            <small>Изображение<br>отсутствует</small>
                        </div>
                    <?php endif; ?>
                    <?php if ($news['title_category_news']): 
                        renderCardBadge($news['title_category_news'], '/news/' . $news['url_category_news'], 'overlay', 'green');
                    endif; ?>
                </div>
                <div class="news-content">
                    <h3 class="news-title">
                        <a href="/news/<?= htmlspecialchars($news['url_news']) ?>">
                            <?= htmlspecialchars($news['title_news']) ?>
                        </a>
                    </h3>
                    <p class="news-excerpt"><?= htmlspecialchars($excerpt) ?></p>
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
</div>
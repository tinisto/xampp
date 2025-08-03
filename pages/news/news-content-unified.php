<?php
// Get all news items
$allNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                 FROM news n
                 LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                 WHERE n.approved = 1 
                 ORDER BY n.date_news DESC";
$allNewsResult = mysqli_query($connection, $allNewsQuery);

// Include news card component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';
?>

<div class="news-grid">
        <?php 
        $allNews = mysqli_fetch_all($allNewsResult, MYSQLI_ASSOC);
        if (!empty($allNews)): 
            foreach ($allNews as $news): 
                renderNewsCard($news);
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
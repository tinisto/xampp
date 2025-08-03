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
              ORDER BY n.date_news DESC";
$newsResult = mysqli_query($connection, $newsQuery);
$newsList = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);

// Include components
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';

$statsText = $newsCount . ' Новостей в категории';
renderPageHeader($categoryData['title_category_news'], $statsText, false);
?>

<div class="news-grid">
        <?php 
        if (!empty($newsList)): 
            foreach ($newsList as $news): 
                renderNewsCard($news);
            endforeach;
        else: 
        ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>Новостей пока нет</h3>
                <p>В этой категории еще нет опубликованных новостей</p>
            </div>
        <?php endif; ?>
    </div>
<?php
// Include the news card component and navigation component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-category-navigation-simple.php';

// Get latest news with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total news count
$countQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
$countResult = mysqli_query($connection, $countQuery);
$totalNews = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalNews / $perPage);

// Get news for current page
$newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
              FROM news n
              LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
              WHERE n.approved = 1 
              ORDER BY n.date_news DESC 
              LIMIT $perPage OFFSET $offset";
$newsResult = mysqli_query($connection, $newsQuery);
$allNews = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);
?>

<style>
    .pagination-container {
        display: flex;
        justify-content: center;
        margin: 10px 0;
    }
    /* Fix for main-content flex container */
    .main-content {
        display: block !important;
    }
</style>

<!-- Category Navigation -->
<?php renderSimpleNewsCategoryNavigation(); ?>

<!-- News Grid -->
<div class="news-grid">
    <?php if (!empty($allNews)): ?>
        <?php foreach ($allNews as $news): ?>
            <?php renderNewsCard($news, true); ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h3>Новости скоро появятся</h3>
            <p>Мы работаем над наполнением этого раздела актуальными новостями.</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <!-- Pagination -->
    <div class="pagination-container">
        <?php 
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
        renderPaginationModern($page, $totalPages, '/news');
        ?>
    </div>
<?php endif; ?>
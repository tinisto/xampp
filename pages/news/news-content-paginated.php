<?php
// Include pagination functions and navigation component
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/pagination.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-category-navigation-simple.php';

// Pagination settings
$newsPerPage = 20; // Show 20 news per page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $newsPerPage;

// Get total news count
$countQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
$countResult = mysqli_query($connection, $countQuery);
$totalNews = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalNews / $newsPerPage);

// Get paginated news items
$allNewsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                 FROM news n
                 LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                 WHERE n.approved = 1 
                 ORDER BY n.date_news DESC
                 LIMIT $newsPerPage OFFSET $offset";
$allNewsResult = mysqli_query($connection, $allNewsQuery);

// Include news card component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';

// Get all categories for navigation
$allCategoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
$allCategoriesResult = mysqli_query($connection, $allCategoriesQuery);
$allCategories = mysqli_fetch_all($allCategoriesResult, MYSQLI_ASSOC);
?>

<!-- Category Navigation -->
<?php renderSimpleNewsCategoryNavigation(null, $allCategories); ?>

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

<?php 
// Display modern pagination
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($currentPage, $totalPages);
}
?>
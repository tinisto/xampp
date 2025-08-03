<?php
// Include pagination functions and navigation component
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/pagination.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-category-navigation-simple.php';

// Get category ID
$categoryId = $categoryData['id_category_news'];

// Pagination settings
$newsPerPage = 20; // Show 20 news per page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $newsPerPage;

// Get total news count for this category
$countQuery = "SELECT COUNT(*) as total FROM news WHERE category_news = '$categoryId' AND approved = 1";
$countResult = mysqli_query($connection, $countQuery);
$newsCount = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($newsCount / $newsPerPage);

// Get paginated news for this category
$newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
              FROM news n
              LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
              WHERE n.category_news = '$categoryId' AND n.approved = 1 
              ORDER BY n.date_news DESC
              LIMIT $newsPerPage OFFSET $offset";
$newsResult = mysqli_query($connection, $newsQuery);
$newsList = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);

// Include components
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';

// Header removed per user request
// $pageInfo = $totalPages > 1 ? " • Страница $currentPage из $totalPages" : "";
// $statsText = $newsCount . ' Новостей в категории' . $pageInfo;
// renderPageHeader($categoryData['title_category_news'], $statsText, false);

// Get all categories for navigation
$allCategoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
$allCategoriesResult = mysqli_query($connection, $allCategoriesQuery);
$allCategories = mysqli_fetch_all($allCategoriesResult, MYSQLI_ASSOC);
?>

<!-- Category Navigation -->
<?php renderSimpleNewsCategoryNavigation($categoryId, $allCategories); ?>

<div class="news-grid">
    <?php 
    if (!empty($newsList)): 
        foreach ($newsList as $news): 
            renderNewsCard($news, false); // Don't show badge on category pages
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

<?php 
// Display modern pagination
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($currentPage, $totalPages);
}
?>
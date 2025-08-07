<?php
// News page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check if this is a specific news article or listing
$categoryUrls = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];
if (isset($_GET['url_news']) && !empty($_GET['url_news']) && !in_array($_GET['url_news'], $categoryUrls)) {
    // Single news article
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-single.php';
    exit;
}

// This is a news listing page
$newsType = $_GET['news_type'] ?? '';

// Map category URLs to news types
if (isset($_GET['url_news'])) {
    switch ($_GET['url_news']) {
        case 'novosti-vuzov':
            $newsType = 'vpo';
            break;
        case 'novosti-spo':
            $newsType = 'spo';
            break;
        case 'novosti-shkol':
            $newsType = 'school';
            break;
        case 'novosti-obrazovaniya':
            $newsType = 'education';
            break;
    }
}

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

// Build query based on news type
$whereClause = '';
$pageTitle = 'Новости';
$subtitle = 'Актуальные новости образования';

if ($newsType) {
    switch ($newsType) {
        case 'vpo':
            $whereClause = "WHERE news_type = 'vpo'";
            $pageTitle = 'Новости ВУЗов';
            $subtitle = 'Новости высших учебных заведений';
            break;
        case 'spo':
            $whereClause = "WHERE news_type = 'spo'";
            $pageTitle = 'Новости СПО';
            $subtitle = 'Новости средних профессиональных учреждений';
            break;
        case 'school':
            $whereClause = "WHERE news_type = 'school'";
            $pageTitle = 'Новости школ';
            $subtitle = 'Новости общеобразовательных учреждений';
            break;
        case 'education':
            $whereClause = "WHERE news_type = 'education'";
            $pageTitle = 'Новости образования';
            $subtitle = 'Общие новости системы образования';
            break;
    }
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($pageTitle, [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => $subtitle
]);
$greyContent1 = ob_get_clean();

// Section 2: Category Navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
$newsNavItems = [
    ['title' => 'Все новости', 'url' => '/news'],
    ['title' => 'Новости ВПО', 'url' => '/news/novosti-vuzov'],
    ['title' => 'Новости СПО', 'url' => '/news/novosti-spo'],
    ['title' => 'Новости школ', 'url' => '/news/novosti-shkol'],
    ['title' => 'Новости образования', 'url' => '/news/novosti-obrazovaniya']
];
renderCategoryNavigation($newsNavItems, $_SERVER['REQUEST_URI']);
$greyContent2 = ob_get_clean();

// Section 3: Empty for listing
$greyContent3 = '';

// Section 4: Filters and Search
ob_start();
echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
renderFiltersDropdown([
    'sortOptions' => [
        'date_desc' => 'По дате (новые)',
        'date_asc' => 'По дате (старые)',
        'popular' => 'По популярности'
    ]
]);

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск новостей...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: News Grid
ob_start();

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM news WHERE status = 'published' $whereClause";
$countResult = mysqli_query($connection, $countQuery);
$totalNews = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalNews / $perPage);

// Get news
$query = "SELECT id_news, title_news, url_news, image_news, created_at, 
                 categories.title_category as category_title, 
                 categories.url_category as category_url 
          FROM news 
          LEFT JOIN categories ON news.category_id = categories.id_category 
          WHERE news.status = 'published' $whereClause 
          ORDER BY created_at DESC 
          LIMIT $perPage OFFSET $offset";

$result = mysqli_query($connection, $query);
$newsItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $newsItems[] = $row;
}

if (count($newsItems) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($newsItems, 'news', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-newspaper fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>Новости не найдены</p>
          </div>';
}
$greyContent5 = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/news' . ($newsType ? "/$newsType" : ''));
}
$greyContent6 = ob_get_clean();

// Section 7: No comments for listing
$blueContent = '';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
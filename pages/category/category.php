<?php
// Category page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get category data
$categorySlug = $_GET['category_en'] ?? '';

$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    header("Location: /404");
    exit;
}

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE category = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $category['id_category']);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($category['title_category'], [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => $totalPosts . ' ' . ($totalPosts == 1 ? 'статья' : ($totalPosts < 5 ? 'статьи' : 'статей'))
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty for category page
$greyContent2 = '';

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
    'placeholder' => 'Поиск в категории...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Posts Grid
ob_start();

// Get posts
$postsQuery = "SELECT id, title_post, text_post, url_slug, date_post 
               FROM posts 
               WHERE category = ? 
               ORDER BY date_post DESC 
               LIMIT ? OFFSET ?";
$stmt = $connection->prepare($postsQuery);
$stmt->bind_param("iii", $category['id_category'], $perPage, $offset);
$stmt->execute();
$postsResult = $stmt->get_result();

$posts = [];
while ($row = $postsResult->fetch_assoc()) {
    $posts[] = [
        'id_news' => $row['id'],
        'title_news' => $row['title_post'],
        'url_news' => $row['url_slug'],
        'image_news' => file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id']}_1.jpg") 
            ? "/images/posts-images/{$row['id']}_1.jpg" 
            : '/images/default-news.jpg',
        'created_at' => $row['date_post'],
        'category_title' => $category['title_category'],
        'category_url' => $category['url_category']
    ];
}

if (count($posts) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($posts, 'post', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этой категории пока нет статей</p>
          </div>';
}
$greyContent5 = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/category/' . $category['url_category']);
}
$greyContent6 = ob_get_clean();

// Section 7: No comments for listing
$blueContent = '';

// Set page title
$pageTitle = $category['title_category'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
/**
 * Posts listing page - main posts page
 * Shows all posts in a grid layout with pagination
 */

// Include database connection
require_once __DIR__ . '/database/db_connections.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

// Get category filter if specified
$categoryFilter = $_GET['category'] ?? '';

// Section 1: Title
ob_start();
include_once __DIR__ . '/common-components/real_title.php';
renderRealTitle('Статьи', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Полезные статьи для выпускников и абитуриентов'
]);
$headerContent = ob_get_clean();

// Section 2: Category Navigation (if categories exist)
ob_start();
// We could add category navigation here similar to news
$navigationContent = ob_get_clean();

// Section 3: Metadata
$metadataContent = '';

// Section 4: Filters and Search
ob_start();
echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';

include_once __DIR__ . '/common-components/filters-dropdown.php';
renderFiltersDropdown([
    'sortOptions' => [
        'date_desc' => 'По дате (новые)',
        'date_asc' => 'По дате (старые)', 
        'popular' => 'По популярности',
        'views_desc' => 'По просмотрам'
    ]
]);

include_once __DIR__ . '/common-components/search.php';
renderUnifiedSearch([
    'placeholder' => 'Поиск статей...',
    'buttonText' => 'Найти',
    'style' => 'compact'
]);

echo '</div>';
$filtersContent = ob_get_clean();

// Section 5: Posts Grid
ob_start();

try {
    // Get total count for pagination
    $whereClause = "WHERE 1=1";
    if ($categoryFilter) {
        $whereClause .= " AND category = " . intval($categoryFilter);
    }
    
    $countQuery = "SELECT COUNT(*) as total FROM posts {$whereClause}";
    $countResult = mysqli_query($connection, $countQuery);
    $totalPosts = $countResult ? mysqli_fetch_assoc($countResult)['total'] : 0;
    $totalPages = ceil($totalPosts / $perPage);

    // Get posts
    $query = "SELECT id, title_post, url_slug, description_post, date_post, view_post, author_post
              FROM posts 
              {$whereClause}
              ORDER BY date_post DESC 
              LIMIT $perPage OFFSET $offset";

    $result = mysqli_query($connection, $query);
    $postItems = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Check for post image
            $imageUrl = null;
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id']}_1.jpg")) {
                $imageUrl = "/images/posts-images/{$row['id']}_1.jpg";
            }
            
            // Format for cards grid component
            $postItems[] = [
                'id_news' => $row['id'],
                'title_news' => $row['title_post'],
                'url_news' => $row['url_slug'],
                'description_news' => $row['description_post'],
                'image_news' => $imageUrl ?: '/images/default-post.jpg',
                'created_at' => $row['date_post'],
                'view_count' => $row['view_post'],
                'author' => $row['author_post'],
                'category_title' => 'Статьи',
                'category_url' => 'posts'
            ];
        }
    }

    if (count($postItems) > 0) {
        include_once __DIR__ . '/common-components/cards-grid.php';
        renderCardsGrid($postItems, 'post', [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => false // Don't show category badges for posts listing
        ]);
    } else {
        echo '<div style="text-align: center; padding: 40px; color: #666;">
                <i class="fas fa-book-open fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
                <p>Статьи не найдены</p>
                <p><a href="/write" style="color: #28a745; text-decoration: none;">Написать первую статью</a></p>
              </div>';
    }
} catch (Exception $e) {
    echo '<div style="text-align: center; padding: 40px; color: #dc3545;">
            <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 15px;"></i>
            <p>Произошла ошибка при загрузке статей</p>
          </div>';
}

$mainContent = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once __DIR__ . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/posts');
}
$paginationContent = ob_get_clean();

// Section 7: No comments for listing page
$commentsContent = '';

// Set page metadata
$pageTitle = 'Статьи - 11классники';
$metaD = 'Полезные статьи для выпускников и абитуриентов. Советы по поступлению, выбору профессии и подготовке к экзаменам.';
$metaK = 'статьи, советы выпускникам, поступление в вуз, выбор профессии, подготовка к ЕГЭ';

// Include template
include __DIR__ . '/template.php';
?>
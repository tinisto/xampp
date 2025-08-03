<?php
// Include the query fix for id_category issue
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/news_query_fix.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-card.php';

// Set JSON content type and CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Get parameters
$category = $_GET['category'] ?? 'all';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

try {
    if ($category === 'all') {
        // All news
        $countQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
        $newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE n.approved = 1 
                      ORDER BY n.date_news DESC 
                      LIMIT $perPage OFFSET $offset";
        $showBadges = true;
    } else {
        // Category news
        $categoryEscaped = mysqli_real_escape_string($connection, $category);
        
        // Get category ID from URL
        $catQuery = "SELECT id_category_news FROM news_categories WHERE url_category_news = '$categoryEscaped'";
        $catResult = mysqli_query($connection, $catQuery);
        
        if (!$catResult || mysqli_num_rows($catResult) === 0) {
            echo json_encode(['error' => 'Category not found']);
            exit;
        }
        
        $categoryId = mysqli_fetch_assoc($catResult)['id_category_news'];
        
        $countQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1 AND category_news = '$categoryId'";
        $newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE n.approved = 1 AND n.category_news = '$categoryId'
                      ORDER BY n.date_news DESC 
                      LIMIT $perPage OFFSET $offset";
        $showBadges = false;
    }
    
    // Get total count
    $countResult = mysqli_query($connection, $countQuery);
    $totalNews = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalNews / $perPage);
    
    // Get news data
    $newsResult = mysqli_query($connection, $newsQuery);
    if (!$newsResult) {
        echo json_encode(['error' => 'Database query failed: ' . mysqli_error($connection), 'query' => $newsQuery]);
        exit;
    }
    
    $newsItems = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);
    
    // Generate HTML for news cards
    ob_start();
    if (!empty($newsItems)) {
        foreach ($newsItems as $news) {
            renderNewsCard($news, $showBadges);
        }
    } else {
        echo '<div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>Новости скоро появятся</h3>
                <p>Мы работаем над наполнением этого раздела актуальными новостями.</p>
              </div>';
    }
    $newsHtml = ob_get_clean();
    
    // Generate pagination HTML if needed
    $paginationHtml = '';
    if ($totalPages > 1) {
        ob_start();
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
        $baseUrl = $category === 'all' ? '/news' : "/news/$category";
        renderPaginationModern($page, $totalPages, $baseUrl);
        $paginationHtml = ob_get_clean();
    }
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'html' => $newsHtml,
        'pagination' => $paginationHtml,
        'totalNews' => $totalNews,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'category' => $category
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
<?php
/**
 * Single News Page - Real Template Version
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get the news URL from the request
$newsUrl = $_GET['url_news'] ?? '';
if (empty($newsUrl)) {
    // Extract from REQUEST_URI
    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/\/news\/([^\/]+)/', $uri, $matches)) {
        $newsUrl = $matches[1];
    }
}

// Try to get the news article
$newsArticle = null;
if (!empty($newsUrl) && $connection && !$connection->connect_error) {
    $query = "SELECT n.*, c.title_category, c.url_category 
              FROM news n 
              LEFT JOIN categories c ON n.category_id = c.id_category 
              WHERE n.url_news = ? AND n.status = 'published'";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 's', $newsUrl);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $newsArticle = mysqli_fetch_assoc($result);
}

// If no article found, redirect to news listing
if (!$newsArticle) {
    header('Location: /news');
    exit;
}

// Section 1: Title + Breadcrumbs
ob_start();
// Breadcrumbs
echo '<nav style="padding: 15px 0; margin-bottom: 20px;">';
echo '<a href="/" style="color: #28a745; text-decoration: none;">Главная</a>';
echo ' → <a href="/news" style="color: #28a745; text-decoration: none;">Новости</a>';
if (!empty($newsArticle['title_category'])) {
    echo ' → <a href="/category/' . htmlspecialchars($newsArticle['url_category']) . '" style="color: #28a745; text-decoration: none;">' . htmlspecialchars($newsArticle['title_category']) . '</a>';
}
echo ' → <span style="color: #666;">' . htmlspecialchars($newsArticle['title_news']) . '</span>';
echo '</nav>';

// Title
echo '<h1 style="font-size: 32px; color: #333; margin: 20px 0; line-height: 1.3;">' . htmlspecialchars($newsArticle['title_news']) . '</h1>';
$greyContent1 = ob_get_clean();

// Section 2: Empty
$greyContent2 = '';

// Section 3: Meta info (date, category)
ob_start();
echo '<div style="padding: 15px 0; border-bottom: 1px solid #eee; margin-bottom: 20px; display: flex; gap: 20px; align-items: center;">';
echo '<span style="color: #666; font-size: 14px;"><i class="fas fa-calendar"></i> ' . date('d.m.Y', strtotime($newsArticle['created_at'])) . '</span>';
if (!empty($newsArticle['title_category'])) {
    echo '<a href="/category/' . htmlspecialchars($newsArticle['url_category']) . '" style="background: #28a745; color: white; padding: 4px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">' . htmlspecialchars($newsArticle['title_category']) . '</a>';
}
if (!empty($newsArticle['views'])) {
    echo '<span style="color: #666; font-size: 14px;"><i class="fas fa-eye"></i> ' . $newsArticle['views'] . ' просмотров</span>';
}
echo '</div>';
$greyContent3 = ob_get_clean();

// Section 4: Empty
$greyContent4 = '';

// Section 5: Article content
ob_start();
echo '<div style="max-width: 800px; margin: 0 auto; line-height: 1.6;">';

// Featured image
if (!empty($newsArticle['image_news'])) {
    echo '<div style="text-align: center; margin: 30px 0;">';
    echo '<img src="' . htmlspecialchars($newsArticle['image_news']) . '" alt="' . htmlspecialchars($newsArticle['title_news']) . '" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
    echo '</div>';
}

// Article content
echo '<div style="font-size: 18px; color: #333;">';
echo nl2br(htmlspecialchars($newsArticle['content_news']));
echo '</div>';

// Related articles section
echo '<div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #eee;">';
echo '<h3 style="color: #333; margin-bottom: 20px;">Похожие статьи</h3>';

// Get related articles
if (!empty($newsArticle['category_id'])) {
    $relatedQuery = "SELECT title_news, url_news FROM news 
                     WHERE category_id = ? AND id_news != ? AND status = 'published' 
                     ORDER BY created_at DESC LIMIT 3";
    $relatedStmt = mysqli_prepare($connection, $relatedQuery);
    mysqli_stmt_bind_param($relatedStmt, 'ii', $newsArticle['category_id'], $newsArticle['id_news']);
    mysqli_stmt_execute($relatedStmt);
    $relatedResult = mysqli_stmt_get_result($relatedStmt);
    
    if (mysqli_num_rows($relatedResult) > 0) {
        echo '<ul style="list-style: none; padding: 0;">';
        while ($related = mysqli_fetch_assoc($relatedResult)) {
            echo '<li style="margin-bottom: 10px;">';
            echo '<a href="/news/' . htmlspecialchars($related['url_news']) . '" style="color: #28a745; text-decoration: none; font-size: 16px;">';
            echo htmlspecialchars($related['title_news']);
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p style="color: #666;">Нет связанных статей.</p>';
    }
}

echo '</div>';
echo '</div>';
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Section 7: Comments (empty for now)
$blueContent = '';

// Page title
$pageTitle = htmlspecialchars($newsArticle['title_news']) . ' - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
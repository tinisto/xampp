<?php
// Test category display using real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get category data
$categorySlug = 'a-naposledok-ya-skazhu';

$query = "SELECT * FROM categories WHERE url_category = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $categorySlug);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    die("Category not found!");
}

// Get current page
$page = 1;
$perPage = 16;
$offset = 0;

// Get total count - check which ID field exists
$categoryId = isset($category['id_category']) ? $category['id_category'] : $category['id'];
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE category = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Section 1: Title
ob_start();
?>
<div style="padding: 30px; text-align: center;">
    <h1><?php echo htmlspecialchars($category['title_category']); ?></h1>
    <p><?php echo $totalPosts . ' ' . ($totalPosts == 1 ? 'статья' : ($totalPosts < 5 ? 'статьи' : 'статей')); ?></p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Empty for category page
$greyContent2 = '';

// Section 3: Empty for listing
$greyContent3 = '';

// Section 4: Empty for now
$greyContent4 = '';

// Section 5: Posts Grid
ob_start();

// Get posts
$postsQuery = "SELECT id, title_post, text_post, url_slug, date_post 
               FROM posts 
               WHERE category = ? 
               ORDER BY date_post DESC 
               LIMIT ? OFFSET ?";
$stmt = $connection->prepare($postsQuery);
$stmt->bind_param("iii", $categoryId, $perPage, $offset);
$stmt->execute();
$postsResult = $stmt->get_result();

echo "<div style='padding: 20px;'>";
echo "<p>Debug: Category ID = $categoryId, Total posts = $totalPosts, Query returned = " . $postsResult->num_rows . " posts</p>";

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
    echo "<p>Posts array has " . count($posts) . " items. Attempting to render cards...</p>";
    
    // Try to include cards-grid
    $cardsGridPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    if (file_exists($cardsGridPath)) {
        echo "<p>Including cards-grid.php...</p>";
        include_once $cardsGridPath;
        
        // Check if function exists
        if (function_exists('renderCardsGrid')) {
            echo "<p>renderCardsGrid function exists, calling it...</p>";
            renderCardsGrid($posts, 'post', [
                'columns' => 4,
                'gap' => 20,
                'showBadge' => true
            ]);
        } else {
            echo "<p style='color:red;'>ERROR: renderCardsGrid function not found!</p>";
            // Fallback display
            echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;'>";
            foreach ($posts as $post) {
                echo "<div style='border: 1px solid #ddd; padding: 15px;'>";
                echo "<h3>" . htmlspecialchars($post['title_news']) . "</h3>";
                echo "<p>ID: " . $post['id_news'] . "</p>";
                echo "<p>Date: " . $post['created_at'] . "</p>";
                echo "<a href='/post/" . $post['url_news'] . "'>Read more</a>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p style='color:red;'>ERROR: cards-grid.php not found!</p>";
        // Fallback display
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;'>";
        foreach ($posts as $post) {
            echo "<div style='border: 1px solid #ddd; padding: 15px;'>";
            echo "<h3>" . htmlspecialchars($post['title_news']) . "</h3>";
            echo "<p>ID: " . $post['id_news'] . "</p>";
            echo "<p>Date: " . $post['created_at'] . "</p>";
            echo "<a href='/post/" . $post['url_news'] . "'>Read more</a>";
            echo "</div>";
        }
        echo "</div>";
    }
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этой категории пока нет статей</p>
            <p style="margin-top: 10px; font-size: 14px;">Категория ID: ' . $categoryId . '</p>
          </div>';
}
echo "</div>";

$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments for listing
$blueContent = '';

// Set page title
$pageTitle = $category['title_category'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
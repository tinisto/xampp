<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/unified-template.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/card-badge.php';

// Database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

if (isset($_GET['url_category_news'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category_news']);

    // Fetch category data - first check what categories exist
    $queryCategoryNews = "SELECT * FROM news_categories WHERE url_category_news = '$urlCategory'";
    $resultCategoryNews = mysqli_query($connection, $queryCategoryNews);

    if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategoryNews);
        $pageTitle = $categoryData['title_category_news'];
        $categoryId = $categoryData['id_category_news'];
        
        // Debug: Check what category we found
        error_log("Category found: " . $categoryData['title_category_news'] . " with ID: " . $categoryId);
        
        // Fetch news for this category with joined category info
        $newsQuery = "SELECT n.*, nc.title_category_news, nc.url_category_news 
                      FROM news n
                      LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
                      WHERE n.category_news = '$categoryId' AND n.approved = 1 
                      ORDER BY n.date_news DESC 
                      LIMIT 20";
        $newsResult = mysqli_query($connection, $newsQuery);
        
        if (!$newsResult) {
            error_log("News query failed: " . mysqli_error($connection));
            $newsList = [];
        } else {
            $newsList = mysqli_fetch_all($newsResult, MYSQLI_ASSOC);
            error_log("Found " . count($newsList) . " news items for category ID: " . $categoryId);
        }
        
        // Count total news in this category
        $totalNews = count($newsList);
        
        // If no news found, still show the page with empty state
        if (empty($newsList)) {
            $totalNews = 0;
        }
        
    } else {
        // Category not found - check all available categories for debugging
        $allCategoriesQuery = "SELECT url_category_news, title_category_news FROM news_categories";
        $allCategoriesResult = mysqli_query($connection, $allCategoriesQuery);
        
        header("Location: /404");
        exit();
    }
} else {
    header("Location: /404");
    exit();
}

// Prepare page content
ob_start();
?>

<div class="news-grid">
    <?php if (!empty($newsList)): ?>
        <?php foreach ($newsList as $news): ?>
            <article class="news-card">
                <div class="news-image-container card-image-container">
                    <?php 
                    // Handle image - check if image files exist
                    $image = '';
                    $imageFound = false;
                    
                    // Try image 1
                    if (!empty($news['image_news_1']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_1.jpg')) {
                        $image = '/images/news-images/' . $news['id_news'] . '_1.jpg';
                        $imageFound = true;
                    }
                    // Try image 2 if image 1 not found
                    elseif (!empty($news['image_news_2']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_2.jpg')) {
                        $image = '/images/news-images/' . $news['id_news'] . '_2.jpg';
                        $imageFound = true;
                    }
                    // Try image 3 if others not found
                    elseif (!empty($news['image_news_3']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/news-images/' . $news['id_news'] . '_3.jpg')) {
                        $image = '/images/news-images/' . $news['id_news'] . '_3.jpg';
                        $imageFound = true;
                    }
                    ?>
                    <?php if ($image): ?>
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($news['title_news']) ?>" class="news-image">
                    <?php else: ?>
                        <div class="news-image">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                    <?php endif; ?>
                    <?php renderCardBadge($categoryData['title_category_news'], '', 'overlay', 'green'); ?>
                </div>
                <div class="news-content">
                    <h3 class="news-title">
                        <a href="/news/<?= htmlspecialchars($news['url_news']) ?>">
                            <?= htmlspecialchars($news['title_news']) ?>
                        </a>
                    </h3>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h3>Новости скоро появятся</h3>
            <p>Мы работаем над наполнением этой категории актуальными новостями.</p>
        </div>
    <?php endif; ?>
</div>

<script>
// Category news search functionality
document.getElementById('categoryNewsSearch').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.news-card').forEach(card => {
        const title = card.querySelector('.news-title').textContent.toLowerCase();
        const isVisible = title.includes(search);
        card.style.display = isVisible ? '' : 'none';
    });
});
</script>

<?php
$pageContent = ob_get_clean();

// Render the unified page
$options = [
    'headerStats' => [
        ['number' => $totalNews, 'label' => 'Новостей']
    ],
    'showSearch' => true,
    'searchPlaceholder' => 'Поиск новостей...',
    'searchId' => 'categoryNewsSearch',
    'metaDescription' => "Новости категории: " . $categoryData['title_category_news']
];

renderUnifiedPage($pageTitle, $pageContent, $options);
?>
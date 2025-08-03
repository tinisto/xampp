<?php
/**
 * Simple News Category Navigation (No AJAX)
 * Falls back to regular page navigation if AJAX fails
 */

function renderSimpleNewsCategoryNavigation($currentCategoryId = null, $categories = [], $basePath = '/news/') {
    // If categories not provided, fetch them
    if (empty($categories)) {
        global $connection;
        $categoriesQuery = "SELECT * FROM news_categories ORDER BY title_category_news";
        $categoriesResult = mysqli_query($connection, $categoriesQuery);
        $categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);
    }
    
    // Determine if we're on the main news page (all news)
    $isMainNewsPage = ($currentCategoryId === null);
?>

<style>
    .news-category-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        padding: 10px;
        margin: 10px 0;
        flex-wrap: wrap;
    }
    
    .news-category-nav a {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        background: white;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .news-category-nav a:hover {
        color: #28a745;
        border-color: #28a745;
        background: #f8f9fa;
        text-decoration: none;
    }
    
    .news-category-nav a.active {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }
    
    @media (max-width: 768px) {
        .news-category-nav {
            gap: 10px;
            padding: 15px 10px;
        }
        
        .news-category-nav a {
            font-size: 13px;
            padding: 6px 12px;
        }
    }
</style>

<div class="news-category-nav">
    <!-- "All News" link -->
    <a href="/news" class="<?= $isMainNewsPage ? 'active' : '' ?>" title="Все новости">
        Все новости
    </a>
    
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category): ?>
            <a href="<?= $basePath ?><?= htmlspecialchars($category['url_category_news']) ?>" 
               class="<?= (!$isMainNewsPage && $category['id_category_news'] == $currentCategoryId) ? 'active' : '' ?>"
               title="<?= htmlspecialchars($category['title_category_news']) ?>">
                <?= htmlspecialchars($category['title_category_news']) ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
}
?>
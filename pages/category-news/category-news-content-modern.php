<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
// Include loading placeholders
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders.php';
// Include news category navigation
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/news-category-navigation-simple.php';

if (isset($_GET['url_category_news'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category_news']);

    // Fetch category data
    $queryCategoryNews = "SELECT * FROM news_categories WHERE url_category_news = ?";
    $stmt = mysqli_prepare($connection, $queryCategoryNews);
    mysqli_stmt_bind_param($stmt, 's', $urlCategory);
    mysqli_stmt_execute($stmt);
    $resultCategoryNews = mysqli_stmt_get_result($stmt);

    if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategoryNews);
?>

<style>
    .news-container {
        max-width: 1200px;
        margin: 40px auto 0;
        padding: 0 20px;
    }
    .news-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .news-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .news-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
        background: #f0f0f0;
    }
    .news-image-container {
        position: relative;
        width: 100%;
        height: 160px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .news-image-placeholder {
        color: #999;
        font-size: 14px;
    }
    .news-content {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .news-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(40, 167, 69, 0.9);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        z-index: 2;
    }
    .news-date {
        color: #28a745;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .news-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        line-height: 1.3;
        flex: 1;
    }
    .news-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .news-title a:hover {
        color: #28a745;
    }
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
        margin: 10px 0;
    }
    .no-news {
        text-align: center;
        padding: 100px 20px;
        color: #666;
    }
    .no-news i {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 20px;
    }
    @media (max-width: 768px) {
        .news-hero h1 {
            font-size: 24px;
        }
        .news-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .news-content {
            padding: 12px;
        }
    }
</style>


<div class="news-container">
    <!-- Category Navigation -->
    <?php renderSimpleNewsCategoryNavigation($categoryData['id_category_news']); ?>
    
    <?php
    mysqli_free_result($resultCategoryNews);

    // Fetch posts associated with the category
    $categoryNewsId = $categoryData['id_category_news'];
    $postsPerPage = 12;
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($currentPage - 1) * $postsPerPage;

    // Get total posts in the category
    $queryTotalPosts = "SELECT COUNT(*) as total_posts FROM news WHERE category_news = ?";
    $stmtTotalPosts = mysqli_prepare($connection, $queryTotalPosts);
    mysqli_stmt_bind_param($stmtTotalPosts, 'i', $categoryNewsId);
    mysqli_stmt_execute($stmtTotalPosts);
    $resultTotalPosts = mysqli_stmt_get_result($stmtTotalPosts);
    $totalPosts = mysqli_fetch_assoc($resultTotalPosts)['total_posts'];
    $totalPages = ceil($totalPosts / $postsPerPage);

    // Fetch posts with pagination
    $queryPosts = "SELECT * FROM news WHERE category_news = ? ORDER BY date_news DESC LIMIT ? OFFSET ?";
    $stmtPosts = mysqli_prepare($connection, $queryPosts);
    mysqli_stmt_bind_param($stmtPosts, 'iii', $categoryNewsId, $postsPerPage, $offset);
    mysqli_stmt_execute($stmtPosts);
    $resultPosts = mysqli_stmt_get_result($stmtPosts);

    if (mysqli_num_rows($resultPosts) > 0): ?>
        <div class="news-grid">
            <?php while ($post = mysqli_fetch_assoc($resultPosts)): 
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$post['id_news']}_1.jpg";
                $imageUrl = file_exists($imagePath) 
                    ? "/images/news-images/{$post['id_news']}_1.jpg" 
                    : "/images/news-images/default.png";
                
                $date = new DateTime($post['date_news']);
                $formattedDate = $date->format('d.m.Y');
                
                // Check if image exists
                $hasImage = file_exists($imagePath);
            ?>
            <article class="news-card">
                <a href="/news/<?= htmlspecialchars($post['url_news']) ?>" class="text-decoration-none">
                    <?php if ($hasImage): ?>
                        <div class="news-image-container">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($post['title_news']) ?>" class="news-image">
                            <div class="news-badge"><?= htmlspecialchars($categoryData['title_category_news']) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="news-image-container">
                            <div class="news-image-placeholder">
                                <i class="fas fa-newspaper"></i><br>
                                Изображение отсутствует
                            </div>
                            <div class="news-badge"><?= htmlspecialchars($categoryData['title_category_news']) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="news-content">
                        <div class="news-date"><?= $formattedDate ?></div>
                        <h2 class="news-title">
                            <?= htmlspecialchars($post['title_news']) ?>
                        </h2>
                    </div>
                </a>
            </article>
            <?php endwhile; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <?php 
            $baseUrl = "/category-news/{$urlCategory}";
            echo generatePagination($currentPage, $totalPages, $baseUrl); 
            ?>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="no-news">
            <i class="fas fa-newspaper"></i>
            <h3>Новостей пока нет</h3>
            <p>В этой категории еще нет опубликованных новостей</p>
        </div>
        
        <!-- Show placeholders for upcoming content -->
        <div class="mt-5">
            <h4 class="text-center mb-4" style="color: #666;">Скоро здесь появятся новости</h4>
            <div class="news-grid">
                <?php renderCardPlaceholder(6); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
    } else {
        header("Location: /404");
        exit();
    }
} else {
    header("Location: /404");
    exit();
}
?>
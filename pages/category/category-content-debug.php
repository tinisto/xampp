<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
// Include loading placeholders
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders.php';

if (isset($_GET['url_category'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);

    // Debug output
    echo "<!-- Debug: url_category = $urlCategory -->\n";

    // Fetch category data using prepared statement
    $queryCategory = "SELECT * FROM categories WHERE url_category = ?";
    $stmt = mysqli_prepare($connection, $queryCategory);
    mysqli_stmt_bind_param($stmt, 's', $urlCategory);
    mysqli_stmt_execute($stmt);
    $resultCategory = mysqli_stmt_get_result($stmt);

    if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategory);
        
        // Debug output
        echo "<!-- Debug: Category found - ID: {$categoryData['id_category']}, Title: {$categoryData['title_category']} -->\n";
?>

<style>
    .category-hero {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
        text-align: center;
    }
    .category-hero h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .category-stats {
        font-size: 16px;
        opacity: 0.9;
        margin-top: 10px;
    }
    .posts-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .post-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    .post-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
    }
    .post-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .post-image-placeholder {
        color: #999;
        font-size: 16px;
        text-align: center;
    }
    .post-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(40, 167, 69, 0.9);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 2;
    }
    .post-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .post-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-size: 14px;
        color: #666;
    }
    .post-date {
        color: #28a745;
        font-weight: 500;
    }
    .post-views {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .post-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        line-height: 1.4;
        flex: 1;
        color: #333;
        transition: color 0.3s ease;
    }
    .post-card:hover .post-title {
        color: #28a745;
    }
    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
        margin: 50px 0;
    }
    .no-posts {
        text-align: center;
        padding: 100px 20px;
        color: #666;
    }
    .no-posts i {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 20px;
    }
    @media (max-width: 768px) {
        .category-hero h1 {
            font-size: 28px;
        }
        .posts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
    mysqli_free_result($resultCategory);

    // Fetch posts associated with the category
    $categoryId = $categoryData['id_category'];
    $postsPerPage = 12;
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($currentPage - 1) * $postsPerPage;

    // Get total posts in the category
    $queryTotalPosts = "SELECT COUNT(*) as total_posts FROM posts WHERE category = ?";
    $stmtTotalPosts = mysqli_prepare($connection, $queryTotalPosts);
    mysqli_stmt_bind_param($stmtTotalPosts, 'i', $categoryId);
    mysqli_stmt_execute($stmtTotalPosts);
    $resultTotalPosts = mysqli_stmt_get_result($stmtTotalPosts);
    $totalPosts = mysqli_fetch_assoc($resultTotalPosts)['total_posts'];
    $totalPages = ceil($totalPosts / $postsPerPage);
    
    // Debug output
    echo "<!-- Debug: Total posts in category: $totalPosts -->\n";
?>

<div class="category-hero">
    <div class="container">
        <h1><?= htmlspecialchars($categoryData['title_category']) ?></h1>
        <div class="category-stats"><?= $totalPosts ?> <?php 
            $lastDigit = $totalPosts % 10;
            $lastTwoDigits = $totalPosts % 100;
            if ($lastTwoDigits >= 11 && $lastTwoDigits <= 14) {
                echo 'статей';
            } elseif ($lastDigit == 1) {
                echo 'статья';
            } elseif ($lastDigit >= 2 && $lastDigit <= 4) {
                echo 'статьи';
            } else {
                echo 'статей';
            }
        ?></div>
    </div>
</div>

<div class="posts-container">
    <?php
    // Fetch posts with pagination
    $queryPosts = "SELECT * FROM posts WHERE category = ? ORDER BY date_post DESC LIMIT ? OFFSET ?";
    $stmtPosts = mysqli_prepare($connection, $queryPosts);
    mysqli_stmt_bind_param($stmtPosts, 'iii', $categoryId, $postsPerPage, $offset);
    mysqli_stmt_execute($stmtPosts);
    $resultPosts = mysqli_stmt_get_result($stmtPosts);
    
    // Debug output
    $numPosts = mysqli_num_rows($resultPosts);
    echo "<!-- Debug: Posts found: $numPosts -->\n";

    if (mysqli_num_rows($resultPosts) > 0): ?>
        <div class="posts-grid">
            <?php while ($post = mysqli_fetch_assoc($resultPosts)): 
                // Debug first post
                static $debugFirst = true;
                if ($debugFirst) {
                    echo "<!-- Debug: First post - ID: {$post['id_post']}, Title: {$post['title_post']} -->\n";
                    $debugFirst = false;
                }
                
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$post['id_post']}_1.jpg";
                $imageUrl = file_exists($imagePath) 
                    ? "/images/posts-images/{$post['id_post']}_1.jpg" 
                    : "/images/posts-images/default.png";
                
                $date = new DateTime($post['date_post']);
                $formattedDate = $date->format('d.m.Y');
                
                // Create excerpt from text
                $excerpt = strip_tags($post['text_post']);
                $excerpt = mb_substr($excerpt, 0, 150) . '...';
            ?>
            <article class="post-card">
                <a href="/post/<?= htmlspecialchars($post['url_post']) ?>" class="text-decoration-none">
                    <?php if (file_exists($imagePath)): ?>
                        <div class="post-image-container">
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($post['title_post']) ?>" class="post-image">
                            <div class="post-badge"><?= htmlspecialchars($categoryData['title_category']) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="post-image-container">
                            <div class="post-image-placeholder">
                                <i class="fas fa-newspaper"></i><br>
                                Изображение отсутствует
                            </div>
                            <div class="post-badge"><?= htmlspecialchars($categoryData['title_category']) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-date"><?= $formattedDate ?></span>
                            <div class="post-views">
                                <i class="fas fa-eye"></i>
                                <span><?= number_format($post['view_post']) ?></span>
                            </div>
                        </div>
                        <h2 class="post-title">
                            <?= htmlspecialchars($post['title_post']) ?>
                        </h2>
                    </div>
                </a>
            </article>
            <?php endwhile; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <?php 
            $baseUrl = "/category/{$urlCategory}";
            echo generatePagination($currentPage, $totalPages, $baseUrl); 
            ?>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="no-posts">
            <i class="fas fa-file-alt"></i>
            <h3>Статей пока нет</h3>
            <p>В этой категории еще нет опубликованных статей</p>
        </div>
        
        <!-- Show placeholders for upcoming content -->
        <div class="mt-5">
            <h4 class="text-center mb-4" style="color: #666;">Скоро здесь появятся статьи</h4>
            <div class="posts-grid">
                <?php renderCardPlaceholder(6); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
    } else {
        echo "<!-- Debug: Category not found -->\n";
        header("Location: /404");
        exit();
    }
} else {
    echo "<!-- Debug: No url_category parameter -->\n";
    header("Location: /404");
    exit();
}
?>
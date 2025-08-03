<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
// Include loading placeholders
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/loading-placeholders.php';

if (isset($_GET['url_category'])) {
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);

    // Fetch category data using prepared statement
    $queryCategory = "SELECT * FROM categories WHERE url_category = ?";
    $stmt = mysqli_prepare($connection, $queryCategory);
    mysqli_stmt_bind_param($stmt, 's', $urlCategory);
    mysqli_stmt_execute($stmt);
    $resultCategory = mysqli_stmt_get_result($stmt);

    if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategory);
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
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0,0,0,0.06);
        position: relative;
    }
    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        border-color: rgba(40, 167, 69, 0.2);
    }
    .post-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .post-card:hover .post-image {
        transform: scale(1.02);
    }
    .post-image-container {
        position: relative;
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .post-image-placeholder {
        color: #999;
        font-size: 16px;
        text-align: center;
    }
    .post-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(40, 167, 69, 0.95);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        z-index: 2;
        backdrop-filter: blur(4px);
    }
    .post-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .post-meta {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 12px;
        font-size: 12px;
        color: #8e9aaf;
    }
    .post-date {
        color: #28a745;
        font-weight: 500;
    }
    .post-views {
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }
    .post-views i {
        font-size: 11px;
        opacity: 0.7;
    }
    .post-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 0;
        line-height: 1.5;
        flex: 1;
        color: #2d3748;
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    /* Dark mode styles */
    [data-bs-theme="dark"] .post-card {
        background: #1a202c;
        color: #e4e6eb;
        border-color: rgba(255,255,255,0.1);
    }
    [data-bs-theme="dark"] .post-card:hover {
        border-color: rgba(40, 167, 69, 0.3);
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    }
    [data-bs-theme="dark"] .post-title {
        color: #f7fafc !important;
    }
    [data-bs-theme="dark"] .post-card:hover .post-title {
        color: #28a745 !important;
    }
    [data-bs-theme="dark"] .post-meta {
        color: #718096;
    }
    [data-bs-theme="dark"] .post-views {
        color: #718096;
    }
    [data-bs-theme="dark"] .post-image-container {
        background: linear-gradient(135deg, #2d3748, #1a202c);
    }
    [data-bs-theme="dark"] .post-image-placeholder {
        color: #718096;
    }
    [data-bs-theme="dark"] .category-hero {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
    }
    [data-bs-theme="dark"] .no-posts {
        color: #718096;
    }
    .post-card:hover .post-title {
        color: #28a745;
    }
    .posts-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
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
    @media (max-width: 1200px) {
        .posts-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .category-hero h1 {
            font-size: 28px;
        }
        .posts-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }
    @media (max-width: 480px) {
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

    if (mysqli_num_rows($resultPosts) > 0): ?>
        <div class="posts-grid">
            <?php while ($post = mysqli_fetch_assoc($resultPosts)): 
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
                        </div>
                    <?php else: ?>
                        <div class="post-image-container">
                            <div class="post-image-placeholder">
                                <i class="fas fa-newspaper"></i><br>
                                Изображение отсутствует
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="post-content">
                        <div class="post-meta">
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
            include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
            renderPaginationModern($currentPage, $totalPages, "/category/{$urlCategory}");
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
        header("Location: /404");
        exit();
    }
} else {
    header("Location: /404");
    exit();
}
?>
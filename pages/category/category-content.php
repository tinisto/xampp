<?php
// Category content - data already fetched in category-data-fetch.php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get category ID from globals
$categoryId = $GLOBALS['categoryId'] ?? null;

// Check if categoryId was set by the data fetch - also ensure database connection
if ($categoryId && isset($connection) && $connection && !$connection->connect_error) {
?>
<div class="main-content">
    <h5 class='text-center fw-bold'>
        <?= $pageTitle ?>
    </h5>
</div>
<?php

    // Fetch posts associated with the category  
    $postsPerPage = 12; // Total posts per page
    $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $offset = ($currentPage - 1) * $postsPerPage;

    // Get total posts in the category using prepared statement
    $stmtTotal = $connection->prepare("SELECT COUNT(*) as total_posts FROM posts WHERE category = ?");
    $stmtTotal->bind_param('i', $categoryId);
    $stmtTotal->execute();
    $resultTotal = $stmtTotal->get_result();
    $totalPosts = $resultTotal->fetch_assoc()['total_posts'];
    $stmtTotal->close();

    $totalPages = ceil($totalPosts / $postsPerPage);

    // Fetch posts with prepared statement
    $stmtPosts = $connection->prepare("SELECT id_post, title_post, url_post FROM posts WHERE category = ? ORDER BY date_post DESC LIMIT ?, ?");
    $stmtPosts->bind_param('iii', $categoryId, $offset, $postsPerPage);
    $stmtPosts->execute();
    $resultPosts = $stmtPosts->get_result();
?>

<div class="container">
    <div class="row">
        <?php if ($resultPosts->num_rows > 0): ?>
            <?php while ($rowPost = $resultPosts->fetch_assoc()): ?>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="card index-card mb-3">
                        <a href="/post/<?= htmlspecialchars($rowPost['url_post']) ?>" class="text-decoration-none">
                            <div class="card-img-container">
                                <?php
                                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowPost['id_post']}_1.jpg";
                                if (file_exists($imagePath)) {
                                    echo '<img src="../images/posts-images/' . $rowPost['id_post'] . '_1.jpg" alt="Post Image" class="img-fluid">';
                                } else {
                                    echo '<img src="../images/posts-images/default.png" alt="Post Image" class="img-fluid">';
                                }
                                ?>
                            </div>
                            <div class="card-title-overlay">
                                <h6 class="card-title mb-0">
                                    <?= htmlspecialchars($rowPost['title_post'], ENT_QUOTES, 'UTF-8') ?>
                                </h6>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted">В этой категории пока нет статей.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <?php generatePagination($currentPage, $totalPages); ?>
    <?php endif; ?>
</div>

<?php
    $stmtPosts->close();
} else {
    echo '<div class="container"><p class="text-center text-muted">Категория не найдена или база данных недоступна.</p></div>';
}
?>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";

// Check if the URL parameter is set
if (isset($_GET['url_category'])) {
    // Sanitize the input
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category']);

    // Fetch category data
    $queryCategory = "SELECT * FROM categories WHERE url_category = ?";
    $stmtCategory = mysqli_prepare($connection, $queryCategory);
    mysqli_stmt_bind_param($stmtCategory, 's', $urlCategory);
    mysqli_stmt_execute($stmtCategory);
    $resultCategory = mysqli_stmt_get_result($stmtCategory);

    if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategory);

        // Display category information
?>
        <div class="main-content">
            <h5 class='text-center fw-bold'>
                <?= htmlspecialchars($categoryData['title_category']) ?>
            </h5>
            <p>
                <?= nl2br(htmlspecialchars($categoryData['text_category'])) ?>
            </p>
        </div>
        <?php

        // Free the result set for categories
        mysqli_free_result($resultCategory);

        // Fetch posts associated with the category
        $categoryId = $categoryData['id_category'];
        $postsPerPage = 4; // Number of posts per row
        $rowsPerPage = 5; // Number of rows per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($currentPage - 1) * ($postsPerPage * $rowsPerPage);

        // Get total posts in the category
        $queryTotalPosts = "SELECT COUNT(*) as total_posts FROM posts WHERE category = ?";
        $stmtTotalPosts = mysqli_prepare($connection, $queryTotalPosts);
        mysqli_stmt_bind_param($stmtTotalPosts, 'i', $categoryId);
        mysqli_stmt_execute($stmtTotalPosts);
        $resultTotalPosts = mysqli_stmt_get_result($stmtTotalPosts);
        $totalPosts = mysqli_fetch_assoc($resultTotalPosts)['total_posts'];

        $totalPages = ceil($totalPosts / ($postsPerPage * $rowsPerPage));

        // Display posts
        ?>
        <div class="container">
            <?php
            for ($rowIndex = 1; $rowIndex <= $rowsPerPage; $rowIndex++) {
            ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
                    <?php
                    $queryPosts = "SELECT * FROM posts WHERE category = ? ORDER BY date_post DESC LIMIT ?, ?";
                    $stmtPosts = mysqli_prepare($connection, $queryPosts);
                    mysqli_stmt_bind_param($stmtPosts, 'sii', $categoryId, $offset, $postsPerPage);
                    mysqli_stmt_execute($stmtPosts);
                    $resultPosts = mysqli_stmt_get_result($stmtPosts);

                    while ($rowPost = mysqli_fetch_assoc($resultPosts)) {
                    ?>
                        <div class="col mb-4">
                            <div class="card index-card">
                                <a href="/post/<?= htmlspecialchars($rowPost['url_post']) ?>" class="text-white text-decoration-none">
                                    <?php if ($rowPost['id_post']) { ?>
                                        <div class="card-img-container">
                                            <?php
                                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$rowPost['id_post']}_1.jpg";
                                            $imageSrc = file_exists($imagePath) ? "../images/posts-images/{$rowPost['id_post']}_1.jpg" : "../images/posts-images/default.png";
                                            ?>
                                            <img src="<?= $imageSrc ?>" alt="Post Image">
                                        </div>
                                        <div class="card-title-overlay">
                                            <h5 class="card-title mb-0">
                                                <?= htmlspecialchars($rowPost['title_post']) ?>
                                            </h5>
                                        </div>
                                    <?php } ?>
                                </a>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
                $offset += $postsPerPage;
            }
            ?>
        </div>

        <?php
        // Display pagination
        generatePagination($currentPage, $totalPages);
        ?>

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
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";

if (isset($_GET['url_category_news'])) {
    // Sanitize input
    $urlCategory = mysqli_real_escape_string($connection, $_GET['url_category_news']);

    // Fetch category data
    $queryCategoryNews = "SELECT * FROM news_categories WHERE url_category_news = ?";
    $stmt = mysqli_prepare($connection, $queryCategoryNews);
    mysqli_stmt_bind_param($stmt, 's', $urlCategory);
    mysqli_stmt_execute($stmt);
    $resultCategoryNews = mysqli_stmt_get_result($stmt);

    if ($resultCategoryNews && mysqli_num_rows($resultCategoryNews) > 0) {
        $categoryData = mysqli_fetch_assoc($resultCategoryNews);

        // Display category information
?>
        <div class="main-content">
            <h5 class='text-center fw-bold'>
                <?= htmlspecialchars($categoryData['title_category_news']) ?>
            </h5>
            <p>
                <?= nl2br(htmlspecialchars($categoryData['text_category_news'])) ?>
            </p>
        </div>
        <?php

        // Free the result set for categories
        mysqli_free_result($resultCategoryNews);

        // Fetch posts associated with the category
        $categoryNewsId = $categoryData['id_category_news'];
        $postsPerPage = 4; // Number of posts per row
        $rowsPerPage = 5; // Number of rows per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($currentPage - 1) * ($postsPerPage * $rowsPerPage);

        // Get total posts in the category
        $queryTotalPosts = "SELECT COUNT(*) as total_posts FROM news WHERE category_news = ?";
        $stmtTotalPosts = mysqli_prepare($connection, $queryTotalPosts);
        mysqli_stmt_bind_param($stmtTotalPosts, 'i', $categoryNewsId);
        mysqli_stmt_execute($stmtTotalPosts);
        $resultTotalPosts = mysqli_stmt_get_result($stmtTotalPosts);
        $totalPosts = mysqli_fetch_assoc($resultTotalPosts)['total_posts'];

        $totalPages = ceil($totalPosts / ($postsPerPage * $rowsPerPage));

        // Display posts
        ?>
        <?php
        for ($rowIndex = 1; $rowIndex <= $rowsPerPage; $rowIndex++) {
        ?>
            <div class="row row-cols-1">
                <?php
                $queryNews = "SELECT * FROM news WHERE category_news = ? ORDER BY date_news DESC LIMIT ?, ?";
                $stmtNews = mysqli_prepare($connection, $queryNews);
                mysqli_stmt_bind_param($stmtNews, 'sii', $categoryNewsId, $offset, $postsPerPage);
                mysqli_stmt_execute($stmtNews);
                $resultNews = mysqli_stmt_get_result($stmtNews);

                while ($rowNews = mysqli_fetch_assoc($resultNews)) {
                ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-none d-sm-block flex-shrink-0">
                            <div class="card">
                                <a href="/news/<?= htmlspecialchars($rowNews['url_news']) ?>" class="link-custom">
                                    <?php
                                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$rowNews['id_news']}_1.jpg";

                                    if (file_exists($imagePath)) {
                                    ?>
                                        <img src="../images/news-images/<?= htmlspecialchars($rowNews['id_news']) ?>_1.jpg" alt="News Image" width="100px">
                                    <?php
                                    }
                                    ?>
                                </a>
                            </div>
                        </div>
                        <div class="ms-xs-0 ms-md-3 flex-grow-1">
                            <a href="/news/<?= htmlspecialchars($rowNews['url_news']) ?>" class="link-custom">
                                <?= htmlspecialchars($rowNews['title_news']) ?>
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
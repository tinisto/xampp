<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountNews = "SELECT COUNT(*) as total FROM news";
$resultCountNews = $connection->query($sqlCountNews);
$rowCountNews = $resultCountNews->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-paper-plane\"></i> {$rowCountNews}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">News</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/news-dashboard/admin-view-news/admin-view-news.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
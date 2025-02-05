<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountPosts = "SELECT COUNT(*) as total FROM posts";
$resultCountPosts = $connection->query($sqlCountPosts);
$rowCountPosts = $resultCountPosts->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-paper-plane\"></i> {$rowCountPosts}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">Posts</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/posts-dashboard/posts-view/posts-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
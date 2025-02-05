<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountComments = "SELECT COUNT(*) as total FROM comments";
$resultCountComments = $connection->query($sqlCountComments);
$rowCountComments = $resultCountComments->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-comment\"></i> {$rowCountComments}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">Comments</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/comments-dashboard/comments-view/comments-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
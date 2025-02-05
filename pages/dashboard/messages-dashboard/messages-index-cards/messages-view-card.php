<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountMessages = "SELECT COUNT(*) as total FROM messages";
$resultCountMessages = $connection->query($sqlCountMessages);
$rowCountMessages = $resultCountMessages->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-envelope\"></i> {$rowCountMessages}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">Messages</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/messages-dashboard/messages-view/messages-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
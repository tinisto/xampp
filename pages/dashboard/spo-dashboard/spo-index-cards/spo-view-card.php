<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountSPO = "SELECT COUNT(*) as total FROM spo";
$resultCountSPO = $connection->query($sqlCountSPO);
$rowCountSPO = $resultCountSPO->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-paper-plane\"></i> {$rowCountSPO}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">spo</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/spo-dashboard/spo-view/spo-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
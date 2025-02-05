<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountVPO = "SELECT COUNT(*) as total FROM vpo";
$resultCountVPO = $connection->query($sqlCountVPO);
$rowCountVPO = $resultCountVPO->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-paper-plane\"></i> {$rowCountVPO}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">vpo</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/vpo-dashboard/vpo-view/vpo-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
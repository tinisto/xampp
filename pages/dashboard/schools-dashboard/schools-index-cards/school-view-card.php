<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountSchools = "SELECT COUNT(*) as total FROM schools";
$resultCountSchools = $connection->query($sqlCountSchools);
$rowCountSchools = $resultCountSchools->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-paper-plane\"></i> {$rowCountSchools}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">Schools</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/schools-dashboard/schools-view/schools-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
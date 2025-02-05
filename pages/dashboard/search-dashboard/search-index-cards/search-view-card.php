<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountSearch = "SELECT COUNT(*) as total FROM search_queries";
$resultCountSearch = $connection->query($sqlCountSearch);
$rowCountSearch = $resultCountSearch->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-envelope\"></i> {$rowCountSearch}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">Search</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/search-dashboard/search-view/search-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
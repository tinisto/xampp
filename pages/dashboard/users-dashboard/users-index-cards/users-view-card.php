<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$sqlCountUsers = "SELECT COUNT(*) as total FROM users";
$resultCountUsers = $connection->query($sqlCountUsers);
$rowCountUsers = $resultCountUsers->fetch_assoc()["total"];

$buttonTitle = "<i class=\"fas fa-user\"></i> {$rowCountUsers}";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <h5 class="card-title text-xl font-weight-bold text-dark">Users</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/users-dashboard/users-view/users-view.php",
                "sm",
                "success"
            ); ?>
        </div>
    </div>
</div>
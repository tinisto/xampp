<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';

$buttonTitle = "Create School";
?>
<div class="col d-flex">
    <div class="card rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center bg-light">
        <?php renderButton(
            $buttonTitle,
            // "/pages/common/create.php",
            "/pages/common/create.php?type=school",
            "sm",
            "light"
        ); ?>
    </div>
</div>
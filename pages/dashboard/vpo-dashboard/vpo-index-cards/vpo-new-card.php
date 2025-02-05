<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';
$query =
    "SELECT COUNT(*) AS pending_count FROM vpo WHERE approved IS NULL OR approved != 1;";
$result = $connection->query($query);
$pendingVPOCount = 0;
if ($result && ($row = $result->fetch_assoc())) {
    $pendingVPOCount = $row["pending_count"];
}

// Set card color based on pending approvals count
// Red if pending, Green if no pending
$cardClass = $pendingVPOCount > 0 ? "bg-danger" : "bg-success";
$buttonTitle =
    $pendingVPOCount > 0
    ? "Pending Approvals: {$pendingVPOCount}"
    : "No need actions";
$buttonColor = $pendingVPOCount > 0 ? "warning" : "success";
?>
<div class="col d-flex">
    <div class="card <?php echo $cardClass; ?> rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-xl font-weight-bold text-dark">New VPO</h5>

        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/vpo-dashboard/vpo-new/vpo-new.php",
                "sm",
                $buttonColor
            ); ?>
        </div>
    </div>
</div>
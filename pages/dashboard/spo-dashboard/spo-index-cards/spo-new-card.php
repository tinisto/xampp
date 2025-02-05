<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';
$query =
    "SELECT COUNT(*) AS pending_count FROM spo WHERE approved IS NULL OR approved != 1;";
$result = $connection->query($query);
$pendingSPOCount = 0;
if ($result && ($row = $result->fetch_assoc())) {
    $pendingSPOCount = $row["pending_count"];
}

// Set card color based on pending approvals count
// Red if pending, Green if no pending
$cardClass = $pendingSPOCount > 0 ? "bg-danger" : "bg-success";
$buttonTitle =
    $pendingSPOCount > 0
    ? "Pending Approvals: {$pendingSPOCount}"
    : "No need actions";
$buttonColor = $pendingSPOCount > 0 ? "warning" : "success";
?>
<div class="col d-flex">
    <div class="card <?php echo $cardClass; ?> rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-xl font-weight-bold text-dark">New SPO</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/spo-dashboard/spo-new/spo-new.php",
                "sm",
                $buttonColor
            ); ?>
        </div>
    </div>
</div>
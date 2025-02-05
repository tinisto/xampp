<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/pages/dashboard/spo-dashboard/spo-edit/spo-edit-data-fetch.php';

$query = "SELECT COUNT(*) AS pending_count FROM spo_verification";
$resultSPO = $connection->query($query);
$pendingEditSpoCount = 0;
if ($resultSPO && ($row = $resultSPO->fetch_assoc())) {
    $pendingEditSpoCount = $row["pending_count"];
}

// Set card color based on pending approvals count
// Red if pending, Green if no pending
$cardClass = $rowVerification > 0 ? "bg-danger" : "bg-success";
$buttonTitle =
    $pendingEditSpoCount > 0
        ? "Pending Approvals: {$pendingEditSpoCount}"
        : "No need actions";
$buttonColor = $pendingEditSpoCount > 0 ? "warning" : "success";
?>
<div class="col d-flex">
    <div class="card <?php echo $cardClass; ?> rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-xl font-weight-bold text-dark">Edit SPO</h5>
        <div class="mt-auto">
        <?php renderButton(
            $buttonTitle,
            "pages/dashboard/spo-dashboard/spo-edit/spo-edit.php",
            "sm",
            $buttonColor
        ); ?>
        </div>
    </div>
</div>

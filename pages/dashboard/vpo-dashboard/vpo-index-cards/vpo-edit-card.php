<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/pages/dashboard/vpo-dashboard/vpo-edit/vpo-edit-data-fetch.php';


$query = "SELECT COUNT(*) AS pending_count FROM vpo_verification";
$result = $connection->query($query);
$pendingEditVpoCount = 0;
if ($result && ($row = $result->fetch_assoc())) {
    $pendingEditVpoCount = $row["pending_count"];
}

// Set card color based on pending approvals count
// Red if pending, Green if no pending
$cardClass = $rowVerification > 0 ? "bg-danger" : "bg-success";
$buttonTitle =
    $pendingEditVpoCount > 0
    ? "Pending Approvals: {$pendingEditVpoCount}"
    : "No need actions";
$buttonColor = $pendingEditVpoCount > 0 ? "warning" : "success";
?>
<div class="col d-flex">
    <div class="card <?php echo $cardClass; ?> rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-xl font-weight-bold text-dark">Edit VPO</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/vpo-dashboard/vpo-edit/vpo-edit.php",
                "sm",
                $buttonColor
            ); ?>
        </div>
    </div>
</div>
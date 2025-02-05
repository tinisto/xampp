<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';
include_once $_SERVER['DOCUMENT_ROOT'] . "/pages/dashboard/schools-dashboard/schools-approve-edit/schools-approve-edit-data-fetch.php";

$query = "SELECT COUNT(*) AS pending_count FROM schools_verification";
$result = $connection->query($query);
$pendingEditSchoolCount = 0;
if ($result && ($row = $result->fetch_assoc())) {
    $pendingEditSchoolCount = $row["pending_count"];
}

// Set card color based on pending approvals count
// Red if pending, Green if no pending
$cardClass = $rowVerification > 0 ? "bg-danger" : "bg-success";
$buttonTitle =
    $pendingEditSchoolCount > 0
    ? "Pending Approvals: {$pendingEditSchoolCount}"
    : "No need actions";
$buttonColor = $pendingEditSchoolCount > 0 ? "warning" : "success";
?>
<div class="col d-flex">
    <div class="card <?php echo $cardClass; ?> rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-xl font-weight-bold text-dark">Edit School</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/schools-dashboard/schools-approve-edit/schools-approve-edit.php",
                "sm",
                $buttonColor
            ); ?>
        </div>
    </div>
</div>
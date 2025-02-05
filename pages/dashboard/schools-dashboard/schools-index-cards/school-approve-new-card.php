<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/button.php';
$query =
    "SELECT COUNT(*) AS pending_count FROM schools WHERE approved IS NULL OR approved != 1;";
$result = $connection->query($query);
$pendingSchoolCount = 0;
if ($result && ($row = $result->fetch_assoc())) {
    $pendingSchoolCount = $row["pending_count"];
}

// Set card color based on pending approvals count
// Red if pending, Green if no pending
$cardClass = $pendingSchoolCount > 0 ? "bg-danger" : "bg-success";
$buttonTitle =
    $pendingSchoolCount > 0
    ? "Pending Approvals: {$pendingSchoolCount}"
    : "No need actions";
$buttonColor = $pendingSchoolCount > 0 ? "warning" : "success";
?>
<div class="col d-flex">
    <div class="card <?php echo $cardClass; ?> rounded-lg shadow p-4 w-100 d-flex flex-column justify-content-center align-items-center">
        <h5 class="card-title text-xl font-weight-bold text-dark">Approve new School</h5>
        <div class="mt-auto">
            <?php renderButton(
                $buttonTitle,
                "pages/dashboard/schools-dashboard/schools-approve-new/schools-approve-new.php",
                "sm",
                $buttonColor
            ); ?>
        </div>
    </div>
</div>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$id_vpo = isset($_GET['id_vpo']) ? intval($_GET['id_vpo']) : 0;

if (!$id_vpo) {
    echo '<h4 class="text-center text-white">Invalid university ID.</h4>';
    exit();
}

$queryVerification = "SELECT *, user_id FROM vpo_verification WHERE id_vpo = ?";
$stmtVerification = $connection->prepare($queryVerification);
$stmtVerification->bind_param("i", $id_vpo);
$stmtVerification->execute();
$resultVerification = $stmtVerification->get_result();

$rowVerification = $resultVerification->fetch_assoc();
if (!$rowVerification) {
    echo '<h4 class="text-center text-white">Verification data not found.</h4>';
    exit();
}

// Fetch corresponding university data
$queryUniversities = "SELECT * FROM vpo WHERE id_vpo = ?";
$stmtUniversities = $connection->prepare($queryUniversities);
$stmtUniversities->bind_param("i", $id_vpo);
$stmtUniversities->execute();
$resultUniversities = $stmtUniversities->get_result();

$rowUniversities = $resultUniversities->fetch_assoc();
?>

<h3 class="text-center text-white mb-3">Review Verification Request</h3>

<form action="/pages/dashboard/admin-universities-verification/admin-universities-verification-process.php" method="post">
    <div class="container mt-4">
        <table class="table table-hover table-sm table-bordered align-middle text-center" style="font-size: 13px;">
            <thead class="table-primary">
                <tr>
                    <th>Column Name</th>
                    <th>New Value</th>
                    <th>Current Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($rowVerification as $key => $newValue) {
                    $oldValue = isset($rowUniversities[$key]) ? $rowUniversities[$key] : '';
                    $rowClass = $newValue != $oldValue ? 'table-warning' : '';

                    // Special handling for URLs
                    if ($key === 'vpo_url') {
                        $newValue = htmlspecialchars($newValue);
                        $oldValue = '<a href="' . htmlspecialchars($oldValue) . '" target="_blank">' . htmlspecialchars($oldValue) . '</a>';
                    } else {
                        $newValue = htmlspecialchars($newValue);
                        $oldValue = htmlspecialchars($oldValue);
                    }
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($key) . '</td>';
                    echo '<td class="' . $rowClass . '">' . $newValue . '</td>';
                    echo '<td class="' . $rowClass . '">' . $oldValue . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <input type="hidden" name="id_vpo" value="<?php echo $id_vpo; ?>">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

    <div class="text-center mt-4">
        <button type="submit" name="action" value="accept" class="btn btn-success">Accept</button>
        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
    </div>
</form>
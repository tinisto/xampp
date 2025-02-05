<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";


$id_spo = isset($_GET['id_spo']) ? intval($_GET['id_spo']) : 0;

if (!$id_spo) {
    echo '<h4 class="text-center text-white">Invalid college ID.</h4>';
    exit();
}

$queryVerification = "SELECT *, user_id FROM spo_verification WHERE id_spo = ?";
$stmtVerification = $connection->prepare($queryVerification);
$stmtVerification->bind_param("i", $id_spo);
$stmtVerification->execute();
$resultVerification = $stmtVerification->get_result();

$rowVerification = $resultVerification->fetch_assoc();
if (!$rowVerification) {
    echo '<h4 class="text-center text-white">Verification data not found.</h4>';
    exit();
}

// Fetch corresponding college data
$queryspo = "SELECT * FROM spo WHERE id_spo = ?";
$stmtspo = $connection->prepare($queryspo);
$stmtspo->bind_param("i", $id_spo);
$stmtspo->execute();
$resultspo = $stmtspo->get_result();

$rowspo = $resultspo->fetch_assoc();
?>

<h3 class="text-center text-white mb-3">Review Verification Request</h3>

<form action="/pages/dashboard/admin-spo-verification/admin-spo-verification-process.php" method="post">
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
                    $oldValue = isset($rowspo[$key]) ? $rowspo[$key] : '';
                    $rowClass = $newValue != $oldValue ? 'table-warning' : '';

                    // Special handling for URLs
                    if ($key === 'spo_url') {
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

    <input type="hidden" name="id_spo" value="<?php echo $id_spo; ?>">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

    <div class="text-center mt-4">
        <button type="submit" name="action" value="accept" class="btn btn-success">Accept</button>
        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
    </div>
</form>
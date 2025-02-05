<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

ensureAdminAuthenticated();


$id_school = isset($_GET['id_school']) ? intval($_GET['id_school']) : 0;

if (!$id_school) {
    echo '<h4 class="text-center text-white">Invalid university ID.</h4>';
    exit();
}

$queryVerification = "SELECT *, user_id FROM schools_verification WHERE id_school = ?";
$stmtVerification = $connection->prepare($queryVerification);
$stmtVerification->bind_param("i", $id_school);
$stmtVerification->execute();
$resultVerification = $stmtVerification->get_result();

$rowVerification = $resultVerification->fetch_assoc();
if (!$rowVerification) {
    echo '<h4 class="text-center text-white">Verification data not found.</h4>';
    exit();
}

// Fetch corresponding university data
$querySchools = "SELECT * FROM schools WHERE id_school = ?";
$stmtSchools = $connection->prepare($querySchools);
$stmtSchools->bind_param("i", $id_school);
$stmtSchools->execute();
$resultSchools = $stmtSchools->get_result();

$rowSchools = $resultSchools->fetch_assoc();
?>

<h3 class="text-center text-white mb-3">Review Verification Request</h3>

<form action="/pages/dashboard/admin-schools-verification/admin-schools-verification-process.php" method="post">
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
                    $oldValue = isset($rowSchools[$key]) ? $rowSchools[$key] : '';
                    $rowClass = $newValue != $oldValue ? 'table-warning' : '';

                    // Special handling for URLs
                    if ($key === 'id_school') {
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

    <input type="hidden" name="id_school" value="<?php echo $id_school; ?>">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

    <div class="text-center mt-4">
        <button type="submit" name="action" value="accept" class="btn btn-success">Accept</button>
        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
    </div>
</form>
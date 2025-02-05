<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

$queryVerification = "SELECT * FROM spo_verification";
$resultVerification = $connection->query($queryVerification);

if ($resultVerification && $resultVerification->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered text-center" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>college name</th>';
    echo '<th>Editor Email</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($rowVerification = $resultVerification->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($rowVerification['id_spo']) . '</td>';
        echo '<td>' . htmlspecialchars($rowVerification['spo_name']) . '</td>';
        echo '<td>' . htmlspecialchars($rowVerification['user_id']) . '</td>';
        echo '<td>
            <a href="/dashboard/admin-spo-verification-form.php?id_spo=' . $rowVerification['id_spo'] . '" class="btn btn-primary btn-sm">Review</a>
            </td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<h4 class="text-center text-white">No pending verification requests.</h4>';
}
?>
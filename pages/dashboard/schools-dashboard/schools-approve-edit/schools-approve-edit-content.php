<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

ensureAdminAuthenticated();


// Query to select data from schools_verification
$queryVerification = "SELECT * FROM schools_verification";
$resultVerification = $connection->query($queryVerification);

if ($resultVerification && $resultVerification->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered text-center" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>School Name</th>';
    echo '<th>Editor Email</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($rowVerification = $resultVerification->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($rowVerification['id_school']) . '</td>';
        echo '<td>' . htmlspecialchars($rowVerification['school_name']) . '</td>';
        echo '<td>' . htmlspecialchars($rowVerification['user_id']) . '</td>';
        echo '<td class="text-center">';
        echo '<a href="pages/dashboard/schools-dashboard/schools-approve-edit/schools-approve-edit-form.php?id_school=' . $rowVerification['id_school'] . '" class="btn btn-primary btn-sm">Review</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<h4 class="text-center text-white">No pending verification requests.</h4>';
}
?>
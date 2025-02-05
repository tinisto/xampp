<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$queryVerification = "SELECT * FROM spo_verification";

$resultVerification = $connection->query($queryVerification);

// Check if there is at least one row
if ($rowVerification = $resultVerification->fetch_assoc()) {
    // Find corresponding row in the spo table
    $id_spo = $rowVerification["id_spo"];

    // Select data from spo
    $queryspo = "SELECT * FROM spo WHERE id_spo = ?";
    $stmtSelectspo = mysqli_prepare($connection, $queryspo);
    mysqli_stmt_bind_param($stmtSelectspo, "i", $id_spo);
    mysqli_stmt_execute($stmtSelectspo);
    $resultspo = mysqli_stmt_get_result($stmtSelectspo);
    $rowspo = mysqli_fetch_assoc($resultspo);
}

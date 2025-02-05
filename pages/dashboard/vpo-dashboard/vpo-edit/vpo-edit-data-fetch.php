<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

// Select the first row FROM vpo_verification
$queryVerification = "SELECT * FROM vpo_verification";

$resultVerification = $connection->query($queryVerification);

// Check if there is at least one row
if ($rowVerification = $resultVerification->fetch_assoc()) {
    // Find corresponding row in the universities table
    $id_vpo = $rowVerification["id_vpo"];

    // Select data FROM vpo
    $queryUniversities = "SELECT * FROM vpo WHERE id_vpo = ?";
    $stmtSelectUniversities = mysqli_prepare($connection, $queryUniversities);
    mysqli_stmt_bind_param($stmtSelectUniversities, "i", $id_vpo);
    mysqli_stmt_execute($stmtSelectUniversities);
    $resultUniversities = mysqli_stmt_get_result($stmtSelectUniversities);
    $rowUniversities = mysqli_fetch_assoc($resultUniversities);
}

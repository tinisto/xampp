<?php
ensureAdminAuthenticated();

// Select the first row from schools_verification
$queryVerification = "SELECT * FROM schools_verification";

$resultVerification = $connection->query($queryVerification);

// Check if there is at least one row
if ($rowVerification = $resultVerification->fetch_assoc()) {
    // Find corresponding row in the schools table
    $id_school = $rowVerification["id_school"];

    // Select data from schools
    $querySchools = "SELECT * FROM schools WHERE id_school = ?";
    $stmtSelectSchools = mysqli_prepare($connection, $querySchools);
    mysqli_stmt_bind_param($stmtSelectSchools, "i", $id_school);
    mysqli_stmt_execute($stmtSelectSchools);
    $resultSchools = mysqli_stmt_get_result($stmtSelectSchools);
    $rowSchools = mysqli_fetch_assoc($resultSchools);
}

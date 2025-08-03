<?php
ensureAdminAuthenticated();

// Select the first row from schools_verification
$queryVerification = "SELECT * FROM schools_verification";

$resultVerification = $connection->query($queryVerification);

// Check if query was successful and has results
if ($resultVerification === false) {
    // Query failed - log error and set empty state
    error_log("Dashboard error: schools_verification query failed - " . $connection->error);
    $rowVerification = null;
    $rowSchools = null;
} elseif ($rowVerification = $resultVerification->fetch_assoc()) {
    // Find corresponding row in the schools table
    $id_school = $rowVerification["id_school"];

    // Select data from schools
    $querySchools = "SELECT * FROM schools WHERE id_school = ?";
    $stmtSelectSchools = mysqli_prepare($connection, $querySchools);
    
    if ($stmtSelectSchools) {
        mysqli_stmt_bind_param($stmtSelectSchools, "i", $id_school);
        mysqli_stmt_execute($stmtSelectSchools);
        $resultSchools = mysqli_stmt_get_result($stmtSelectSchools);
        $rowSchools = mysqli_fetch_assoc($resultSchools);
        mysqli_stmt_close($stmtSelectSchools);
    } else {
        error_log("Dashboard error: schools query prepare failed - " . $connection->error);
        $rowSchools = null;
    }
} else {
    // No rows in schools_verification
    $rowVerification = null;
    $rowSchools = null;
}
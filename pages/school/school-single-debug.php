<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Debug: Starting school page<br>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "Debug: Session started<br>";
}

echo "Debug: About to include database connection<br>";

// Try to establish database connection
$dbFile = $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
if (file_exists($dbFile)) {
    echo "Debug: Database file exists<br>";
    require_once $dbFile;
    echo "Debug: Database file included<br>";
    
    // Check if connection exists
    if (isset($connection)) {
        echo "Debug: Database connection exists<br>";
    } else {
        echo "Debug: ERROR - Database connection variable not set<br>";
    }
} else {
    echo "Debug: ERROR - Database file not found at: " . $dbFile . "<br>";
}

echo "Debug: About to include extract-school-id.php<br>";

// Include school ID extraction
include $_SERVER['DOCUMENT_ROOT'] . '/pages/school/extract-school-id.php';

echo "Debug: School ID extracted: " . (isset($id_school) ? $id_school : 'NOT SET') . "<br>";

if (!isset($id_school) || !is_numeric($id_school)) {
    echo "Debug: Invalid school ID, would redirect to 404<br>";
    exit();
}

echo "Debug: School ID is valid: " . $id_school . "<br>";

// Test database query
if (isset($connection)) {
    echo "Debug: Testing database query<br>";
    $testQuery = "SELECT COUNT(*) as count FROM schools WHERE id_school = ?";
    $stmt = mysqli_prepare($connection, $testQuery);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_school);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $count = mysqli_fetch_assoc($result)['count'];
            echo "Debug: School found in database: " . ($count > 0 ? 'YES' : 'NO') . "<br>";
        } else {
            echo "Debug: ERROR executing query: " . mysqli_error($connection) . "<br>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Debug: ERROR preparing query: " . mysqli_error($connection) . "<br>";
    }
} else {
    echo "Debug: ERROR - No database connection available<br>";
}

echo "<br>Debug completed. If you see this, the basic functionality is working.<br>";
?>
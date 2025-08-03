<?php
// Absolute bare minimum school page
session_start();

// Get school ID
$id_school = isset($_GET['id_school']) ? $_GET['id_school'] : null;

if (!$id_school || !is_numeric($id_school)) {
    die("No valid school ID provided");
}

// Direct database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// Set charset to UTF-8
$connection->set_charset("utf8mb4");

// Get school data
$query = "SELECT * FROM schools WHERE id_school = ? AND approved = '1'";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_school);
$stmt->execute();
$result = $stmt->get_result();
$school = $result->fetch_assoc();

if (!$school) {
    die("School not found");
}

// Just output the data
echo "<h1>School Found!</h1>";
echo "<p>ID: " . $school['id_school'] . "</p>";
echo "<p>Name: " . htmlspecialchars($school['school_name']) . "</p>";
echo "<p>This proves the basic functionality works.</p>";
echo "<a href='/'>Home</a>";
?>
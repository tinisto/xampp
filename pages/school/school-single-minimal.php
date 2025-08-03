<?php
// Minimal school page - bypass all the complex includes
session_start();

// Get school ID
$id_school = isset($_GET['id_school']) ? $_GET['id_school'] : null;

if (!$id_school) {
    // Try to get from URL path
    $path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    if (count($path) >= 2 && $path[0] === 'school') {
        $id_school = $path[1];
    }
}

if (!$id_school || !is_numeric($id_school)) {
    header("Location: /404");
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

// Get school data
$query = "SELECT * FROM schools WHERE id_school = ? AND approved = '1'";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id_school);
$stmt->execute();
$result = $stmt->get_result();
$school = $result->fetch_assoc();

if (!$school) {
    header("Location: /404");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($school['school_name']) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($school['school_name']) ?></h1>
    <p>School ID: <?= $id_school ?></p>
    <p>This is a minimal working school page.</p>
    <a href="/">Back to home</a>
</body>
</html>
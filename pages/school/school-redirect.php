<?php
// School ID to slug redirect handler
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

try {
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            header("Location: /404");
            exit();
        }
        
        $connection->set_charset("utf8mb4");
    } else {
        header("Location: /404");
        exit();
    }
} catch (Exception $e) {
    header("Location: /404");
    exit();
}

// Get school ID from URL parameter
$school_id = $_GET['id_school'] ?? null;

if (!$school_id || !is_numeric($school_id)) {
    header("Location: /404");
    exit();
}

// Look up the slug for this school ID
$school_id_int = intval($school_id);
$query = "SELECT url_slug FROM schools WHERE id = ? AND url_slug IS NOT NULL AND url_slug != ''";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $school_id_int);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // School not found or no slug - redirect to 404
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$slug = $row['url_slug'];

// 301 permanent redirect to the friendly URL
header("Location: /school/{$slug}", true, 301);
exit();
?>
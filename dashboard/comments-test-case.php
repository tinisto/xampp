<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once dirname(__DIR__) . '/config/loadEnv.php';
require_once dirname(__DIR__) . '/database/db_connections.php';

echo "Testing CASE statement...<br><br>";

// Test simple query first
$sql1 = "SELECT COUNT(*) as count FROM comments";
$result1 = $connection->query($sql1);
echo "Simple query test: " . ($result1 ? "✅ SUCCESS" : "❌ FAILED: " . $connection->error) . "<br>";

// Test with CASE
$sql2 = "SELECT c.*, u.first_name, u.last_name, u.email,
        CASE 
            WHEN c.entity_type = 'post' THEN (SELECT url_post FROM posts WHERE id = c.id_entity)
            WHEN c.entity_type = 'news' THEN (SELECT url_news FROM news WHERE id = c.id_entity)
            ELSE NULL
        END as entity_url
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.date DESC 
        LIMIT 5";

echo "<br>Testing CASE query...<br>";
$result2 = $connection->query($sql2);

if ($result2) {
    echo "✅ CASE query SUCCESS<br><br>";
    echo "Results:<br>";
    while ($row = $result2->fetch_assoc()) {
        echo "Comment ID: " . $row['id'] . ", Entity: " . $row['entity_type'] . ", Entity URL: " . ($row['entity_url'] ?? 'NULL') . "<br>";
    }
} else {
    echo "❌ CASE query FAILED: " . $connection->error . "<br>";
}

// Test without CASE
echo "<br><br>Testing without CASE...<br>";
$sql3 = "SELECT c.*, u.first_name, u.last_name, u.email
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.date DESC 
        LIMIT 5";

$result3 = $connection->query($sql3);
if ($result3) {
    echo "✅ Simple join query SUCCESS<br>";
} else {
    echo "❌ Simple join query FAILED: " . $connection->error . "<br>";
}
?>
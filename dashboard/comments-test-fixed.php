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

echo "Testing fixed CASE query...<br><br>";

$sql = "SELECT c.*, u.first_name, u.last_name, u.email,
        CASE 
            WHEN c.entity_type = 'post' THEN (SELECT p.url_post FROM posts p WHERE p.id = c.id_entity)
            WHEN c.entity_type = 'news' THEN (SELECT n.url_news FROM news n WHERE n.id = c.id_entity)
            WHEN c.entity_type = 'vpo' OR c.entity_type = 'university' THEN (SELECT v.vpo_url FROM vpo v WHERE v.id = c.id_entity)
            WHEN c.entity_type = 'spo' OR c.entity_type = 'college' THEN (SELECT s.spo_url FROM spo s WHERE s.id = c.id_entity)
            ELSE NULL
        END as entity_url
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.date DESC 
        LIMIT 5";

$result = $connection->query($sql);

if ($result) {
    echo "✅ Query SUCCESS!<br><br>";
    echo "Results:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Comment ID: " . $row['id'] . "<br>";
        echo "Entity Type: " . $row['entity_type'] . "<br>";
        echo "Entity ID: " . $row['id_entity'] . "<br>";
        echo "Entity URL: " . ($row['entity_url'] ?? 'NULL') . "<br>";
        echo "---<br>";
    }
} else {
    echo "❌ Query FAILED: " . $connection->error . "<br>";
}

// Also test which tables exist
echo "<br><br>Checking if tables exist:<br>";
$tables = ['posts', 'news', 'vpo', 'spo', 'universities', 'colleges'];
foreach ($tables as $table) {
    $check = $connection->query("SHOW TABLES LIKE '$table'");
    echo "$table: " . ($check->num_rows > 0 ? "✅ EXISTS" : "❌ NOT FOUND") . "<br>";
}
?>
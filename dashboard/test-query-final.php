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

echo "Testing final query with correct column names...<br><br>";

$sql = "SELECT c.*, u.first_name, u.last_name, u.email,
        CASE 
            WHEN c.entity_type = 'post' THEN (SELECT p.url_post FROM posts p WHERE p.id_post = c.id_entity)
            WHEN c.entity_type = 'news' THEN (SELECT n.url_news FROM news n WHERE n.id_news = c.id_entity)
            WHEN c.entity_type = 'vpo' OR c.entity_type = 'university' THEN (SELECT v.vpo_url FROM vpo v WHERE v.id_vpo = c.id_entity)
            WHEN c.entity_type = 'spo' OR c.entity_type = 'college' THEN (SELECT s.spo_url FROM spo s WHERE s.id_spo = c.id_entity)
            ELSE NULL
        END as entity_url,
        CASE 
            WHEN c.entity_type = 'post' THEN (SELECT p.title_post FROM posts p WHERE p.id_post = c.id_entity)
            WHEN c.entity_type = 'news' THEN (SELECT n.title_news FROM news n WHERE n.id_news = c.id_entity)
            WHEN c.entity_type = 'vpo' OR c.entity_type = 'university' THEN (SELECT v.vpo_name FROM vpo v WHERE v.id_vpo = c.id_entity)
            WHEN c.entity_type = 'spo' OR c.entity_type = 'college' THEN (SELECT s.spo_name FROM spo s WHERE s.id_spo = c.id_entity)
            WHEN c.entity_type = 'school' THEN (SELECT sc.school_name FROM schools sc WHERE sc.id = c.id_entity)
            ELSE NULL
        END as entity_title
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.date DESC 
        LIMIT 5";

echo "Executing query...<br>";

$result = $connection->query($sql);

if ($result) {
    echo "✅ Query SUCCESS!<br><br>";
    echo "Results:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Comment ID: " . $row['id'] . "<br>";
        echo "Entity Type: " . $row['entity_type'] . "<br>";
        echo "Entity ID: " . $row['id_entity'] . "<br>";
        echo "Entity URL: " . ($row['entity_url'] ?? 'NULL') . "<br>";
        echo "Entity Title: " . ($row['entity_title'] ?? 'NULL') . "<br>";
        echo "---<br>";
    }
} else {
    echo "❌ Query FAILED: " . $connection->error . "<br>";
    
    // Try a simpler version
    echo "<br><br>Trying simpler version without entity_title...<br>";
    
    $sql2 = "SELECT c.*, u.first_name, u.last_name, u.email,
            CASE 
                WHEN c.entity_type = 'post' THEN (SELECT p.url_post FROM posts p WHERE p.id_post = c.id_entity)
                WHEN c.entity_type = 'news' THEN (SELECT n.url_news FROM news n WHERE n.id_news = c.id_entity)
                ELSE NULL
            END as entity_url
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            ORDER BY c.date DESC 
            LIMIT 5";
    
    $result2 = $connection->query($sql2);
    
    if ($result2) {
        echo "✅ Simpler query works!<br>";
    } else {
        echo "❌ Simpler query also failed: " . $connection->error . "<br>";
    }
}
?>
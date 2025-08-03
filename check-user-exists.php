<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$email = $_GET['email'] ?? '11klassniki.ru@gmail.com';

echo "<h2>User Check: $email</h2>";
echo "<pre>";

if (!$connection) {
    die("❌ Database connection failed\n");
}

// Check if user exists
$stmt = $connection->prepare("SELECT id, firstname, email FROM users WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "✅ USER FOUND:\n";
        echo "  ID: {$user['id']}\n";
        echo "  Name: {$user['firstname']}\n";
        echo "  Email: {$user['email']}\n";
    } else {
        echo "❌ NO USER FOUND with email: $email\n";
        echo "\nLet's check what users exist...\n";
        
        // Show first 5 users for reference
        $allUsers = $connection->query("SELECT id, firstname, email FROM users LIMIT 5");
        if ($allUsers && $allUsers->num_rows > 0) {
            echo "\n📋 Sample users in database:\n";
            while ($row = $allUsers->fetch_assoc()) {
                echo "  ID: {$row['id']}, Name: {$row['firstname']}, Email: {$row['email']}\n";
            }
        }
    }
    $stmt->close();
} else {
    echo "❌ Failed to prepare statement\n";
}

echo "</pre>";
?>
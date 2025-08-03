<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Database Structure Check</h2>";
echo "<pre>";

if (!$connection) {
    die("❌ Database connection failed\n");
}

// Check what tables exist
echo "=== Available Tables ===\n";
$tables = $connection->query("SHOW TABLES");
if ($tables) {
    while ($row = $tables->fetch_array()) {
        echo "  📋 {$row[0]}\n";
    }
} else {
    echo "❌ Failed to get tables: " . $connection->error . "\n";
}

// Check users table structure
echo "\n=== Users Table Structure ===\n";
$userTableDesc = $connection->query("DESCRIBE users");
if ($userTableDesc) {
    while ($row = $userTableDesc->fetch_assoc()) {
        echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
} else {
    echo "❌ Failed to describe users table: " . $connection->error . "\n";
}

// Try different approaches to find user
$email = $_GET['email'] ?? '11klassniki.ru@gmail.com';
echo "\n=== Finding User: $email ===\n";

// Method 1: Direct query
$directQuery = $connection->query("SELECT id, firstname, email FROM users WHERE email = '$email' LIMIT 1");
if ($directQuery) {
    if ($directQuery->num_rows > 0) {
        $user = $directQuery->fetch_assoc();
        echo "✅ FOUND USER (direct query):\n";
        echo "  ID: {$user['id']}\n";
        echo "  Name: {$user['firstname']}\n";
        echo "  Email: {$user['email']}\n";
    } else {
        echo "❌ No user found with direct query\n";
    }
} else {
    echo "❌ Direct query failed: " . $connection->error . "\n";
}

// Method 2: Check for similar emails
echo "\n=== Similar Emails ===\n";
$similarEmails = $connection->query("SELECT id, firstname, email FROM users WHERE email LIKE '%gmail.com%' LIMIT 3");
if ($similarEmails && $similarEmails->num_rows > 0) {
    while ($row = $similarEmails->fetch_assoc()) {
        echo "  ID: {$row['id']}, Name: {$row['firstname']}, Email: {$row['email']}\n";
    }
} else {
    echo "No Gmail users found\n";
}

echo "</pre>";
?>
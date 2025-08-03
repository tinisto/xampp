<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'create123') {
    die('Access denied');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Creating password_resets Table</h2>";
echo "<pre>";

if (!$connection) {
    die("❌ Database connection failed\n");
}

// Create the password_resets table
$createTableQuery = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used BOOLEAN DEFAULT FALSE,
    INDEX idx_user_id (user_id),
    INDEX idx_token (token),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($connection->query($createTableQuery)) {
    echo "✅ password_resets table created successfully\n";
    
    // Verify table exists
    $tableCheck = $connection->query("SHOW TABLES LIKE 'password_resets'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "✅ Table verified - password_resets exists\n";
        
        // Show table structure
        $descResult = $connection->query("DESCRIBE password_resets");
        echo "\n📋 Table Structure:\n";
        while ($row = $descResult->fetch_assoc()) {
            echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
        }
    } else {
        echo "❌ Table verification failed\n";
    }
} else {
    echo "❌ Error creating table: " . $connection->error . "\n";
}

echo "\nNow try the password reset again!";
echo "</pre>";
?>
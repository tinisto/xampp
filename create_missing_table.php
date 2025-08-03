<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

echo "<h2>Create Missing Dashboard Tables</h2>";

// Create schools_verification table
$createTableSQL = "
CREATE TABLE IF NOT EXISTS schools_verification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_school INT NOT NULL,
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submitted_by INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (id_school) REFERENCES schools(id_school) ON DELETE CASCADE,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
)";

$result = $connection->query($createTableSQL);

if ($result) {
    echo "<p style='color: green;'>✅ schools_verification table created successfully!</p>";
    
    // Add a few sample records for testing
    $sampleData = "
    INSERT IGNORE INTO schools_verification (id_school, verification_status, notes) 
    SELECT id_school, 'pending', 'Sample verification request' 
    FROM schools 
    LIMIT 3";
    
    $connection->query($sampleData);
    echo "<p style='color: blue;'>📝 Added sample verification requests</p>";
    
} else {
    echo "<p style='color: red;'>❌ Failed to create table: " . htmlspecialchars($connection->error) . "</p>";
}

// Check if table now exists
$checkTable = "SELECT COUNT(*) as count FROM schools_verification";
$result = $connection->query($checkTable);

if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>📊 schools_verification now has {$row['count']} records</p>";
} else {
    echo "<p style='color: red;'>❌ Still can't access table</p>";
}

echo "<hr>";
echo "<p><a href='/dashboard-debug.php'>🔍 Check Debug Info</a></p>";
echo "<p><a href='/dashboard'>📊 Go to Dashboard</a></p>";
?>
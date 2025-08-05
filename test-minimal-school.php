<?php
// Minimal school template to test basic functionality
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<!DOCTYPE html><html><head><title>Test School</title></head><body>";
echo "<h1>Minimal School Template Test</h1>";

$school_id = $_GET['id'] ?? 2718;
echo "<p>Testing school ID: {$school_id}</p>";

try {
    $query = "SELECT id, name, street FROM schools WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $school_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $school = $result->fetch_assoc();
        echo "<h2>✅ School Found:</h2>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($school['name']) . "</p>";
        echo "<p><strong>Address:</strong> " . htmlspecialchars($school['street'] ?? 'No address') . "</p>";
        echo "<p><strong>ID:</strong> " . $school['id'] . "</p>";
    } else {
        echo "<h2>❌ School Not Found</h2>";
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
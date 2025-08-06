<?php
// Check all institution tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>ALL TABLES STRUCTURE CHECK\n";
echo "===========================\n\n";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$tables = ['regions', 'towns', 'vpo', 'spo', 'schools'];

foreach ($tables as $table) {
    echo strtoupper($table) . " TABLE COLUMNS:\n";
    $query = "SHOW COLUMNS FROM $table";
    $result = $connection->query($query);
    if ($result) {
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
            if (strpos($row['Field'], 'region') !== false || strpos($row['Field'], 'town') !== false || $row['Field'] === 'id' || strpos($row['Field'], 'id_') === 0) {
                echo "- " . $row['Field'] . " (" . $row['Type'] . ") <-- KEY FIELD\n";
            }
        }
        echo "All columns: " . implode(', ', $columns) . "\n";
    } else {
        echo "ERROR: " . $connection->error . "\n";
    }
    echo "\n";
}

// Test queries
echo "\nTEST QUERIES:\n";
echo "=============\n";

// Test VPO count
$region_id = 72; // Amurskaya oblast
echo "VPO in region $region_id: ";
$result = $connection->query("SELECT COUNT(*) as cnt FROM vpo WHERE region_id = $region_id");
if ($result) {
    echo $result->fetch_assoc()['cnt'] . "\n";
} else {
    echo "ERROR: " . $connection->error . "\n";
}

// Test SPO count - try both column names
echo "SPO in region $region_id: ";
$result = $connection->query("SELECT COUNT(*) as cnt FROM spo WHERE region_id = $region_id");
if ($result) {
    echo $result->fetch_assoc()['cnt'] . " (using region_id)\n";
} else {
    // Try id_region
    $result = $connection->query("SELECT COUNT(*) as cnt FROM spo WHERE id_region = $region_id");
    if ($result) {
        echo $result->fetch_assoc()['cnt'] . " (using id_region)\n";
    } else {
        echo "ERROR: " . $connection->error . "\n";
    }
}

// Test schools count - try both column names
echo "Schools in region $region_id: ";
$result = $connection->query("SELECT COUNT(*) as cnt FROM schools WHERE region_id = $region_id");
if ($result) {
    echo $result->fetch_assoc()['cnt'] . " (using region_id)\n";
} else {
    // Try id_region
    $result = $connection->query("SELECT COUNT(*) as cnt FROM schools WHERE id_region = $region_id");
    if ($result) {
        echo $result->fetch_assoc()['cnt'] . " (using id_region)\n";
    } else {
        echo "ERROR: " . $connection->error . "\n";
    }
}

echo "</pre>";
?>
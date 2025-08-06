<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>DEBUG START\n";

// Check if db_connections exists
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php')) {
    echo "ERROR: db_connections.php not found\n";
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "Connected to database\n";

// Handle slug-based URLs only
$url_slug = $_GET['url_slug'] ?? null;

echo "URL slug: " . ($url_slug ?? 'NULL') . "\n";

if (!$url_slug) {
    echo "ERROR: No URL slug provided\n";
    exit();
}

$query = "SELECT s.*, r.region_name, r.region_name_en, t.town_name, t.town_name_en 
          FROM schools s
          LEFT JOIN regions r ON s.region_id = r.region_id
          LEFT JOIN towns t ON s.town_id = t.town_id
          WHERE s.url_slug = ?";

echo "Query: $query\n";
echo "Looking for slug: $url_slug\n";

$stmt = $connection->prepare($query);
if (!$stmt) {
    echo "ERROR preparing statement: " . $connection->error . "\n";
    exit();
}

$stmt->bind_param("s", $url_slug);
$stmt->execute();
$result = $stmt->get_result();

echo "Query executed, rows found: " . $result->num_rows . "\n";

if ($result->num_rows === 0) {
    // Try without joins to see if school exists
    $query2 = "SELECT * FROM schools WHERE url_slug = ?";
    $stmt2 = $connection->prepare($query2);
    $stmt2->bind_param("s", $url_slug);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    echo "Simple query rows: " . $result2->num_rows . "\n";
    
    if ($result2->num_rows > 0) {
        $row = $result2->fetch_assoc();
        echo "School found but JOIN failed\n";
        echo "School data:\n";
        print_r($row);
    } else {
        echo "School not found in database\n";
    }
    exit();
}

$row = $result->fetch_assoc();
echo "School found: " . $row['name'] . "\n";
echo "Full data:\n";
print_r($row);

echo "\nDEBUG END</pre>";
?>
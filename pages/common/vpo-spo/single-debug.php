<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>DEBUG SPO/VPO SINGLE PAGE\n";
echo "========================\n\n";

// Check database connection
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php')) {
    echo "ERROR: db_connections.php not found\n";
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
echo "✓ Database connected\n";

// Get parameters
$url_slug = $_GET['url_slug'] ?? basename($_SERVER['REQUEST_URI']);
$url_slug = preg_replace('/\?.*/', '', $url_slug);
echo "URL slug: $url_slug\n";

// Determine type
$type = $_GET['type'] ?? null;
if (!$type) {
    $requestUri = $_SERVER['REQUEST_URI'];
    $type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
}
echo "Type: $type\n";

// Validate type
if (!in_array($type, ['vpo', 'spo'])) {
    echo "ERROR: Invalid type\n";
    exit();
}

// Test simple query first
$test_query = "SELECT COUNT(*) as cnt FROM $type";
$test_result = $connection->query($test_query);
if ($test_result) {
    $count = $test_result->fetch_assoc()['cnt'];
    echo "Total records in $type table: $count\n";
} else {
    echo "ERROR querying $type table: " . $connection->error . "\n";
}

// Main query
$query = "SELECT i.*, r.region_name, r.region_name_en, t.town_name, t.town_name_en 
          FROM $type i
          LEFT JOIN regions r ON i.region_id = r.region_id
          LEFT JOIN towns t ON i.town_id = t.town_id
          WHERE i.url_slug = ?";
echo "\nMain query: $query\n";
echo "Looking for: $url_slug\n";

$stmt = $connection->prepare($query);
if (!$stmt) {
    echo "ERROR preparing statement: " . $connection->error . "\n";
    exit();
}

$stmt->bind_param("s", $url_slug);
$stmt->execute();
$result = $stmt->get_result();

echo "Rows found: " . $result->num_rows . "\n";

if ($result->num_rows === 0) {
    // Try simpler query
    echo "\nTrying simpler query without joins...\n";
    $simple_query = "SELECT * FROM $type WHERE url_slug = ?";
    $stmt2 = $connection->prepare($simple_query);
    $stmt2->bind_param("s", $url_slug);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    echo "Simple query rows: " . $result2->num_rows . "\n";
    
    if ($result2->num_rows > 0) {
        $row = $result2->fetch_assoc();
        echo "\nRecord found:\n";
        echo "ID: " . $row['id'] . "\n";
        echo "Name: " . $row['name'] . "\n";
        echo "URL slug: " . $row['url_slug'] . "\n";
        echo "Region ID: " . $row['region_id'] . "\n";
        echo "Town ID: " . $row['town_id'] . "\n";
    } else {
        // Check what slugs exist
        echo "\nChecking existing slugs in $type table:\n";
        $check_query = "SELECT url_slug FROM $type WHERE url_slug LIKE ? LIMIT 5";
        $stmt3 = $connection->prepare($check_query);
        $search_pattern = '%' . substr($url_slug, 0, 10) . '%';
        $stmt3->bind_param("s", $search_pattern);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        while ($r = $result3->fetch_assoc()) {
            echo "- " . $r['url_slug'] . "\n";
        }
    }
} else {
    $row = $result->fetch_assoc();
    echo "\nRecord found successfully!\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Region: " . $row['region_name'] . "\n";
    echo "Town: " . $row['town_name'] . "\n";
}

echo "\n</pre>";
?>
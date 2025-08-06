<?php
// Debug version to identify the error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>DEBUG START\n";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters
$region_url_slug = $_GET['region_name_en'] ?? '';
$institution_type = $_GET['type'] ?? 'spo';

echo "URL Parameters:\n";
echo "- region_url_slug: $region_url_slug\n";
echo "- institution_type: $institution_type\n";

// Validate institution type
if (!in_array($institution_type, ['schools', 'spo', 'vpo'])) {
    echo "ERROR: Invalid institution type\n";
    exit();
}

// Get region data using the URL slug
$region_database_id = null;
$region_display_name = '';
$region_url_slug_stored = '';

if (!empty($region_url_slug)) {
    $query = "SELECT id_region, region_name, region_name_en FROM regions WHERE region_name_en = ?";
    echo "\nRegion Query: $query\n";
    echo "Parameter: $region_url_slug\n";
    
    $stmt = $connection->prepare($query);
    if (!$stmt) {
        echo "ERROR preparing statement: " . $connection->error . "\n";
        exit();
    }
    
    $stmt->bind_param("s", $region_url_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $region_database_id = $row['id_region'];
        $region_display_name = $row['region_name'];
        $region_url_slug_stored = $row['region_name_en'];
        echo "Found region: ID=$region_database_id, Name=$region_display_name\n";
    } else {
        echo "ERROR: Region not found\n";
    }
    $stmt->close();
}

if (!$region_database_id) {
    echo "ERROR: No region ID\n";
    exit();
}

// Configure based on type
$config = [
    'vpo' => [
        'title' => 'ВПО в регионе ' . $region_display_name,
        'region_column' => 'region_id',
        'name_column' => 'name',
        'id_column' => 'id',
        'url_prefix' => 'vpo',
        'address_column' => 'street',
        'phone_column' => 'tel',
        'website_column' => 'site'
    ],
    'spo' => [
        'title' => 'СПО в регионе ' . $region_display_name,
        'region_column' => 'id_region',
        'name_column' => 'spo_name',
        'id_column' => 'id_spo',
        'url_prefix' => 'spo',
        'address_column' => 'spo_address',
        'phone_column' => 'spo_phone',
        'website_column' => 'spo_site'
    ],
    'schools' => [
        'title' => 'Школы в регионе ' . $region_display_name,
        'region_column' => 'id_region',
        'name_column' => 'school_name',
        'id_column' => 'id_school',
        'url_prefix' => 'school',
        'address_column' => 'school_address',
        'phone_column' => 'school_phone',
        'website_column' => 'school_site'
    ]
];

$typeConfig = $config[$institution_type];
echo "\nUsing config for: $institution_type\n";
echo "Region column: {$typeConfig['region_column']}\n";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM $institution_type WHERE {$typeConfig['region_column']} = ?";
echo "\nCount Query: $count_query\n";
echo "Parameter: $region_database_id\n";

$stmt = $connection->prepare($count_query);
if (!$stmt) {
    echo "ERROR preparing count statement: " . $connection->error . "\n";
    exit();
}

$stmt->bind_param("i", $region_database_id);
if (!$stmt->execute()) {
    echo "ERROR executing count query: " . $stmt->error . "\n";
    exit();
}

$result = $stmt->get_result();
$total_institutions = $result->fetch_assoc()['total'];
echo "Total institutions: $total_institutions\n";
$stmt->close();

echo "\nDEBUG END - No errors found\n";
echo "</pre>";
?>
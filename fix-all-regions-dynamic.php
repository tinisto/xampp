<?php
// Dynamic fix for all-regions pages that adapts to actual database structure
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

// Check if constants are defined
if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
    die("Database configuration not found");
}

// Create connection
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

// Function to get the actual column names from regions table
function getRegionsColumns($connection) {
    $columns = [];
    $result = $connection->query("SHOW COLUMNS FROM regions");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    return $columns;
}

// Get actual column names
$regionColumns = getRegionsColumns($connection);

// Determine the ID column name
$regionIdColumn = 'id_region'; // default
if (in_array('id', $regionColumns)) {
    $regionIdColumn = 'id';
} elseif (in_array('region_id', $regionColumns)) {
    $regionIdColumn = 'region_id';
}

// Determine the country column name
$countryColumn = 'id_country'; // default
if (in_array('country_id', $regionColumns)) {
    $countryColumn = 'country_id';
}

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $countField = 'spo_count';
        $linkPrefix = '/spo-in-region';
        $pageTitle = 'СПО по регионам';
        $regionColumn = 'region_id';
        break;
    case 'vpo':
        $table = 'vpo';
        $countField = 'vpo_count';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = 'ВПО по регионам';
        $regionColumn = 'region_id';
        break;
    default: // schools
        $table = 'schools';
        $countField = 'school_count';
        $linkPrefix = '/schools-in-region';
        $pageTitle = 'Школы по регионам';
        $regionColumn = 'region_id';
        break;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; margin-bottom: 30px; }
        .regions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .region { background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef; }
        .region h6 { margin: 0; font-size: 16px; }
        .region a { color: #0066cc; text-decoration: none; }
        .region a:hover { text-decoration: underline; }
        .badge { background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        
        <div class="info">
            <p>Debug info: Regions table ID column is: <strong><?php echo $regionIdColumn; ?></strong></p>
            <p>Debug info: Regions table country column is: <strong><?php echo $countryColumn; ?></strong></p>
            <p>All columns: <?php echo implode(', ', $regionColumns); ?></p>
        </div>
        
        <div class="regions-grid">
            <?php 
            // Build the query with the correct column name
            $sql = "SELECT $regionIdColumn, region_name, region_name_en FROM regions WHERE $countryColumn = 1 ORDER BY region_name ASC";
            $result = $connection->query($sql);
            
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): 
                    // Use the dynamic column name
                    $regionId = $row[$regionIdColumn];
                    
                    // Count institutions in this region
                    $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE $regionColumn = $regionId";
                    $count_result = $connection->query($count_sql);
                    
                    if ($count_result) {
                        $count_row = $count_result->fetch_assoc();
                        $institution_count = $count_row['count'];
                        
                        if ($institution_count > 0):
            ?>
                <div class="region">
                    <h6>
                        <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>">
                            <?= htmlspecialchars($row['region_name']) ?>
                        </a>
                        <span class="badge"><?= $institution_count ?></span>
                    </h6>
                </div>
            <?php 
                        endif;
                    }
                endwhile;
            else:
            ?>
                <div class="error">
                    <p>Error loading regions: <?php echo $connection->error; ?></p>
                    <p>Query was: <?php echo htmlspecialchars($sql); ?></p>
                </div>
            <?php
            endif;
            ?>
        </div>
    </div>
</body>
</html>
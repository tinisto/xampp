<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Full Debug for School Page</h2>";
echo "<pre>";

try {
    echo "Step 1: Checking environment...\n";
    echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
    echo "GET params: ";
    print_r($_GET);
    echo "\n";

    echo "\nStep 2: Checking file existence...\n";
    $files_to_check = [
        '/common-components/check_under_construction.php',
        '/config/loadEnv.php',
        '/database/db_connections.php',
        '/pages/school/school-single-data-fetch.php',
        '/pages/school/extract-school-id.php',
        '/common-components/template-engine.php'
    ];
    
    foreach ($files_to_check as $file) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
        echo $file . ": " . (file_exists($fullPath) ? "EXISTS" : "NOT FOUND") . "\n";
    }

    echo "\nStep 3: Testing includes one by one...\n";
    
    // Test check_under_construction.php
    echo "Including check_under_construction.php...\n";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
    echo "✓ check_under_construction.php included successfully\n";
    
    // Check if we have database connection
    if (isset($connection)) {
        echo "✓ Database connection exists from check_under_construction.php\n";
    } else {
        echo "✗ No database connection from check_under_construction.php\n";
    }

    echo "\nStep 4: Simulating school-single.php flow...\n";
    
    // Include school-single-data-fetch.php
    echo "Including school-single-data-fetch.php...\n";
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/school/school-single-data-fetch.php';
    
    // Check what variables we have
    echo "\nVariables after data fetch:\n";
    echo "id_school: " . (isset($id_school) ? $id_school : "NOT SET") . "\n";
    echo "pageTitle: " . (isset($pageTitle) ? $pageTitle : "NOT SET") . "\n";
    echo "row: " . (isset($row) ? "SET (school data loaded)" : "NOT SET") . "\n";
    
    echo "\nStep 5: Would include template-engine.php next...\n";
    echo "mainContent would be: pages/school/school-single-content-modern.php\n";
    
    echo "\nDebug completed successfully!\n";
    
} catch (Exception $e) {
    echo "\nERROR CAUGHT: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "\nFATAL ERROR CAUGHT: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
?>
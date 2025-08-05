<?php
// Debug version of school template to find the error
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>Debug School Template</title></head><body>";
echo "<h1>School Template Debug</h1>";

try {
    echo "<p>Step 1: Starting...</p>";
    
    // Test the under construction check
    echo "<p>Step 2: Testing under construction check...</p>";
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
        echo "<p>✅ Under construction check loaded</p>";
    } else {
        echo "<p>⚠️ Under construction check file not found</p>";
    }
    
    echo "<p>Step 3: Testing database connection...</p>";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    echo "<p>✅ Environment loaded</p>";
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            throw new Exception("Connection failed: " . $connection->connect_error);
        }
        
        $connection->set_charset("utf8mb4");
        echo "<p>✅ Database connected</p>";
    } else {
        throw new Exception("Database constants not defined");
    }
    
    echo "<p>Step 4: Testing URL parameters...</p>";
    $url_slug = $_GET['url_slug'] ?? null;
    $school_id = $_GET['id_school'] ?? null;
    
    echo "<p>url_slug: " . ($url_slug ?: 'null') . "</p>";
    echo "<p>id_school: " . ($school_id ?: 'null') . "</p>";
    
    if ($url_slug) {
        $query = "SELECT * FROM schools WHERE url_slug = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $url_slug);
        echo "<p>Using slug query</p>";
    } elseif ($school_id && intval($school_id) > 0) {
        $query = "SELECT * FROM schools WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", intval($school_id));
        echo "<p>Using ID query</p>";
    } else {
        throw new Exception("No valid URL parameters");
    }
    
    echo "<p>Step 5: Executing query...</p>";
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("School not found");
    }
    
    $row = $result->fetch_assoc();
    echo "<p>✅ School found: " . htmlspecialchars($row['name']) . "</p>";
    
    echo "<p>Step 6: Testing address building...</p>";
    $addressParts = array_filter([
        $row['zip_code'] ?? '',
        $row['street'] ?? ''
    ]);
    $address = implode(', ', $addressParts);
    echo "<p>Address: " . ($address ?: 'No address') . "</p>";
    
    echo "<p>Step 7: Testing page title...</p>";
    $pageTitle = $row['name'] ?? 'Школа';
    echo "<p>Page title: " . htmlspecialchars($pageTitle) . "</p>";
    
    echo "<h2>🎉 All steps completed successfully!</h2>";
    echo "<p>The template should work. The issue might be in the HTML/CSS part.</p>";
    
    $stmt->close();
    $connection->close();
    
} catch (Exception $e) {
    echo "<h2>❌ Error at step:</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " line " . $e->getLine() . "</p>";
}

echo "</body></html>";
?>
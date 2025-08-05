<?php
// Debug the redirect handler
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug School Redirect</h1>";

echo "<p>Testing redirect logic for school ID: " . ($_GET['id_school'] ?? 'none') . "</p>";

try {
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
    
    // Get school ID from URL parameter
    $school_id = $_GET['id_school'] ?? null;
    echo "<p>School ID from URL: " . ($school_id ?: 'null') . "</p>";
    
    if (!$school_id || !is_numeric($school_id)) {
        throw new Exception("Invalid school ID");
    }
    
    // Look up the slug for this school ID
    $query = "SELECT url_slug FROM schools WHERE id = ? AND url_slug IS NOT NULL AND url_slug != ''";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", intval($school_id));
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<p>Query executed for ID: " . intval($school_id) . "</p>";
    echo "<p>Rows found: " . $result->num_rows . "</p>";
    
    if ($result->num_rows === 0) {
        echo "<p>❌ School not found or no slug</p>";
        
        // Check if school exists at all
        $check_query = "SELECT id, name, url_slug FROM schools WHERE id = ?";
        $check_stmt = $connection->prepare($check_query);
        $check_stmt->bind_param("i", intval($school_id));
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $school = $check_result->fetch_assoc();
            echo "<p>School exists: " . htmlspecialchars($school['name']) . "</p>";
            echo "<p>URL slug: '" . htmlspecialchars($school['url_slug'] ?? 'NULL') . "'</p>";
        } else {
            echo "<p>School with ID $school_id does not exist</p>";
        }
        
        exit();
    }
    
    $row = $result->fetch_assoc();
    $slug = $row['url_slug'];
    
    echo "<p>✅ Found slug: " . htmlspecialchars($slug) . "</p>";
    echo "<p>Would redirect to: /school/{$slug}</p>";
    
    // Don't actually redirect in debug mode
    echo "<p><a href='/school/{$slug}'>Click to test redirect target</a></p>";
    
    $stmt->close();
    $connection->close();
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " line " . $e->getLine() . "</p>";
}
?>
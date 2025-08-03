<?php
// Direct test at root level
echo "School test at root level<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "GET params: ";
print_r($_GET);
echo "<br>";

// Test htaccess rewrite
if (isset($_GET['id_school'])) {
    echo "School ID from GET: " . $_GET['id_school'] . "<br>";
} else {
    echo "No id_school in GET parameters<br>";
}

// Test direct access to school page
echo "<br>Testing direct include of school files:<br>";

// Test database
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    echo "Database connection failed: " . $connection->connect_error . "<br>";
} else {
    echo "Database connected successfully<br>";
    
    // Test query
    $result = $connection->query("SELECT COUNT(*) as count FROM schools");
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "Total schools in database: " . $count . "<br>";
    }
}
?>
<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- INDEX.PHP DEBUG START -->\n";
echo "<h1>Index.php Debug</h1>";

// Check if files exist
$files_to_check = [
    '/database/db_connections.php',
    '/common-components/real_title.php',
    '/common-components/search-inline.php',
    '/common-components/cards-grid.php',
    '/real_template.php'
];

echo "<h2>File Check:</h2>";
echo "<ul>";
foreach ($files_to_check as $file) {
    $exists = file_exists($_SERVER['DOCUMENT_ROOT'] . $file);
    echo "<li>$file: " . ($exists ? "✓ EXISTS" : "✗ MISSING") . "</li>";
}
echo "</ul>";

// Try to include files one by one
echo "<h2>Testing includes:</h2>";

// Test database connection
echo "<p>1. Testing database connection...</p>";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    echo "<p style='color: green;'>✓ Database connection loaded</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test if connection exists
if (isset($connection)) {
    echo "<p style='color: green;'>✓ \$connection variable exists</p>";
} else {
    echo "<p style='color: red;'>✗ \$connection variable does not exist</p>";
}

// Simple content test
echo "<h2>Simple Template Test:</h2>";
$greyContent1 = '<div style="background: yellow; padding: 20px;"><h1>TEST HOMEPAGE CONTENT</h1></div>';
$greyContent2 = '';
$greyContent3 = '<div style="background: lightgreen; padding: 20px;">Stats would go here</div>';
$greyContent4 = '';
$greyContent5 = '<div style="background: lightblue; padding: 20px;">Posts grid would go here</div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Homepage Debug';

echo "<p>About to include real_template.php...</p>";

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
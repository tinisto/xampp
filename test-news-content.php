<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "Starting test...<br>";

// Test 1: Check if component files exist
$cardFile = $_SERVER['DOCUMENT_ROOT'] . '/components/card-component.php';
$navFile = $_SERVER['DOCUMENT_ROOT'] . '/components/navigation-component.php';

echo "Card component exists: " . (file_exists($cardFile) ? 'YES' : 'NO') . "<br>";
echo "Nav component exists: " . (file_exists($navFile) ? 'YES' : 'NO') . "<br>";

// Test 2: Try to include them
echo "Including card component...<br>";
require_once $cardFile;
echo "Card component included OK<br>";

echo "Including nav component...<br>";
require_once $navFile;
echo "Nav component included OK<br>";

// Test 3: Check if functions exist
echo "renderCard function exists: " . (function_exists('renderCard') ? 'YES' : 'NO') . "<br>";
echo "renderNavigation function exists: " . (function_exists('renderNavigation') ? 'YES' : 'NO') . "<br>";

// Test 4: Database test
echo "<br>Database test:<br>";
if (isset($connection)) {
    $testQuery = "SELECT COUNT(*) as cnt FROM news WHERE approved = 1";
    $result = mysqli_query($connection, $testQuery);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Total approved news: " . $row['cnt'] . "<br>";
    } else {
        echo "Query failed: " . mysqli_error($connection) . "<br>";
    }
} else {
    echo "No database connection<br>";
}

// Test 5: Include news-content.php with error reporting
echo "<br>Including news-content.php with error reporting:<br>";
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<div style='border: 2px solid red; padding: 10px;'>";
include $_SERVER['DOCUMENT_ROOT'] . '/news-content.php';
echo "</div>";
?>
<?php
// Test current site with proper database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct database connection for testing
$connection = new mysqli('127.0.0.1', 'root', '', '11klassniki');
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}
$connection->set_charset("utf8mb4");

// Make connection global so header can use it
$GLOBALS['connection'] = $connection;

// Check if categories exist
$result = $connection->query("SELECT * FROM categories LIMIT 5");
if ($result) {
    echo "<!-- Categories found: " . $result->num_rows . " -->\n";
}

// Include header to see Categories dropdown
include __DIR__ . '/common-components/header.php';
?>

<div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <h1>Current Main Branch Site</h1>
    <p>This shows the current state of the main branch with Categories dropdown in the header.</p>
    
    <h2>Categories in Database:</h2>
    <?php
    $result = $connection->query("SELECT * FROM categories ORDER BY title_category");
    if ($result && $result->num_rows > 0) {
        echo "<ul>";
        while ($cat = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($cat['title_category']) . " - /category/" . htmlspecialchars($cat['url_category']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No categories found in database.</p>";
    }
    ?>
    
    <h2>Main Links:</h2>
    <ul>
        <li><a href="/vpo-all-regions">Universities (ВУЗы)</a></li>
        <li><a href="/spo-all-regions">Colleges (ССУЗы)</a></li>
        <li><a href="/schools-all-regions">Schools (Школы)</a></li>
        <li><a href="/news">News (Новости)</a></li>
        <li><a href="/tests">Tests (Тесты)</a></li>
    </ul>
</div>

<?php include __DIR__ . '/common-components/footer.php'; ?>
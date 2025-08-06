<?php
// Diagnostic script to debug 404 errors
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>404 Error Debugging Tool</h1>";

// Check .htaccess routing
echo "<h2>1. URL Routing Configuration</h2>";
$htaccess_path = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccess_path)) {
    echo "<p>✓ .htaccess file exists</p>";
    // Show only relevant routing rules
    $content = file_get_contents($htaccess_path);
    if (preg_match('/# Categories\s*\n(.*?)(?=\n\s*#|$)/s', $content, $matches)) {
        echo "<h3>Category Routing Rules:</h3>";
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
    }
    if (preg_match('/(RewriteRule.*?category.*?$)/m', $content, $matches)) {
        echo "<h3>Category RewriteRule:</h3>";
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
    }
    if (preg_match('/(RewriteRule.*?post\/.*?$)/m', $content, $matches)) {
        echo "<h3>Post RewriteRule:</h3>";
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
    }
} else {
    echo "<p>✗ .htaccess file not found!</p>";
}

// Check database structure
echo "<h2>2. Database Table Structure</h2>";

// Check categories table
echo "<h3>Categories Table</h3>";
$result = $connection->query("DESCRIBE categories");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
}

// Check posts table
echo "<h3>Posts Table</h3>";
$result = $connection->query("DESCRIBE posts");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
}

// Check if 'ege' category exists
echo "<h2>3. Check for 'ege' Category</h2>";
$stmt = $connection->prepare("SELECT * FROM categories WHERE url_category = ?");
$stmt->bind_param("s", $ege);
$ege = 'ege';
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<p>✓ 'ege' category exists in database</p>";
    $row = $result->fetch_assoc();
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p>✗ 'ege' category NOT FOUND in database!</p>";
}

// List all categories
echo "<h2>4. All Categories</h2>";
$result = $connection->query("SELECT id_category, name_category, url_category FROM categories ORDER BY name_category");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>URL</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id_category']}</td><td>{$row['name_category']}</td><td>{$row['url_category']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No categories found!</p>";
}

// Check sample posts and their URLs
echo "<h2>5. Sample Posts with URLs</h2>";
$result = $connection->query("SELECT id_post, title, url_slug, url_post FROM posts LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>url_slug</th><th>url_post (old)</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id_post']}</td><td>{$row['title']}</td><td>{$row['url_slug']}</td><td>{$row['url_post']}</td></tr>";
    }
    echo "</table>";
}

// Check if there are posts without url_slug
echo "<h2>6. Posts without url_slug</h2>";
$result = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_slug IS NULL OR url_slug = ''");
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        echo "<p>⚠️ Found {$row['count']} posts without url_slug!</p>";
    } else {
        echo "<p>✓ All posts have url_slug values</p>";
    }
}

$connection->close();
?>
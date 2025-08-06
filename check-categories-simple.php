<?php
// Simple categories table checker for web access

header('Content-Type: text/html; charset=utf-8');

echo "<html><head><title>Categories Table Check</title></head><body>";
echo "<h1>Categories Table Structure Check</h1>";

echo "<p><strong>Status:</strong> This script needs to be accessed through a web browser with XAMPP running.</p>";

echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Make sure XAMPP is running (Apache and MySQL)</li>";
echo "<li>Open this URL in your browser: <code>http://localhost/check-categories-simple.php</code></li>";
echo "<li>You should see the database results below</li>";
echo "</ol>";

// Try to include the database connection
try {
    require_once 'database/db_connections.php';
    
    echo "<h2>✅ Database Connection Successful</h2>";
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    // 1. Show table structure
    echo "<h3>1. Table Structure (DESCRIBE categories):</h3>";
    echo "<div style='background: #f5f5f5; padding: 15px; font-family: monospace; overflow-x: auto;'>";
    
    $result = $connection->query("DESCRIBE categories");
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; font-family: monospace;'>";
        echo "<tr style='background-color: #ddd;'><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($column = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Error describing table: " . htmlspecialchars($connection->error) . "</p>";
    }
    echo "</div>";
    
    // 2. Check for 'ege' category
    echo "<h3>2. Searching for 'ege' category:</h3>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #0066cc;'>";
    
    $found_ege = false;
    
    // Search in title_category
    $stmt = $connection->prepare("SELECT * FROM categories WHERE title_category LIKE ? OR title_category = ?");
    if ($stmt) {
        $search_term = '%ege%';
        $exact_term = 'ege';
        $stmt->bind_param("ss", $search_term, $exact_term);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $found_ege = true;
            echo "<h4>✅ Found 'ege' in title_category:</h4>";
            while ($row = $result->fetch_assoc()) {
                echo "<div style='background: white; margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
                echo "<strong>ID:</strong> " . htmlspecialchars($row['id_category']) . "<br>";
                echo "<strong>Title:</strong> " . htmlspecialchars($row['title_category']) . "<br>";
                echo "<strong>URL:</strong> " . htmlspecialchars($row['url_category']);
                echo "</div>";
            }
        }
        $stmt->close();
    }
    
    // Search in url_category
    $stmt = $connection->prepare("SELECT * FROM categories WHERE url_category LIKE ? OR url_category = ?");
    if ($stmt) {
        $search_term = '%ege%';
        $exact_term = 'ege';
        $stmt->bind_param("ss", $search_term, $exact_term);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $found_ege = true;
            echo "<h4>✅ Found 'ege' in url_category:</h4>";
            while ($row = $result->fetch_assoc()) {
                echo "<div style='background: white; margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
                echo "<strong>ID:</strong> " . htmlspecialchars($row['id_category']) . "<br>";
                echo "<strong>Title:</strong> " . htmlspecialchars($row['title_category']) . "<br>";
                echo "<strong>URL:</strong> " . htmlspecialchars($row['url_category']);
                echo "</div>";
            }
        }
        $stmt->close();
    }
    
    if (!$found_ege) {
        echo "<h4>❌ No 'ege' category found</h4>";
        echo "<p>Searched in both 'title_category' and 'url_category' fields.</p>";
    }
    
    echo "</div>";
    
    // 3. Show all categories
    echo "<h3>3. All Categories:</h3>";
    $result = $connection->query("SELECT * FROM categories ORDER BY id_category");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>Title</th><th>URL</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_category']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title_category']) . "</td>";
            echo "<td>" . htmlspecialchars($row['url_category']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show total count
        $count_result = $connection->query("SELECT COUNT(*) as total FROM categories");
        $count = $count_result->fetch_assoc();
        echo "<p><strong>Total categories: " . $count['total'] . "</strong></p>";
    } else {
        echo "<p>No categories found</p>";
    }
    
    $connection->close();
    
} catch (Exception $e) {
    echo "<h2>❌ Database Connection Error</h2>";
    echo "<div style='background: #ffeeee; border: 1px solid #cc0000; padding: 15px; color: #cc0000;'>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>";
    echo "<strong>Troubleshooting:</strong><br>";
    echo "1. Make sure XAMPP is running<br>";
    echo "2. Check that MySQL service is started in XAMPP Control Panel<br>";
    echo "3. Verify database credentials in .env file<br>";
    echo "4. Ensure the database '11klassniki_claude' exists";
    echo "</div>";
}

echo "</body></html>";
?>
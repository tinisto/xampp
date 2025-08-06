<?php
require_once 'database/db_connections.php';

echo "<h2>Categories Table Structure Check</h2>";

// Check if connection was successful
if ($connection->connect_error) {
    die("<p style='color: red;'>Connection failed: " . $connection->connect_error . "</p>");
}

try {
    // 1. Show table structure
    echo "<h3>1. Table Structure (DESCRIBE categories):</h3>";
    echo "<pre>";
    $result = $connection->query("DESCRIBE categories");
    
    if ($result) {
        echo "Column Name       | Type                 | Null | Key | Default | Extra\n";
        echo str_repeat("-", 80) . "\n";
        while ($column = $result->fetch_assoc()) {
            printf("%-17s | %-20s | %-4s | %-3s | %-7s | %s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Null'], 
                $column['Key'], 
                $column['Default'] ?? 'NULL', 
                $column['Extra']
            );
        }
    } else {
        echo "Error describing table: " . $connection->error;
    }
    echo "</pre>";
    
    // 2. Sample categories
    echo "<h3>2. Sample Categories (first 10):</h3>";
    echo "<pre>";
    $result = $connection->query("SELECT * FROM categories ORDER BY id_category LIMIT 10");
    
    if ($result && $result->num_rows > 0) {
        $index = 1;
        while ($category = $result->fetch_assoc()) {
            echo "Category #" . $index . ":\n";
            foreach ($category as $key => $value) {
                echo "  $key: " . htmlspecialchars($value ?? 'NULL') . "\n";
            }
            echo "\n";
            $index++;
        }
    } else {
        echo "No categories found or error: " . $connection->error . "\n";
    }
    echo "</pre>";
    
    // 3. Check for 'ege' category - search in title and url fields
    echo "<h3>3. Checking for 'ege' category:</h3>";
    echo "<pre>";
    
    // Search in title_category
    $stmt = $connection->prepare("SELECT * FROM categories WHERE title_category LIKE ? OR title_category = ?");
    $search_term = '%ege%';
    $exact_term = 'ege';
    $stmt->bind_param("ss", $search_term, $exact_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        echo "Found in title_category:\n";
        while ($row = $result->fetch_assoc()) {
            echo "  ID: " . $row['id_category'] . "\n";
            echo "  Title: " . htmlspecialchars($row['title_category']) . "\n";
            echo "  URL: " . htmlspecialchars($row['url_category']) . "\n\n";
        }
    } else {
        echo "No 'ege' found in title_category field.\n";
    }
    
    // Search in url_category
    $stmt = $connection->prepare("SELECT * FROM categories WHERE url_category LIKE ? OR url_category = ?");
    $stmt->bind_param("ss", $search_term, $exact_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        echo "Found in url_category:\n";
        while ($row = $result->fetch_assoc()) {
            echo "  ID: " . $row['id_category'] . "\n";
            echo "  Title: " . htmlspecialchars($row['title_category']) . "\n";
            echo "  URL: " . htmlspecialchars($row['url_category']) . "\n\n";
        }
    } else {
        echo "No 'ege' found in url_category field.\n";
    }
    echo "</pre>";
    
    // 4. Show all categories in a table
    echo "<h3>4. All Categories:</h3>";
    $result = $connection->query("SELECT * FROM categories ORDER BY id_category");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>Title</th><th>URL</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id_category'] . "</td>";
            echo "<td>" . htmlspecialchars($row['title_category']) . "</td>";
            echo "<td>" . htmlspecialchars($row['url_category']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No categories found</p>";
    }
    
    // 5. Show total count
    echo "<h3>5. Total Categories Count:</h3>";
    echo "<pre>";
    $result = $connection->query("SELECT COUNT(*) as total FROM categories");
    if ($result) {
        $count = $result->fetch_assoc();
        echo "Total categories in database: " . $count['total'];
    } else {
        echo "Error getting count: " . $connection->error;
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$connection->close();
?>
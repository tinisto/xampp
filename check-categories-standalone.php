<?php
// Standalone categories table checker - doesn't require web environment
echo "Categories Table Structure Check\n";
echo "================================\n\n";

// Create a direct database connection
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = '11klassniki_claude';

try {
    echo "Attempting to connect to database '$database' at '$host'...\n";
    $connection = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error . "\n");
    }
    
    echo "Connected to database successfully.\n\n";
    
    // 1. Show table structure
    echo "1. Table Structure (DESCRIBE categories):\n";
    echo str_repeat("-", 50) . "\n";
    $result = $connection->query("DESCRIBE categories");
    
    if ($result) {
        printf("%-17s | %-20s | %-4s | %-3s | %-7s | %s\n", 
            'Column Name', 'Type', 'Null', 'Key', 'Default', 'Extra');
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
        echo "Error describing table: " . $connection->error . "\n";
    }
    
    echo "\n\n";
    
    // 2. Sample categories
    echo "2. Sample Categories (first 10):\n";
    echo str_repeat("-", 50) . "\n";
    $result = $connection->query("SELECT * FROM categories ORDER BY id_category LIMIT 10");
    
    if ($result && $result->num_rows > 0) {
        $index = 1;
        while ($category = $result->fetch_assoc()) {
            echo "Category #" . $index . ":\n";
            foreach ($category as $key => $value) {
                echo "  $key: " . ($value ?? 'NULL') . "\n";
            }
            echo "\n";
            $index++;
        }
    } else {
        echo "No categories found or error: " . $connection->error . "\n";
    }
    
    // 3. Check for 'ege' category
    echo "\n3. Checking for 'ege' category:\n";
    echo str_repeat("-", 50) . "\n";
    
    // Search in title_category
    $stmt = $connection->prepare("SELECT * FROM categories WHERE title_category LIKE ? OR title_category = ?");
    if ($stmt) {
        $search_term = '%ege%';
        $exact_term = 'ege';
        $stmt->bind_param("ss", $search_term, $exact_term);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            echo "Found in title_category:\n";
            while ($row = $result->fetch_assoc()) {
                echo "  ID: " . $row['id_category'] . "\n";
                echo "  Title: " . $row['title_category'] . "\n";
                echo "  URL: " . $row['url_category'] . "\n\n";
            }
        } else {
            echo "No 'ege' found in title_category field.\n";
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
            echo "Found in url_category:\n";
            while ($row = $result->fetch_assoc()) {
                echo "  ID: " . $row['id_category'] . "\n";
                echo "  Title: " . $row['title_category'] . "\n";
                echo "  URL: " . $row['url_category'] . "\n\n";
            }
        } else {
            echo "No 'ege' found in url_category field.\n";
        }
        $stmt->close();
    }
    
    // 4. Show all categories
    echo "\n4. All Categories:\n";
    echo str_repeat("-", 50) . "\n";
    $result = $connection->query("SELECT * FROM categories ORDER BY id_category");
    
    if ($result && $result->num_rows > 0) {
        printf("%-4s | %-30s | %-20s\n", 'ID', 'Title', 'URL');
        echo str_repeat("-", 60) . "\n";
        while ($row = $result->fetch_assoc()) {
            printf("%-4s | %-30s | %-20s\n", 
                $row['id_category'], 
                substr($row['title_category'], 0, 30),
                substr($row['url_category'], 0, 20)
            );
        }
    } else {
        echo "No categories found\n";
    }
    
    // 5. Show total count
    echo "\n\n5. Total Categories Count:\n";
    echo str_repeat("-", 50) . "\n";
    $result = $connection->query("SELECT COUNT(*) as total FROM categories");
    if ($result) {
        $count = $result->fetch_assoc();
        echo "Total categories in database: " . $count['total'] . "\n";
    } else {
        echo "Error getting count: " . $connection->error . "\n";
    }
    
    $connection->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
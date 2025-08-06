<?php
// Direct database connection to check categories structure

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = '11klassniki_claude';

try {
    // Create connection
    $connection = new mysqli($host, $user, $pass, $dbname);
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "✓ Database connection successful\n\n";
    
    // Check if categories table exists
    $result = $connection->query("SHOW TABLES LIKE 'categories'");
    if ($result->num_rows == 0) {
        echo "✗ Categories table does not exist!\n";
        exit(1);
    }
    
    echo "✓ Categories table exists\n\n";
    
    // Show table structure
    echo "CATEGORIES TABLE STRUCTURE:\n";
    echo "==========================\n";
    $result = $connection->query("DESCRIBE categories");
    if ($result) {
        printf("%-20s %-15s %-8s %-5s %-10s %s\n", 
               "Field", "Type", "Null", "Key", "Default", "Extra");
        echo str_repeat("-", 80) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            printf("%-20s %-15s %-8s %-5s %-10s %s\n",
                   $row['Field'], 
                   $row['Type'], 
                   $row['Null'], 
                   $row['Key'], 
                   $row['Default'] ?? 'NULL',
                   $row['Extra']);
        }
    }
    
    echo "\n\nEXISTING CATEGORIES:\n";
    echo "===================\n";
    $result = $connection->query("SELECT * FROM categories ORDER BY id_category LIMIT 10");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id_category']} | ";
            
            // Check which title field exists
            if (isset($row['title_category'])) {
                echo "Title: {$row['title_category']} | ";
            } elseif (isset($row['name_category'])) {
                echo "Name: {$row['name_category']} | ";
            }
            
            if (isset($row['url_category'])) {
                echo "URL: {$row['url_category']}";
            }
            echo "\n";
        }
    } else {
        echo "No categories found!\n";
    }
    
    // Check for 'ege' category specifically
    echo "\n\nCHECKING FOR 'ege' CATEGORY:\n";
    echo "============================\n";
    $stmt = $connection->prepare("SELECT * FROM categories WHERE url_category = ?");
    $ege = 'ege';
    $stmt->bind_param("s", $ege);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "✓ 'ege' category found!\n";
        $row = $result->fetch_assoc();
        foreach ($row as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "✗ 'ege' category NOT found!\n";
    }
    
    // Check posts table for url_slug field
    echo "\n\nPOSTS TABLE - URL FIELDS:\n";
    echo "========================\n";
    $result = $connection->query("SHOW COLUMNS FROM posts LIKE '%url%'");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Field: {$row['Field']} | Type: {$row['Type']}\n";
        }
    }
    
    // Count posts without url_slug
    $result = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_slug IS NULL OR url_slug = ''");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "\nPosts without url_slug: {$row['count']}\n";
    }
    
    $connection->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
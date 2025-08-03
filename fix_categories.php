<?php
// Fix categories table
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Fixing Categories</h2>";

// Check if categories table exists
$tables = $connection->query("SHOW TABLES LIKE 'categories'");
if ($tables->num_rows == 0) {
    echo "<p>Creating categories table...</p>";
    
    $create_sql = "CREATE TABLE categories (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        url_category VARCHAR(255) NOT NULL UNIQUE,
        title_category VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($connection->query($create_sql)) {
        echo "<p>✅ Categories table created</p>";
    } else {
        echo "<p>❌ Error creating table: " . $connection->error . "</p>";
    }
}

// Check columns
$columns = $connection->query("DESCRIBE categories");
$column_names = [];
while ($col = $columns->fetch_assoc()) {
    $column_names[] = $col['Field'];
}

echo "<p>Current columns: " . implode(', ', $column_names) . "</p>";

// Check if we need to add missing columns
$needs_url = !in_array('url_category', $column_names);
$needs_title = !in_array('title_category', $column_names);

if ($needs_url || $needs_title) {
    // Check if there are alternative column names
    $has_name = in_array('name', $column_names);
    $has_slug = in_array('slug', $column_names);
    $has_url = in_array('url', $column_names);
    
    if ($has_name && !$needs_title) {
        // We have title_category but need to handle name column
    } elseif ($has_name && $needs_title) {
        // Rename 'name' to 'title_category'
        echo "<p>Renaming 'name' column to 'title_category'...</p>";
        $alter_sql = "ALTER TABLE categories CHANGE COLUMN name title_category VARCHAR(255) NOT NULL";
        if ($connection->query($alter_sql)) {
            echo "<p>✅ Column renamed</p>";
        } else {
            echo "<p>❌ Error: " . $connection->error . "</p>";
        }
    } elseif ($needs_title) {
        // Add title_category column
        echo "<p>Adding title_category column...</p>";
        $alter_sql = "ALTER TABLE categories ADD COLUMN title_category VARCHAR(255) NOT NULL";
        if ($connection->query($alter_sql)) {
            echo "<p>✅ Column added</p>";
        } else {
            echo "<p>❌ Error: " . $connection->error . "</p>";
        }
    }
    
    if (($has_slug || $has_url) && $needs_url) {
        // Rename 'slug' or 'url' to 'url_category'
        $old_col = $has_slug ? 'slug' : 'url';
        echo "<p>Renaming '$old_col' column to 'url_category'...</p>";
        $alter_sql = "ALTER TABLE categories CHANGE COLUMN $old_col url_category VARCHAR(255) NOT NULL";
        if ($connection->query($alter_sql)) {
            echo "<p>✅ Column renamed</p>";
        } else {
            echo "<p>❌ Error: " . $connection->error . "</p>";
        }
    } elseif ($needs_url) {
        // Add url_category column
        echo "<p>Adding url_category column...</p>";
        $alter_sql = "ALTER TABLE categories ADD COLUMN url_category VARCHAR(255) NOT NULL";
        if ($connection->query($alter_sql)) {
            echo "<p>✅ Column added</p>";
        } else {
            echo "<p>❌ Error: " . $connection->error . "</p>";
        }
    }
}

// Check if categories table is empty
$count = $connection->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc();
if ($count['total'] == 0) {
    echo "<p>Categories table is empty. Adding default categories...</p>";
    
    $default_categories = [
        ['novosti-obrazovaniya', 'Новости образования'],
        ['postupleniye', 'Поступление'],
        ['yege-oge', 'ЕГЭ и ОГЭ'],
        ['olimpiady', 'Олимпиады'],
        ['granty-stipendii', 'Гранты и стипендии'],
        ['karera', 'Карьера'],
        ['studencheskaya-zhizn', 'Студенческая жизнь'],
        ['mezhdunarodnoe-obrazovanie', 'Международное образование'],
        ['onlayn-obrazovanie', 'Онлайн образование'],
        ['roditeljam', 'Родителям']
    ];
    
    foreach ($default_categories as $cat) {
        $stmt = $connection->prepare("INSERT INTO categories (url_category, title_category) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat[0], $cat[1]);
        if ($stmt->execute()) {
            echo "<p>✅ Added: " . $cat[1] . "</p>";
        } else {
            echo "<p>❌ Error adding " . $cat[1] . ": " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Show final result
echo "<h3>Final check:</h3>";
$result = $connection->query("SELECT url_category, title_category FROM categories ORDER BY title_category");
if ($result && $result->num_rows > 0) {
    echo "<p>✅ Categories are now available:</p>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href='/category/" . htmlspecialchars($row['url_category']) . "'>" . 
             htmlspecialchars($row['title_category']) . "</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No categories found</p>";
}

$connection->close();
?>
<?php
// Migrate news categories from numeric strings to proper category IDs
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrate News Categories - 11-классники</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiBmaWxsPSIjMDA3YmZmIi8+Cjx0ZXh0IHg9IjE2IiB5PSIyMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmb250LXdlaWdodD0iYm9sZCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPjExPC90ZXh0Pgo8L3N2Zz4K" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .action-box { background: #e8f4f8; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
<h1>Migrate News Categories</h1>

<?php
$action = $_GET['action'] ?? '';

if ($action === 'add_news_categories') {
    echo "<h2>Adding News-Specific Categories</h2>";
    
    $newCategories = [
        ['title' => 'Новости ВУЗов', 'url_slug' => 'novosti-vuzov', 'description' => 'Новости университетов и высшего образования'],
        ['title' => 'Новости школ', 'url_slug' => 'novosti-shkol', 'description' => 'Новости школ и среднего образования'],
        ['title' => 'Студенческие новости', 'url_slug' => 'studencheskie-novosti', 'description' => 'Новости студенческой жизни'],
        ['title' => 'Объявления', 'url_slug' => 'obyavleniya', 'description' => 'Важные объявления и анонсы']
    ];
    
    foreach ($newCategories as $cat) {
        // Check if category already exists
        $checkQuery = "SELECT id_category FROM categories WHERE url_slug = '{$cat['url_slug']}'";
        $checkResult = mysqli_query($connection, $checkQuery);
        
        if (mysqli_num_rows($checkResult) == 0) {
            // Get next available ID
            $maxIdQuery = "SELECT MAX(id_category) as max_id FROM categories";
            $maxIdResult = mysqli_query($connection, $maxIdQuery);
            $maxId = mysqli_fetch_assoc($maxIdResult)['max_id'];
            $newId = $maxId + 1;
            
            $insertQuery = "INSERT INTO categories (id_category, title_category, url_slug, category_name) 
                           VALUES ($newId, '{$cat['title']}', '{$cat['url_slug']}', '{$cat['title']}')";
            
            if (mysqli_query($connection, $insertQuery)) {
                echo "<p class='success'>✅ Added category: {$cat['title']} (ID: $newId)</p>";
            } else {
                echo "<p class='error'>❌ Error adding {$cat['title']}: " . mysqli_error($connection) . "</p>";
            }
        } else {
            $existing = mysqli_fetch_assoc($checkResult);
            echo "<p class='info'>ℹ️ Category '{$cat['title']}' already exists (ID: {$existing['id_category']})</p>";
        }
    }
    
} elseif ($action === 'migrate_news') {
    echo "<h2>Migrating News Categories</h2>";
    
    // First, get the category IDs we need
    $categoryMap = [];
    $categories = [
        'novosti-vuzov' => '1',        // Category 1 -> University news
        'novosti-shkol' => '4',        // Category 4 -> School news
        'studencheskie-novosti' => '2', // Category 2 -> Student news
        'obyavleniya' => '3'           // Category 3 -> Announcements
    ];
    
    // Get actual category IDs from database
    foreach (array_keys($categories) as $slug) {
        $query = "SELECT id_category FROM categories WHERE url_slug = '$slug'";
        $result = mysqli_query($connection, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $categoryMap[$categories[$slug]] = $row['id_category'];
        }
    }
    
    if (empty($categoryMap)) {
        echo "<p class='error'>❌ News categories not found. Please add them first.</p>";
        return;
    }
    
    // Show mapping plan
    echo "<h3>Category Mapping Plan:</h3>";
    echo "<table>";
    echo "<tr><th>Old Category</th><th>New Category ID</th><th>Count</th></tr>";
    
    foreach ($categoryMap as $old => $new) {
        $countQuery = "SELECT COUNT(*) as count FROM news WHERE category_news = '$old'";
        $countResult = mysqli_query($connection, $countQuery);
        $count = mysqli_fetch_assoc($countResult)['count'];
        
        echo "<tr>";
        echo "<td>$old</td>";
        echo "<td>$new</td>";
        echo "<td>$count items</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Add new column if it doesn't exist
    $checkColumnQuery = "SHOW COLUMNS FROM news LIKE 'category_id'";
    $checkColumnResult = mysqli_query($connection, $checkColumnQuery);
    
    if (mysqli_num_rows($checkColumnResult) == 0) {
        echo "<p>Adding category_id column to news table...</p>";
        $addColumnQuery = "ALTER TABLE news ADD COLUMN category_id INT AFTER category_news";
        if (mysqli_query($connection, $addColumnQuery)) {
            echo "<p class='success'>✅ Added category_id column</p>";
        } else {
            echo "<p class='error'>❌ Error adding column: " . mysqli_error($connection) . "</p>";
            return;
        }
    }
    
    // Perform migration
    echo "<h3>Migrating categories...</h3>";
    
    foreach ($categoryMap as $old => $new) {
        $updateQuery = "UPDATE news SET category_id = $new WHERE category_news = '$old'";
        if (mysqli_query($connection, $updateQuery)) {
            $affected = mysqli_affected_rows($connection);
            echo "<p class='success'>✅ Migrated category $old → $new ($affected items)</p>";
        } else {
            echo "<p class='error'>❌ Error migrating category $old: " . mysqli_error($connection) . "</p>";
        }
    }
    
    echo "<p class='info'>ℹ️ Migration complete! The old category_news field is preserved for rollback if needed.</p>";
    
} elseif ($action === 'standardize_fields') {
    echo "<h2>Standardizing News Table Fields</h2>";
    
    // This would rename fields to match the posts table structure
    echo "<p class='info'>This action would rename news table fields to match posts table structure.</p>";
    echo "<p>Field mapping:</p>";
    echo "<pre>";
    echo "title_news → title
text_news → content
author_news → author
date_news → created_at
view_news → views
image_news → featured_image
category_id → category_id (already standardized)
url_slug → url_slug (already standardized)
</pre>";
    echo "<p class='error'>⚠️ This is a destructive operation. Make sure to backup first!</p>";
    
} else {
    // Show available actions
    ?>
    <h2>Migration Steps</h2>
    
    <div class="action-box">
        <h3>Step 1: Add News Categories</h3>
        <p>Create specific categories for news items in the categories table:</p>
        <ul>
            <li>Новости ВУЗов (University news)</li>
            <li>Новости школ (School news)</li>
            <li>Студенческие новости (Student news)</li>
            <li>Объявления (Announcements)</li>
        </ul>
        <button onclick="window.location.href='?action=add_news_categories'">Add News Categories</button>
    </div>
    
    <div class="action-box">
        <h3>Step 2: Migrate News Categories</h3>
        <p>Convert numeric category values (1,2,3,4) to proper category IDs:</p>
        <ul>
            <li>Category 1 → Новости ВУЗов (243 items)</li>
            <li>Category 2 → Студенческие новости (96 items)</li>
            <li>Category 3 → Объявления (6 items)</li>
            <li>Category 4 → Новости школ (156 items)</li>
        </ul>
        <button onclick="window.location.href='?action=migrate_news'">Migrate Categories</button>
    </div>
    
    <div class="action-box">
        <h3>Step 3: Standardize Field Names (Optional)</h3>
        <p>Rename news table fields to match posts table structure for consistency.</p>
        <button onclick="window.location.href='?action=standardize_fields'" disabled>Standardize Fields (Coming Soon)</button>
    </div>
    
    <div class="action-box" style="background: #fff3cd; border-color: #ffc107;">
        <h3>⚠️ Important Notes</h3>
        <ul>
            <li>Backup is recommended before migration</li>
            <li>Old category_news field is preserved during migration</li>
            <li>Migration adds category_id field without removing old field</li>
            <li>This allows for easy rollback if needed</li>
        </ul>
    </div>
    <?php
}

mysqli_close($connection);
?>

<p style="margin-top: 40px;">
    <a href="execute-db-standardization.php" style="color: #007bff;">← Back to Database Standardization</a> | 
    <a href="analyze-news-categories.php" style="color: #007bff;">View Analysis</a>
</p>

</body>
</html>
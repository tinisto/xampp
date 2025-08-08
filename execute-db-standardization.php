<?php
// Execute database standardization - Phase 1
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Execute Database Standardization - 11-классники</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiBmaWxsPSIjMDA3YmZmIi8+Cjx0ZXh0IHg9IjE2IiB5PSIyMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmb250LXdlaWdodD0iYm9sZCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPjExPC90ZXh0Pgo8L3N2Zz4K" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .action-box { background: #e8f4f8; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        button:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>
<h1>Execute Database Standardization</h1>

<?php
// Check if action is requested
$action = $_GET['action'] ?? '';

if ($action === 'add_category_2') {
    echo "<h2>Phase 1: Adding Missing Category ID 2</h2>";
    
    // First check if category 2 already exists
    $checkQuery = "SELECT * FROM categories WHERE id_category = 2";
    $checkResult = mysqli_query($connection, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo "<p class='warning'>⚠️ Category ID 2 already exists!</p>";
        $existing = mysqli_fetch_assoc($checkResult);
        echo "<p>Existing category: {$existing['title_category']}</p>";
    } else {
        // First, fix any empty url_slug values that might cause constraint issues
        echo "<h3>Checking for empty url_slug values...</h3>";
        
        $emptySlugQuery = "SELECT id_category, title_category FROM categories WHERE url_slug = '' OR url_slug IS NULL";
        $emptySlugResult = mysqli_query($connection, $emptySlugQuery);
        
        if ($emptySlugResult && mysqli_num_rows($emptySlugResult) > 0) {
            echo "<p>Found categories with empty url_slug. Fixing them first...</p>";
            
            while ($row = mysqli_fetch_assoc($emptySlugResult)) {
                // Generate slug from title
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $row['title_category']));
                $slug = preg_replace('/-+/', '-', $slug);
                $slug = trim($slug, '-');
                
                // Make sure slug is unique
                $uniqueSlug = $slug;
                $counter = 1;
                while (true) {
                    $checkUniqueQuery = "SELECT id_category FROM categories WHERE url_slug = '$uniqueSlug' AND id_category != {$row['id_category']}";
                    $checkUniqueResult = mysqli_query($connection, $checkUniqueQuery);
                    if (mysqli_num_rows($checkUniqueResult) == 0) {
                        break;
                    }
                    $uniqueSlug = $slug . '-' . $counter;
                    $counter++;
                }
                
                $updateQuery = "UPDATE categories SET url_slug = '$uniqueSlug' WHERE id_category = {$row['id_category']}";
                if (mysqli_query($connection, $updateQuery)) {
                    echo "<p class='success'>✅ Fixed url_slug for category ID {$row['id_category']}: $uniqueSlug</p>";
                } else {
                    echo "<p class='error'>❌ Error fixing category ID {$row['id_category']}: " . mysqli_error($connection) . "</p>";
                }
            }
        }
        // First check what columns exist in categories table
        $columnsQuery = "SHOW COLUMNS FROM categories";
        $columnsResult = mysqli_query($connection, $columnsQuery);
        $columns = [];
        while ($col = mysqli_fetch_assoc($columnsResult)) {
            $columns[] = $col['Field'];
        }
        
        // Build insert query based on existing columns
        $insertFields = ['id_category'];
        $insertValues = [2];
        
        if (in_array('title_category', $columns)) {
            $insertFields[] = 'title_category';
            $insertValues[] = "'Без категории'";
        }
        
        if (in_array('url_category', $columns)) {
            $insertFields[] = 'url_category';
            $insertValues[] = "'bez-kategorii'";
        }
        
        if (in_array('url_slug', $columns)) {
            $insertFields[] = 'url_slug';
            $insertValues[] = "'bez-kategorii'";
        }
        
        if (in_array('category_name', $columns)) {
            $insertFields[] = 'category_name';
            $insertValues[] = "'Без категории'";
        }
        
        if (in_array('category_name_en', $columns)) {
            $insertFields[] = 'category_name_en';
            $insertValues[] = "'no-category'";
        }
        
        $insertQuery = "INSERT INTO categories (" . implode(', ', $insertFields) . ") 
                       VALUES (" . implode(', ', $insertValues) . ")";
        
        if (mysqli_query($connection, $insertQuery)) {
            echo "<p class='success'>✅ Successfully added Category ID 2 'Без категории'</p>";
            
            // Show affected posts
            $affectedQuery = "SELECT COUNT(*) as count FROM posts WHERE category = 2";
            $affectedResult = mysqli_query($connection, $affectedQuery);
            $affected = mysqli_fetch_assoc($affectedResult)['count'];
            echo "<p>This fixes <strong>$affected posts</strong> that were missing their category!</p>";
        } else {
            echo "<p class='error'>❌ Error adding category: " . mysqli_error($connection) . "</p>";
        }
    }
} elseif ($action === 'backup_tables') {
    echo "<h2>Creating Backup Tables</h2>";
    
    $tables = ['categories', 'posts', 'news'];
    foreach ($tables as $table) {
        $backupTable = $table . '_backup_' . date('Ymd_His');
        $backupQuery = "CREATE TABLE $backupTable AS SELECT * FROM $table";
        
        if (mysqli_query($connection, $backupQuery)) {
            echo "<p class='success'>✅ Created backup: $backupTable</p>";
        } else {
            echo "<p class='error'>❌ Error backing up $table: " . mysqli_error($connection) . "</p>";
        }
    }
} elseif ($action === 'analyze_categories') {
    echo "<h2>Category Usage Analysis</h2>";
    
    // Analyze posts table
    echo "<h3>Posts Table Category Usage:</h3>";
    $postsQuery = "SELECT p.category, c.title_category, COUNT(*) as count 
                   FROM posts p 
                   LEFT JOIN categories c ON p.category = c.id_category 
                   GROUP BY p.category 
                   ORDER BY count DESC";
    $postsResult = mysqli_query($connection, $postsQuery);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Category ID</th><th>Category Name</th><th>Post Count</th></tr>";
    while ($row = mysqli_fetch_assoc($postsResult)) {
        $categoryName = $row['title_category'] ?? '<span class="error">MISSING</span>';
        echo "<tr><td>{$row['category']}</td><td>$categoryName</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
    
    // Analyze news table
    echo "<h3>News Table Category Usage:</h3>";
    $newsQuery = "SELECT category_news, COUNT(*) as count 
                  FROM news 
                  GROUP BY category_news 
                  ORDER BY count DESC";
    $newsResult = mysqli_query($connection, $newsQuery);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Category String</th><th>News Count</th><th>Suggested Category ID</th></tr>";
    while ($row = mysqli_fetch_assoc($newsResult)) {
        // Map string categories to IDs
        switch($row['category_news']) {
            case 'education-news':
                $suggestedId = 1;
                break;
            case 'vpo':
                $suggestedId = 'Create new VPO category';
                break;
            case 'spo':
                $suggestedId = 'Create new SPO category';
                break;
            case 'school':
                $suggestedId = 'Create new School category';
                break;
            default:
                $suggestedId = 'Unknown mapping';
        }
        echo "<tr><td>{$row['category_news']}</td><td>{$row['count']}</td><td>$suggestedId</td></tr>";
    }
    echo "</table>";
} else {
    // Show available actions
    ?>
    <h2>Available Actions</h2>
    
    <div class="action-box">
        <h3>🚨 Phase 1: Critical Fix - Add Missing Category</h3>
        <p>Category ID 2 is missing from categories table but referenced by 14 posts.</p>
        <button onclick="window.location.href='?action=add_category_2'">Add Category ID 2</button>
    </div>
    
    <div class="action-box">
        <h3>📊 Analyze Category Usage</h3>
        <p>See detailed analysis of how categories are used in posts and news tables.</p>
        <button onclick="window.location.href='?action=analyze_categories'">Analyze Categories</button>
    </div>
    
    <div class="action-box">
        <h3>💾 Create Backup Tables</h3>
        <p>Create backup copies of categories, posts, and news tables before making changes.</p>
        <button onclick="window.location.href='?action=backup_tables'">Create Backups</button>
    </div>
    
    <h2>Standardization SQL Scripts</h2>
    
    <h3>Step 1: Fix Categories Table Primary Key</h3>
    <pre>
-- Add new standard id column (don't run if already exists)
ALTER TABLE categories ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- Copy id_category values to id
UPDATE categories SET id = id_category;

-- Update all foreign key references
ALTER TABLE posts ADD COLUMN category_id INT;
UPDATE posts SET category_id = category;
    </pre>
    
    <h3>Step 2: Standardize Posts Table</h3>
    <pre>
-- Rename fields to standard names
ALTER TABLE posts 
    CHANGE COLUMN title_post title VARCHAR(255),
    CHANGE COLUMN text_post content TEXT,
    CHANGE COLUMN author_post author VARCHAR(255),
    CHANGE COLUMN date_post created_at DATETIME,
    CHANGE COLUMN view_post views INT DEFAULT 0,
    CHANGE COLUMN image_post featured_image VARCHAR(255);
    </pre>
    
    <h3>Step 3: Standardize News Table</h3>
    <pre>
-- Add category_id and map string values
ALTER TABLE news ADD COLUMN category_id INT;

-- Map category strings to IDs
UPDATE news SET category_id = 1 WHERE category_news = 'education-news';
UPDATE news SET category_id = 6 WHERE category_news = 'abiturientam';

-- Rename fields to standard names  
ALTER TABLE news 
    CHANGE COLUMN title_news title VARCHAR(255),
    CHANGE COLUMN text_news content TEXT,
    CHANGE COLUMN author_news author VARCHAR(255),
    CHANGE COLUMN date_news created_at DATETIME,
    CHANGE COLUMN view_news views INT DEFAULT 0,
    CHANGE COLUMN image_news featured_image VARCHAR(255);
    </pre>
    
    <div class="action-box" style="background: #fff3cd; border-color: #ffc107;">
        <h3>⚠️ Important Notes</h3>
        <ul>
            <li>Always backup before making schema changes</li>
            <li>Test on development database first</li>
            <li>Update application code after schema changes</li>
            <li>Run changes during low-traffic periods</li>
        </ul>
    </div>
    <?php
}

mysqli_close($connection);
?>

<p style="margin-top: 40px;">
    <a href="execute-db-standardization.php" style="color: #007bff;">← Back to Actions</a>
</p>

</body>
</html>
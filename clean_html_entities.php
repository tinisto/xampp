<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Set execution time limit
set_time_limit(300); // 5 minutes

echo "<h2>HTML Entities Cleaning Tool</h2>";

// Safety check
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeeba; padding: 20px; border-radius: 5px;'>";
    echo "<h3>⚠️ Warning</h3>";
    echo "<p>This will modify 418 posts in the database by converting HTML entities to UTF-8 characters.</p>";
    echo "<p>Examples of changes:</p>";
    echo "<ul>";
    echo "<li>&amp;ndash; → –</li>";
    echo "<li>&amp;laquo; → «</li>";
    echo "<li>&amp;raquo; → »</li>";
    echo "</ul>";
    echo "<p><strong>A backup will be created first.</strong></p>";
    echo "<p>To proceed, click the button below:</p>";
    echo "<a href='?confirm=yes' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Proceed with Cleaning</a>";
    echo " <a href='/' style='display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>Cancel</a>";
    echo "</div>";
    exit;
}

echo "<h3>Step 1: Creating Backup</h3>";

// Create backup table
$backup_table = "posts_backup_" . date('Ymd_His');
$backup_query = "CREATE TABLE $backup_table LIKE posts";
if (mysqli_query($connection, $backup_query)) {
    echo "✓ Created backup table: $backup_table<br>";
    
    // Copy data
    $copy_query = "INSERT INTO $backup_table SELECT * FROM posts";
    if (mysqli_query($connection, $copy_query)) {
        $count = mysqli_affected_rows($connection);
        echo "✓ Copied $count posts to backup table<br>";
    } else {
        echo "<span style='color:red;'>✗ Error copying data: " . mysqli_error($connection) . "</span><br>";
        exit;
    }
} else {
    echo "<span style='color:red;'>✗ Error creating backup: " . mysqli_error($connection) . "</span><br>";
    exit;
}

echo "<h3>Step 2: Cleaning HTML Entities</h3>";

// Get posts with HTML entities
$select_query = "SELECT id_post, text_post, description_post FROM posts WHERE text_post LIKE '%&%' OR description_post LIKE '%&%'";
$result = mysqli_query($connection, $select_query);

$cleaned_count = 0;
$error_count = 0;
$errors = [];

echo "<div style='height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id_post'];
    
    // Clean text_post
    $cleaned_text = html_entity_decode($row['text_post'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $cleaned_text = mysqli_real_escape_string($connection, $cleaned_text);
    
    // Clean description_post if it exists
    $cleaned_desc = '';
    if (!empty($row['description_post'])) {
        $cleaned_desc = html_entity_decode($row['description_post'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleaned_desc = mysqli_real_escape_string($connection, $cleaned_desc);
    }
    
    // Update post
    $update_query = "UPDATE posts SET 
                     text_post = '$cleaned_text'" . 
                     (!empty($cleaned_desc) ? ", description_post = '$cleaned_desc'" : "") . 
                     " WHERE id_post = $id";
    
    if (mysqli_query($connection, $update_query)) {
        $cleaned_count++;
        echo "✓ Cleaned post ID: $id<br>";
    } else {
        $error_count++;
        $errors[] = "Post ID $id: " . mysqli_error($connection);
        echo "<span style='color:red;'>✗ Error cleaning post ID: $id</span><br>";
    }
    
    // Flush output every 10 posts
    if ($cleaned_count % 10 == 0) {
        ob_flush();
        flush();
    }
}

echo "</div>";

echo "<h3>Summary</h3>";
echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
echo "✓ Successfully cleaned: <strong>$cleaned_count posts</strong><br>";
if ($error_count > 0) {
    echo "✗ Errors: <strong>$error_count posts</strong><br>";
    echo "<details><summary>View errors</summary><pre>" . implode("\n", $errors) . "</pre></details>";
}
echo "Backup table: <strong>$backup_table</strong><br>";
echo "</div>";

// Verify cleaning worked
echo "<h3>Verification</h3>";
$verify_query = "SELECT COUNT(*) as remaining FROM posts WHERE text_post LIKE '%&ndash;%' OR text_post LIKE '%&laquo;%' OR text_post LIKE '%&raquo;%'";
$verify_result = mysqli_query($connection, $verify_query);
$verify_row = mysqli_fetch_assoc($verify_result);

if ($verify_row['remaining'] == 0) {
    echo "<p style='color:green;'>✓ All common HTML entities have been cleaned!</p>";
} else {
    echo "<p style='color:orange;'>⚠️ {$verify_row['remaining']} posts still contain common HTML entities.</p>";
}

echo "<h3>Next Steps</h3>";
echo "<ul>";
echo "<li>Test the website to ensure posts display correctly</li>";
echo "<li>If everything works, you can drop the backup table later</li>";
echo "<li>To restore from backup: <code>DROP TABLE posts; RENAME TABLE $backup_table TO posts;</code></li>";
echo "</ul>";

mysqli_close($connection);
?>
<?php
// Fix remaining issues after database migration
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Fixing Remaining Issues</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// 1. Check and fix news approval
echo "<h2>1. News Approval Issue</h2>";
$unapproved_news = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 0")->fetch_assoc()['count'];
$approved_news = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 1")->fetch_assoc()['count'];

echo "<p>Unapproved news: $unapproved_news</p>";
echo "<p>Approved news: $approved_news</p>";

if ($approved_news == 0 && $unapproved_news > 0) {
    if (isset($_GET['approve_news']) && $_GET['approve_news'] == 'yes') {
        // Approve some recent news
        $approve_query = "UPDATE news SET is_approved = 1 WHERE is_approved = 0 ORDER BY created_at DESC LIMIT 20";
        if ($connection->query($approve_query)) {
            $affected = $connection->affected_rows;
            echo "<p style='color: green;'>✅ Approved $affected news articles</p>";
        }
    } else {
        echo "<p><a href='?approve_news=yes'>Approve 20 Recent News Articles</a></p>";
    }
}

// 2. Check posts table structure
echo "<h2>2. Posts Table Check</h2>";
$posts_columns = $connection->query("SHOW COLUMNS FROM posts");
if ($posts_columns) {
    $has_title = false;
    $has_is_published = false;
    while ($col = $posts_columns->fetch_assoc()) {
        if ($col['Field'] == 'title') $has_title = true;
        if ($col['Field'] == 'is_published') $has_is_published = true;
    }
    
    if (!$has_title) {
        echo "<p style='color: red;'>❌ Posts table missing 'title' column</p>";
        // Add title column
        if (isset($_GET['fix_posts']) && $_GET['fix_posts'] == 'yes') {
            $add_title = "ALTER TABLE posts ADD COLUMN title VARCHAR(255) AFTER id";
            if ($connection->query($add_title)) {
                echo "<p style='color: green;'>✅ Added title column to posts</p>";
            }
        }
    } else {
        echo "<p style='color: green;'>✅ Posts table has title column</p>";
    }
    
    if ($has_is_published) {
        $published_posts = $connection->query("SELECT COUNT(*) as count FROM posts WHERE is_published = 1")->fetch_assoc()['count'];
        $unpublished_posts = $connection->query("SELECT COUNT(*) as count FROM posts WHERE is_published = 0")->fetch_assoc()['count'];
        echo "<p>Published posts: $published_posts</p>";
        echo "<p>Unpublished posts: $unpublished_posts</p>";
        
        if ($published_posts == 0 && $unpublished_posts > 0) {
            if (isset($_GET['publish_posts']) && $_GET['publish_posts'] == 'yes') {
                $publish_query = "UPDATE posts SET is_published = 1 WHERE is_published = 0 LIMIT 20";
                if ($connection->query($publish_query)) {
                    $affected = $connection->affected_rows;
                    echo "<p style='color: green;'>✅ Published $affected posts</p>";
                }
            } else {
                echo "<p><a href='?publish_posts=yes'>Publish 20 Recent Posts</a></p>";
            }
        }
    }
}

// 3. Check routing issues
echo "<h2>3. Routing Issues</h2>";
echo "<p>Checking .htaccess for /posts route...</p>";
$htaccess_path = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
    if (strpos($htaccess_content, 'RewriteRule ^posts/?$') !== false) {
        echo "<p style='color: green;'>✅ Posts route exists in .htaccess</p>";
    } else {
        echo "<p style='color: red;'>❌ Posts route missing in .htaccess</p>";
    }
}

// 4. Check VPO/SPO pages
echo "<h2>4. VPO/SPO Pages Issue</h2>";
echo "<p>The error 'Ошибка загрузки данных' suggests the AJAX calls or includes are failing.</p>";

// Check if the content files exist
$vpo_content = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php';
if (file_exists($vpo_content)) {
    echo "<p style='color: green;'>✅ VPO content file exists</p>";
    
    // Check for region/area column mismatch
    $check_query = "SELECT column_name FROM information_schema.columns 
                    WHERE table_schema = '11klassniki_claude' 
                    AND table_name = 'universities' 
                    AND column_name IN ('region_id', 'id_region')";
    $col_result = $connection->query($check_query);
    if ($col_result && $col_result->num_rows > 0) {
        $col = $col_result->fetch_assoc()['column_name'];
        echo "<p>Universities table uses column: <strong>$col</strong></p>";
    }
} else {
    echo "<p style='color: red;'>❌ VPO content file missing</p>";
}

// 5. Registration form issue
echo "<h2>5. Registration Form</h2>";
$reg_form = $_SERVER['DOCUMENT_ROOT'] . '/pages/registration/registration_form.php';
if (file_exists($reg_form)) {
    echo "<p style='color: green;'>✅ Registration form file exists</p>";
} else {
    echo "<p style='color: red;'>❌ Registration form file missing</p>";
}

echo "<hr>";
echo "<h2>Quick Fixes Available:</h2>";
echo "<ul>";
if ($approved_news == 0) echo "<li><a href='?approve_news=yes'>Approve 20 News Articles</a></li>";
if (isset($published_posts) && $published_posts == 0) echo "<li><a href='?publish_posts=yes'>Publish 20 Posts</a></li>";
if (!isset($has_title) || !$has_title) echo "<li><a href='?fix_posts=yes'>Fix Posts Table Structure</a></li>";
echo "</ul>";

echo "<p><a href='/site_review.php'>← Back to Site Review</a></p>";

$connection->close();
?>
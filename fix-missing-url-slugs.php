<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Fix Missing URL Slugs</h1>";

// Function to create a URL slug from text
function createSlug($text) {
    // Convert to lowercase
    $slug = mb_strtolower($text, 'UTF-8');
    
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9а-я]+/u', '-', $slug);
    
    // Remove multiple hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Trim hyphens from beginning and end
    $slug = trim($slug, '-');
    
    return $slug;
}

// Check posts with missing url_slug
$query = "SELECT id, title, url_post, url_slug FROM posts 
          WHERE url_slug IS NULL OR url_slug = ''";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h2>Found " . mysqli_num_rows($result) . " posts with missing url_slug</h2>";
    
    $updated = 0;
    $errors = 0;
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>Old url_post</th><th>New url_slug</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_post'] ?? '') . "</td>";
        
        // Determine the new slug
        $newSlug = '';
        if (!empty($row['url_post'])) {
            // Use existing url_post if available
            $newSlug = $row['url_post'];
        } elseif (!empty($row['title'])) {
            // Generate from title
            $newSlug = createSlug($row['title']);
            
            // Ensure uniqueness
            $checkQuery = "SELECT COUNT(*) as count FROM posts WHERE url_slug = ?";
            $checkStmt = mysqli_prepare($connection, $checkQuery);
            $tempSlug = $newSlug;
            $counter = 1;
            
            do {
                mysqli_stmt_bind_param($checkStmt, 's', $tempSlug);
                mysqli_stmt_execute($checkStmt);
                $checkResult = mysqli_stmt_get_result($checkStmt);
                $count = mysqli_fetch_assoc($checkResult)['count'];
                
                if ($count > 0) {
                    $tempSlug = $newSlug . '-' . $counter;
                    $counter++;
                }
            } while ($count > 0);
            
            $newSlug = $tempSlug;
            mysqli_stmt_close($checkStmt);
        }
        
        echo "<td>" . htmlspecialchars($newSlug) . "</td>";
        
        // Update the post
        if (!empty($newSlug)) {
            $updateQuery = "UPDATE posts SET url_slug = ? WHERE id = ?";
            $updateStmt = mysqli_prepare($connection, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'si', $newSlug, $row['id']);
            
            if (mysqli_stmt_execute($updateStmt)) {
                echo "<td style='color: green;'>✓ Updated</td>";
                $updated++;
            } else {
                echo "<td style='color: red;'>✗ Error: " . mysqli_error($connection) . "</td>";
                $errors++;
            }
            mysqli_stmt_close($updateStmt);
        } else {
            echo "<td style='color: orange;'>⚠ No slug generated</td>";
            $errors++;
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Summary:</h3>";
    echo "<p>Successfully updated: $updated posts</p>";
    echo "<p>Errors: $errors</p>";
    
} else {
    echo "<p style='color: green;'>✓ All posts already have url_slug values!</p>";
}

// Verify the fix
echo "<h2>Verification:</h2>";
$query = "SELECT COUNT(*) as total, 
          COUNT(url_slug) as with_slug, 
          COUNT(CASE WHEN url_slug IS NULL OR url_slug = '' THEN 1 END) as without_slug 
          FROM posts";
$result = mysqli_query($connection, $query);

if ($result) {
    $stats = mysqli_fetch_assoc($result);
    echo "<p>Total posts: {$stats['total']}</p>";
    echo "<p>Posts with url_slug: {$stats['with_slug']}</p>";
    echo "<p>Posts without url_slug: {$stats['without_slug']}</p>";
}

mysqli_close($connection);
?>
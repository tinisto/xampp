<?php
// Check posts table and generate slugs if needed
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Check Posts and Generate Slugs</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Check posts table structure
    echo "<h2>📊 Posts Table Structure</h2>";
    $structure = $connection->query("SHOW COLUMNS FROM posts");
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
    }
    echo "</table>";
    
    // Check how many posts exist
    echo "<h2>📋 Posts Count</h2>";
    $total_posts = $connection->query("SELECT COUNT(*) as total FROM posts");
    $total = $total_posts->fetch_assoc()['total'];
    echo "<p>Total posts: {$total}</p>";
    
    if ($total > 0) {
        // Show sample posts
        echo "<h3>Sample Posts:</h3>";
        $sample = $connection->query("SELECT id, title, url_slug FROM posts ORDER BY id DESC LIMIT 10");
        echo "<table border='1'><tr><th>ID</th><th>Title</th><th>URL Slug</th></tr>";
        while ($row = $sample->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>" . htmlspecialchars(substr($row['title'], 0, 50)) . "</td><td>" . ($row['url_slug'] ?: 'NULL') . "</td></tr>";
        }
        echo "</table>";
        
        // Count posts without slugs
        $no_slugs = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_slug IS NULL OR url_slug = ''");
        $missing_slugs = $no_slugs->fetch_assoc()['count'];
        echo "<p>Posts without slugs: {$missing_slugs}</p>";
        
        if ($missing_slugs > 0) {
            echo "<h2>🔧 Generating URL Slugs</h2>";
            
            // Function to create slug from title
            function createSlug($title) {
                // Convert to lowercase
                $slug = mb_strtolower($title, 'UTF-8');
                
                // Replace Russian characters with Latin equivalents
                $ru_to_en = [
                    'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
                    'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
                    'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
                    'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
                    'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
                    'ш' => 'sh', 'щ' => 'sch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu',
                    'я' => 'ya', 'ь' => '', 'ъ' => ''
                ];
                
                $slug = strtr($slug, $ru_to_en);
                
                // Remove special characters and replace spaces with hyphens
                $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
                $slug = preg_replace('/\s+/', '-', trim($slug));
                $slug = preg_replace('/-+/', '-', $slug);
                $slug = trim($slug, '-');
                
                // Limit length
                return substr($slug, 0, 100);
            }
            
            // Generate slugs for posts without them
            $posts_to_update = $connection->query("SELECT id, title FROM posts WHERE url_slug IS NULL OR url_slug = '' LIMIT 50");
            
            $updated = 0;
            while ($post = $posts_to_update->fetch_assoc()) {
                $slug = createSlug($post['title']);
                
                // Make sure slug is unique
                $check = $connection->query("SELECT id FROM posts WHERE url_slug = '{$slug}' AND id != {$post['id']}");
                if ($check->num_rows > 0) {
                    $slug .= '-' . $post['id'];
                }
                
                $update_sql = "UPDATE posts SET url_slug = '{$slug}' WHERE id = {$post['id']}";
                if ($connection->query($update_sql)) {
                    echo "<p>✅ Generated slug for post {$post['id']}: {$slug}</p>";
                    $updated++;
                } else {
                    echo "<p>❌ Failed to update post {$post['id']}: " . $connection->error . "</p>";
                }
            }
            
            echo "<p><strong>Updated {$updated} posts with slugs</strong></p>";
        }
        
        // Show posts after slug generation
        echo "<h2>📋 Posts with Slugs (After Update)</h2>";
        $with_slugs = $connection->query("SELECT id, title, url_slug FROM posts WHERE url_slug IS NOT NULL AND url_slug != '' ORDER BY id DESC LIMIT 10");
        if ($with_slugs->num_rows > 0) {
            echo "<table border='1'><tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Test Link</th></tr>";
            while ($row = $with_slugs->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>" . htmlspecialchars(substr($row['title'], 0, 40)) . "</td>";
                echo "<td>{$row['url_slug']}</td>";
                echo "<td><a href='/post/{$row['url_slug']}' target='_blank'>Test</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
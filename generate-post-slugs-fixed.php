<?php
// Generate slugs for posts (corrected field names)
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Generate Post Slugs - Fixed</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Check posts count
    $total_posts = $connection->query("SELECT COUNT(*) as total FROM posts");
    if ($total_posts) {
        $total = $total_posts->fetch_assoc()['total'];
        echo "<p>Total posts: {$total}</p>";
    }
    
    // Show sample posts with correct field names
    echo "<h2>📋 Sample Posts</h2>";
    $sample_query = "SELECT id, title_post, url_slug FROM posts ORDER BY id DESC LIMIT 10";
    $sample = $connection->query($sample_query);
    
    if ($sample && $sample->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th></tr>";
        while ($row = $sample->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars(substr($row['title_post'], 0, 50)) . "...</td>";
            echo "<td>" . ($row['url_slug'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Count posts without slugs
    $no_slugs_query = "SELECT COUNT(*) as count FROM posts WHERE url_slug IS NULL OR url_slug = ''";
    $no_slugs = $connection->query($no_slugs_query);
    if ($no_slugs) {
        $missing_slugs = $no_slugs->fetch_assoc()['count'];
        echo "<p><strong>Posts without slugs: {$missing_slugs}</strong></p>";
        
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
            
            // Generate slugs for first 20 posts without them
            $posts_query = "SELECT id, title_post FROM posts WHERE url_slug IS NULL OR url_slug = '' ORDER BY id DESC LIMIT 20";
            $posts_to_update = $connection->query($posts_query);
            
            $updated = 0;
            if ($posts_to_update && $posts_to_update->num_rows > 0) {
                while ($post = $posts_to_update->fetch_assoc()) {
                    $slug = createSlug($post['title_post']);
                    
                    if (empty($slug)) {
                        $slug = 'post-' . $post['id'];
                    }
                    
                    // Make sure slug is unique
                    $check_query = "SELECT id FROM posts WHERE url_slug = '{$slug}' AND id != {$post['id']}";
                    $check = $connection->query($check_query);
                    if ($check && $check->num_rows > 0) {
                        $slug .= '-' . $post['id'];
                    }
                    
                    $update_query = "UPDATE posts SET url_slug = '{$slug}' WHERE id = {$post['id']}";
                    if ($connection->query($update_query)) {
                        echo "<p>✅ Generated slug for post {$post['id']}: <strong>{$slug}</strong></p>";
                        $updated++;
                    } else {
                        echo "<p>❌ Failed to update post {$post['id']}: " . $connection->error . "</p>";
                    }
                }
            }
            
            echo "<p><strong>✅ Updated {$updated} posts with slugs</strong></p>";
        }
    }
    
    // Show posts with slugs after generation
    echo "<h2>🎉 Posts with Slugs (Ready for Testing)</h2>";
    $final_query = "SELECT id, title_post, url_slug FROM posts WHERE url_slug IS NOT NULL AND url_slug != '' ORDER BY id DESC LIMIT 10";
    $with_slugs = $connection->query($final_query);
    
    if ($with_slugs && $with_slugs->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Test Comment Link</th></tr>";
        while ($row = $with_slugs->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars(substr($row['title_post'], 0, 40)) . "...</td>";
            echo "<td>{$row['url_slug']}</td>";
            echo "<td><a href='/post/{$row['url_slug']}' target='_blank'>Test Comments</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h2>🎯 Ready to Test!</h2>";
        echo "<p>Click any 'Test Comments' link above to test the comment system.</p>";
        echo "<p>The URL field standardization is complete and posts now have slugs!</p>";
    } else {
        echo "<p>❌ No posts with slugs found after generation</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
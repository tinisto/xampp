<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Debug Post Images Issue</h1>";

// Get the specific post that's having issues
$post_url = 'kogda-ege-ostalis-pozadi';

echo "<h2>Testing post: {$post_url}</h2>";

// Query the post using the new field name (id instead of id_post)
$result = $connection->query("SELECT * FROM posts WHERE url_slug = '$post_url'");

if ($result && $result->num_rows > 0) {
    $post = $result->fetch_assoc();
    echo "✅ Post found with ID: " . $post['id'] . "<br>";
    echo "Title: " . $post['title_post'] . "<br>";
    
    echo "<h3>Image fields in this post:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Value</th><th>Status</th></tr>";
    
    $image_fields = [
        'image_post' => $post['image_post'] ?? '',
        'image_post_1' => $post['image_post_1'] ?? '',
        'image_post_2' => $post['image_post_2'] ?? '',
        'image_post_3' => $post['image_post_3'] ?? '',
        'photo_town_one' => $post['photo_town_one'] ?? '',
        'photo_town_two' => $post['photo_town_two'] ?? '',
        'photo_town_three' => $post['photo_town_three'] ?? '',
    ];
    
    foreach ($image_fields as $field => $value) {
        $status = empty($value) ? "❌ Empty" : "✅ Has value";
        echo "<tr><td>{$field}</td><td>" . htmlspecialchars($value) . "</td><td>{$status}</td></tr>";
    }
    echo "</table>";
    
    // Check if image files actually exist
    echo "<h3>Checking if image files exist:</h3>";
    foreach ($image_fields as $field => $filename) {
        if (!empty($filename)) {
            // Common image paths
            $possible_paths = [
                $_SERVER['DOCUMENT_ROOT'] . '/images/posts/' . $filename,
                $_SERVER['DOCUMENT_ROOT'] . '/images/' . $filename,
                $_SERVER['DOCUMENT_ROOT'] . '/uploads/posts/' . $filename,
                $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $filename,
            ];
            
            $found = false;
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    echo "✅ {$field}: Found at " . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path) . "<br>";
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                echo "❌ {$field}: File '{$filename}' not found in common image directories<br>";
            }
        }
    }
} else {
    echo "❌ Post not found with URL: {$post_url}<br>";
    
    // Check if there are any posts at all
    $count_result = $connection->query("SELECT COUNT(*) as count FROM posts");
    $total_posts = $count_result->fetch_assoc()['count'];
    echo "Total posts in database: {$total_posts}<br>";
    
    // Show a few sample posts
    $sample_result = $connection->query("SELECT id, title_post, url_post FROM posts LIMIT 5");
    echo "<h3>Sample posts:</h3>";
    while ($row = $sample_result->fetch_assoc()) {
        echo "ID: {$row['id']}, Title: {$row['title_post']}, URL: {$row['url_post']}<br>";
    }
}

// Check common image directories
echo "<h2>Common image directories:</h2>";
$image_dirs = [
    '/images',
    '/images/posts',
    '/uploads',
    '/uploads/posts',
    '/assets/images',
];

foreach ($image_dirs as $dir) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $dir;
    if (is_dir($full_path)) {
        $files = array_slice(scandir($full_path), 2, 10); // First 10 files
        echo "✅ {$dir}: " . count($files) . " files (showing first 10)<br>";
        echo "&nbsp;&nbsp;&nbsp;" . implode(', ', $files) . "<br>";
    } else {
        echo "❌ {$dir}: Directory not found<br>";
    }
}
?>
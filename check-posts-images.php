<?php
echo "<h1>Check Posts Images Directory</h1>";

$posts_images_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/posts-images';

if (is_dir($posts_images_dir)) {
    echo "<h2>✅ /images/posts-images directory exists</h2>";
    
    $files = scandir($posts_images_dir);
    $image_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..', '.DS_Store']) && 
               preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
    });
    
    echo "<p>Total image files: " . count($image_files) . "</p>";
    
    echo "<h3>Image files (first 20):</h3>";
    echo "<ul>";
    $count = 0;
    foreach ($image_files as $file) {
        if ($count >= 20) break;
        echo "<li>{$file}</li>";
        $count++;
    }
    echo "</ul>";
    
    // Check specifically for the missing files
    echo "<h3>Checking for specific missing files:</h3>";
    $missing_files = ['124_1.jpg', '124_2.jpg', '124_3.jpg'];
    foreach ($missing_files as $file) {
        if (file_exists($posts_images_dir . '/' . $file)) {
            echo "✅ {$file} - Found<br>";
        } else {
            echo "❌ {$file} - Missing<br>";
        }
    }
    
} else {
    echo "<h2>❌ /images/posts-images directory not found</h2>";
}
?>
<?php
/**
 * Fix missing URL slugs in posts table
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🔧 Fix Missing Post URL Slugs</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

function generateSlug($title) {
    // Convert to lowercase
    $slug = mb_strtolower($title, 'UTF-8');
    
    // Replace Russian characters with transliteration
    $russian = [
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
        'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
    ];
    
    $english = [
        'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p',
        'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya'
    ];
    
    $slug = str_replace($russian, $english, $slug);
    
    // Remove special characters and replace spaces/multiple dashes with single dash
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
    $slug = preg_replace('/\-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Limit length to 100 characters
    if (strlen($slug) > 100) {
        $slug = substr($slug, 0, 100);
        $slug = rtrim($slug, '-');
    }
    
    return $slug;
}

// Check how many posts need fixing
$posts_without_url = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_post IS NULL OR url_post = ''");
$missing_count = $posts_without_url ? $posts_without_url->fetch_assoc()['count'] : 0;

echo "<p><strong>Posts missing URL slugs:</strong> $missing_count</p>";

if ($missing_count == 0) {
    echo "<p class='success'>✅ All posts already have URL slugs!</p>";
    echo "<p><a href='/'>← Back to Home</a></p>";
    exit;
}

// Get posts that need fixing
$posts_to_fix = $connection->query("
    SELECT id_post, title_post 
    FROM posts 
    WHERE url_post IS NULL OR url_post = '' 
    ORDER BY created_at DESC
");

if (!$posts_to_fix) {
    echo "<p class='error'>❌ Could not retrieve posts to fix</p>";
    exit;
}

echo "<h2>🛠️ Generating URL Slugs</h2>";
echo "<table>";
echo "<tr><th>ID</th><th>Title</th><th>Generated Slug</th><th>Status</th></tr>";

$fixed_count = 0;
$error_count = 0;

while ($row = $posts_to_fix->fetch_assoc()) {
    $id_post = $row['id_post'];
    $title = $row['title_post'];
    $generated_slug = generateSlug($title);
    
    echo "<tr>";
    echo "<td>$id_post</td>";
    echo "<td>" . htmlspecialchars(substr($title, 0, 50)) . "...</td>";
    echo "<td>$generated_slug</td>";
    
    // Check if this slug already exists
    $slug_check = $connection->prepare("SELECT id_post FROM posts WHERE url_post = ? AND id_post != ?");
    $slug_check->bind_param("si", $generated_slug, $id_post);
    $slug_check->execute();
    $result = $slug_check->get_result();
    
    if ($result->num_rows > 0) {
        // Slug exists, make it unique by adding ID
        $generated_slug = $generated_slug . '-' . $id_post;
        echo "<td class='warning'>⚠️ Made unique: $generated_slug</td>";
    } else {
        echo "<td class='info'>ℹ️ Original slug used</td>";
    }
    
    // Update the post with the generated slug
    $update_stmt = $connection->prepare("UPDATE posts SET url_post = ? WHERE id_post = ?");
    $update_stmt->bind_param("si", $generated_slug, $id_post);
    
    if ($update_stmt->execute()) {
        $fixed_count++;
        echo "<td class='success'>✅ Fixed</td>";
    } else {
        $error_count++;
        echo "<td class='error'>❌ Error: " . $connection->error . "</td>";
    }
    
    echo "</tr>";
    
    $slug_check->close();
    $update_stmt->close();
}

echo "</table>";

echo "<h2>📊 Results</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li class='success'>✅ Fixed: $fixed_count posts</li>";
if ($error_count > 0) {
    echo "<li class='error'>❌ Errors: $error_count posts</li>";
}
echo "<li>Total processed: " . ($fixed_count + $error_count) . " posts</li>";
echo "</ul>";

if ($fixed_count > 0) {
    echo "<p class='success'><strong>✅ Post URL routing should now be working!</strong></p>";
    echo "<p>Posts can now be accessed via: <code>/post/{url_slug}</code></p>";
}

echo "</div>";

// Verify the fix worked
$remaining_missing = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_post IS NULL OR url_post = ''");
$remaining_count = $remaining_missing ? $remaining_missing->fetch_assoc()['count'] : 0;

if ($remaining_count == 0) {
    echo "<p class='success'>🎉 All posts now have URL slugs!</p>";
} else {
    echo "<p class='warning'>⚠️ $remaining_count posts still missing URL slugs</p>";
}

echo "<p><a href='/'>← Back to Home</a></p>";
echo "<p><a href='/debug_posts_routing.php'>🔍 Debug Posts Routing</a></p>";

$connection->close();
?>
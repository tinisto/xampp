<?php
/**
 * Debug and fix post URL routing issues
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🔍 Debug Posts Routing Issues</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; }
</style>";

// Check posts table structure
echo "<h2>1️⃣ Posts Table Structure</h2>";
$columns_result = $connection->query("SHOW COLUMNS FROM posts");
if ($columns_result) {
    echo "<table>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $columns_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Could not get table structure</p>";
}

// Check total posts count
echo "<h2>2️⃣ Posts Data Overview</h2>";
$total_posts = $connection->query("SELECT COUNT(*) as count FROM posts");
$total_count = $total_posts ? $total_posts->fetch_assoc()['count'] : 0;

echo "<p><strong>Total posts:</strong> $total_count</p>";

// Check posts without URL slugs
$posts_without_url = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_post IS NULL OR url_post = ''");
$missing_urls = $posts_without_url ? $posts_without_url->fetch_assoc()['count'] : 0;

echo "<p><strong>Posts without url_post:</strong> $missing_urls</p>";

if ($missing_urls > 0) {
    echo "<p class='error'>❌ Found $missing_urls posts without url_post - this will cause 404 errors</p>";
    
    // Show sample of posts without URLs
    echo "<h3>Sample Posts Without URL Slugs:</h3>";
    $sample_missing = $connection->query("SELECT id_post, title_post, created_at FROM posts WHERE url_post IS NULL OR url_post = '' LIMIT 10");
    if ($sample_missing) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Created</th></tr>";
        while ($row = $sample_missing->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id_post']}</td>";
            echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p class='success'>✅ All posts have url_post</p>";
}

// Check for duplicate URL slugs
echo "<h2>3️⃣ Duplicate URL Slugs Check</h2>";
$duplicate_urls = $connection->query("
    SELECT url_post, COUNT(*) as count 
    FROM posts 
    WHERE url_post IS NOT NULL AND url_post != ''
    GROUP BY url_post 
    HAVING COUNT(*) > 1
");

if ($duplicate_urls && $duplicate_urls->num_rows > 0) {
    echo "<p class='warning'>⚠️ Found duplicate URL slugs:</p>";
    echo "<table>";
    echo "<tr><th>URL Slug</th><th>Count</th></tr>";
    while ($row = $duplicate_urls->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['url_post']}</td>";
        echo "<td>{$row['count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='success'>✅ No duplicate URL slugs found</p>";
}

// Check specific post URLs mentioned in conversation
echo "<h2>4️⃣ Test Specific Posts</h2>";
$test_posts = [
    'hochu-poblagodarit'
];

echo "<table>";
echo "<tr><th>URL Slug</th><th>Exists in DB</th><th>Post Title</th><th>Created</th></tr>";

foreach ($test_posts as $url_slug) {
    echo "<tr>";
    echo "<td>$url_slug</td>";
    
    $post_check = $connection->query("SELECT id_post, title_post, created_at FROM posts WHERE url_post = '$url_slug'");
    if ($post_check && $post_check->num_rows > 0) {
        $post = $post_check->fetch_assoc();
        echo "<td class='success'>✅ Yes</td>";
        echo "<td>" . htmlspecialchars($post['title_post']) . "</td>";
        echo "<td>{$post['created_at']}</td>";
    } else {
        echo "<td class='error'>❌ No</td>";
        echo "<td colspan='2'>Not found</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Check recent posts to see if any have missing URLs
echo "<h2>5️⃣ Recent Posts Analysis</h2>";
$recent_posts = $connection->query("
    SELECT id_post, title_post, url_post, created_at 
    FROM posts 
    ORDER BY created_at DESC 
    LIMIT 20
");

if ($recent_posts) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Created</th><th>Status</th></tr>";
    while ($row = $recent_posts->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id_post']}</td>";
        echo "<td>" . htmlspecialchars(substr($row['title_post'], 0, 50)) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_post'] ?? 'NULL') . "</td>";
        echo "<td>{$row['created_at']}</td>";
        
        if (empty($row['url_post'])) {
            echo "<td class='error'>❌ Missing URL</td>";
        } else {
            echo "<td class='success'>✅ OK</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Generate URL slugs for posts missing them
if ($missing_urls > 0) {
    echo "<h2>6️⃣ Fix Missing URL Slugs</h2>";
    echo "<p>Generate URL slugs for posts that are missing them...</p>";
    
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
        
        return $slug;
    }
    
    // Get posts without URL slugs and generate them
    $posts_to_fix = $connection->query("SELECT id_post, title_post FROM posts WHERE url_post IS NULL OR url_post = '' LIMIT 10");
    
    if ($posts_to_fix) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Generated Slug</th><th>Action</th></tr>";
        
        while ($row = $posts_to_fix->fetch_assoc()) {
            $generated_slug = generateSlug($row['title_post']);
            
            echo "<tr>";
            echo "<td>{$row['id_post']}</td>";
            echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
            echo "<td>$generated_slug</td>";
            echo "<td>";
            
            // Check if this slug already exists
            $slug_exists = $connection->query("SELECT id_post FROM posts WHERE url_post = '$generated_slug'");
            if ($slug_exists && $slug_exists->num_rows > 0) {
                // Add ID to make it unique
                $generated_slug = $generated_slug . '-' . $row['id_post'];
                echo "<span class='warning'>⚠️ Slug exists, using: $generated_slug</span>";
            } else {
                echo "<span class='success'>✅ Unique slug</span>";
            }
            
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><strong>Would you like to automatically generate URL slugs for all posts missing them?</strong></p>";
        echo "<p>This will help fix the 404 errors you're experiencing.</p>";
    }
}

echo "<h2>📋 Summary</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Findings:</strong></p>";
echo "<ul>";
if ($missing_urls > 0) {
    echo "<li class='error'>$missing_urls posts are missing url_post values - this causes 404 errors</li>";
}
echo "<li>Total posts in database: $total_count</li>";
echo "<li>Post routing uses: /post/{url_post}</li>";
echo "</ul>";

if ($missing_urls > 0) {
    echo "<p><strong>Recommended Action:</strong></p>";
    echo "<p>Generate URL slugs for all posts missing them to fix the 404 errors.</p>";
}
echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
?>
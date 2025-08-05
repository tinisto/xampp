<?php
// Fix all post-related field references
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix All Post Field References</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Check current posts table structure
    echo "<h2>📊 Current Posts Table Structure</h2>";
    $result = $connection->query("SHOW COLUMNS FROM posts");
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
    
    // Check if we have 'id' or 'id_post'
    $id_field_check = $connection->query("SHOW COLUMNS FROM posts LIKE 'id'");
    $id_post_check = $connection->query("SHOW COLUMNS FROM posts LIKE 'id_post'");
    
    $current_id_field = 'id';
    if ($id_post_check->num_rows > 0) {
        $current_id_field = 'id_post';
        echo "<p>⚠️ Posts table is using 'id_post' field</p>";
    } else if ($id_field_check->num_rows > 0) {
        echo "<p>✅ Posts table is using 'id' field</p>";
    }
    
    echo "<h2>📝 Files to Update</h2>";
    
    $files_to_fix = [
        '/pages/post/post-content-professional.php' => [
            "UPDATE posts SET view_post = \$updatedViews WHERE id_post = " => "UPDATE posts SET view_post = \$updatedViews WHERE {$current_id_field} = ",
            "\$rowPost['id_post']" => "\$rowPost['{$current_id_field}']",
            "htmlspecialchars(\$rowPost['id_post'])" => "htmlspecialchars(\$rowPost['{$current_id_field}'])",
        ],
        '/comments/modern-comments-component.php' => [
            "SELECT id_post FROM posts WHERE url_slug = ?" => "SELECT {$current_id_field} FROM posts WHERE url_slug = ?",
            "\$post['id_post']" => "\$post['{$current_id_field}']",
        ]
    ];
    
    foreach ($files_to_fix as $file => $replacements) {
        $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
        if (file_exists($full_path)) {
            echo "<h3>Fixing: {$file}</h3>";
            $content = file_get_contents($full_path);
            $original_content = $content;
            
            foreach ($replacements as $search => $replace) {
                $count = 0;
                $content = str_replace($search, $replace, $content, $count);
                if ($count > 0) {
                    echo "<p>✅ Replaced '{$search}' with '{$replace}' ({$count} occurrences)</p>";
                }
            }
            
            if ($content !== $original_content) {
                file_put_contents($full_path, $content);
                echo "<p>✅ File updated successfully</p>";
            } else {
                echo "<p>⚠️ No changes needed</p>";
            }
        } else {
            echo "<p>❌ File not found: {$file}</p>";
        }
    }
    
    // Test the fix
    echo "<h2>🧪 Testing Fix</h2>";
    $test_slug = 'kuda-dvigatsya-posle-shkoly';
    $test_query = "SELECT {$current_id_field} as id, title_post FROM posts WHERE url_slug = '{$test_slug}'";
    $test_result = $connection->query($test_query);
    
    if ($test_result && $test_result->num_rows > 0) {
        $post = $test_result->fetch_assoc();
        echo "<p>✅ Test post found: ID {$post['id']}, Title: " . htmlspecialchars($post['title_post']) . "</p>";
        echo "<p>🎉 Ready to test comments at: <a href='/post/{$test_slug}' target='_blank'>/post/{$test_slug}</a></p>";
    } else {
        echo "<p>❌ Test post not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
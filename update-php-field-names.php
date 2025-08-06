<?php
echo "<h1>Update PHP Files to Use New Field Names</h1>";
echo "<pre>";

// Field name mappings
$replacements = [
    'meta_d_post' => 'meta_description',
    'meta_k_post' => null, // Remove
    'meta_d_news' => 'meta_description', 
    'meta_k_news' => null, // Remove
    'meta_d_vpo' => 'meta_description',
    'meta_k_vpo' => null, // Remove
    'meta_d_spo' => 'meta_description',
    'meta_k_spo' => null, // Remove
];

// Files to update (excluding the standardization script itself)
$files = [
    '/pages/post/post.php',
    '/pages/post/post-data-fetch.php',
    '/pages/common/news/news-data-fetch.php',
    '/pages/common/news/news-form-content.php',
    '/pages/common/news/news-process.php',
    '/pages/common/create-process.php',
    '/pages/common/create-form.php',
    '/pages/dashboard/posts-dashboard/posts-view/posts-view-content.php',
    '/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form-content.php',
    '/pages/dashboard/posts-dashboard/posts-view/posts-view-edit-form-process.php',
    '/pages/dashboard/posts-dashboard/posts-create/posts-create-process.php',
    '/pages/dashboard/posts-dashboard/posts-create/posts-create-form.php'
];

$updatedFiles = [];

foreach ($files as $file) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    
    if (!file_exists($fullPath)) {
        echo "File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    $originalContent = $content;
    $changes = [];
    
    foreach ($replacements as $old => $new) {
        if ($new === null) {
            // Remove references to this field
            continue;
        }
        
        // Count replacements
        $count = 0;
        
        // Replace in SQL queries (both with backticks and without)
        $content = str_replace("`$old`", "`$new`", $content, $count1);
        $content = str_replace("'$old'", "'$new'", $content, $count2);
        $content = str_replace('"' . $old . '"', '"' . $new . '"', $content, $count3);
        
        // Replace in array access
        $content = str_replace("['$old']", "['$new']", $content, $count4);
        $content = str_replace('["' . $old . '"]', '["' . $new . '"]', $content, $count5);
        
        // Replace in variable names
        $content = str_replace('$' . $old, '$' . $new, $content, $count6);
        
        $totalCount = $count1 + $count2 + $count3 + $count4 + $count5 + $count6;
        
        if ($totalCount > 0) {
            $changes[] = "$old → $new ($totalCount replacements)";
        }
    }
    
    if ($content !== $originalContent) {
        if (file_put_contents($fullPath, $content)) {
            echo "\n✓ Updated: $file\n";
            foreach ($changes as $change) {
                echo "  - $change\n";
            }
            $updatedFiles[] = $file;
        } else {
            echo "\n✗ Failed to update: $file\n";
        }
    }
}

echo "\n\nSummary:\n";
echo "Updated " . count($updatedFiles) . " files\n";

echo "</pre>";
?>
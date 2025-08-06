<?php
// Fix missing categories and URL issues
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Fix 404 Categories and Posts</h1>";

// 1. Create missing 'ege' category
echo "<h2>1. Creating Missing Categories</h2>";

$categories_to_add = [
    [
        'title_category' => 'ЕГЭ',
        'url_category' => 'ege'
    ],
    [
        'title_category' => 'ОГЭ', 
        'url_category' => 'oge'
    ],
    [
        'title_category' => 'ВПР',
        'url_category' => 'vpr'
    ]
];

foreach ($categories_to_add as $category) {
    // Check if category exists
    $stmt = $connection->prepare("SELECT id_category FROM categories WHERE url_category = ?");
    $stmt->bind_param("s", $category['url_category']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Insert category
        $stmt = $connection->prepare("INSERT INTO categories (title_category, url_category) VALUES (?, ?)");
        $stmt->bind_param("ss", $category['title_category'], $category['url_category']);
        
        if ($stmt->execute()) {
            echo "<p>✓ Created category: {$category['title_category']} ({$category['url_category']})</p>";
        } else {
            echo "<p>✗ Error creating category {$category['title_category']}: " . $connection->error . "</p>";
        }
    } else {
        echo "<p>• Category already exists: {$category['title_category']} ({$category['url_category']})</p>";
    }
}

// 2. Fix posts without url_slug
echo "<h2>2. Fixing Posts Without URL Slugs</h2>";

// First, check if url_post column exists
$result = $connection->query("SHOW COLUMNS FROM posts LIKE 'url_post'");
$has_url_post = ($result && $result->num_rows > 0);

if ($has_url_post) {
    // Copy url_post to url_slug where url_slug is empty
    $update_query = "UPDATE posts SET url_slug = url_post WHERE (url_slug IS NULL OR url_slug = '') AND url_post IS NOT NULL AND url_post != ''";
    if ($connection->query($update_query)) {
        echo "<p>✓ Copied url_post values to url_slug where missing</p>";
        echo "<p>Affected rows: " . $connection->affected_rows . "</p>";
    } else {
        echo "<p>✗ Error updating url_slug: " . $connection->error . "</p>";
    }
}

// Generate slugs from titles for any remaining posts without url_slug
$result = $connection->query("SELECT id_post, title FROM posts WHERE url_slug IS NULL OR url_slug = ''");
if ($result && $result->num_rows > 0) {
    echo "<p>Generating URL slugs for " . $result->num_rows . " posts...</p>";
    
    while ($row = $result->fetch_assoc()) {
        // Generate slug from title
        $slug = generateSlug($row['title']);
        
        // Make sure slug is unique
        $base_slug = $slug;
        $counter = 1;
        while (slugExists($connection, $slug, $row['id_post'])) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }
        
        // Update the post
        $stmt = $connection->prepare("UPDATE posts SET url_slug = ? WHERE id_post = ?");
        $stmt->bind_param("si", $slug, $row['id_post']);
        
        if ($stmt->execute()) {
            echo "<p>✓ Generated slug for post #{$row['id_post']}: {$slug}</p>";
        } else {
            echo "<p>✗ Error updating post #{$row['id_post']}: " . $connection->error . "</p>";
        }
    }
}

// 3. Show test links
echo "<h2>3. Test Links</h2>";
echo "<p>Try these links to verify the fixes:</p>";
echo "<ul>";
echo "<li><a href='/category/ege/' target='_blank'>/category/ege/</a></li>";
echo "<li><a href='/category/oge/' target='_blank'>/category/oge/</a></li>";
echo "<li><a href='/category/vpr/' target='_blank'>/category/vpr/</a></li>";
echo "</ul>";

// Show some sample post links
$result = $connection->query("SELECT title, url_slug FROM posts WHERE url_slug IS NOT NULL AND url_slug != '' LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<p>Sample post links:</p>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href='/post/{$row['url_slug']}/' target='_blank'>{$row['title']}</a></li>";
    }
    echo "</ul>";
}

$connection->close();

// Helper functions
function generateSlug($string) {
    $translit = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
    ];
    
    $string = strtr($string, $translit);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = trim($string, '-');
    
    return $string;
}

function slugExists($connection, $slug, $exclude_id = null) {
    $query = "SELECT id_post FROM posts WHERE url_slug = ?";
    if ($exclude_id) {
        $query .= " AND id_post != ?";
    }
    
    $stmt = $connection->prepare($query);
    if ($exclude_id) {
        $stmt->bind_param("si", $slug, $exclude_id);
    } else {
        $stmt->bind_param("s", $slug);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>
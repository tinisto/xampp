<?php
// Generate manual SQL fixes for 404 errors
echo "<h1>Manual SQL Commands to Fix 404 Errors</h1>";
echo "<p>Copy and paste these SQL commands into your database:</p>";

echo "<h2>1. Create Missing Categories</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";

$categories = [
    ['title' => 'ЕГЭ', 'url' => 'ege'],
    ['title' => 'ОГЭ', 'url' => 'oge'], 
    ['title' => 'ВПР', 'url' => 'vpr']
];

foreach ($categories as $cat) {
    echo "-- Check if category exists first\n";
    echo "SELECT * FROM categories WHERE url_category = '{$cat['url']}';\n";
    echo "-- If not found, insert it:\n";
    echo "INSERT IGNORE INTO categories (title_category, url_category) VALUES ('{$cat['title']}', '{$cat['url']}');\n\n";
}

echo "</pre>";

echo "<h2>2. Fix Posts Without url_slug</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";
echo "-- Check if url_post column exists\n";
echo "SHOW COLUMNS FROM posts LIKE '%url%';\n\n";

echo "-- Copy url_post to url_slug where missing (if url_post column exists)\n";
echo "UPDATE posts SET url_slug = url_post WHERE (url_slug IS NULL OR url_slug = '') AND url_post IS NOT NULL AND url_post != '';\n\n";

echo "-- Check posts without url_slug\n";
echo "SELECT COUNT(*) as posts_without_slug FROM posts WHERE url_slug IS NULL OR url_slug = '';\n\n";

echo "-- Show sample posts that need fixing\n";
echo "SELECT id_post, title_post, url_slug FROM posts WHERE url_slug IS NULL OR url_slug = '' LIMIT 5;\n";
echo "</pre>";

echo "<h2>3. Verification Commands</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";
echo "-- Check all categories\n";
echo "SELECT * FROM categories ORDER BY title_category;\n\n";

echo "-- Check for 'ege' category specifically\n";
echo "SELECT * FROM categories WHERE url_category = 'ege';\n\n";

echo "-- Check posts table structure\n";
echo "DESCRIBE posts;\n\n";

echo "-- Count posts by category (if category field exists)\n";
echo "SELECT c.title_category, c.url_category, COUNT(p.id_post) as post_count\n";
echo "FROM categories c\n";
echo "LEFT JOIN posts p ON c.id_category = p.category\n";
echo "GROUP BY c.id_category, c.title_category, c.url_category\n";
echo "ORDER BY c.title_category;\n";
echo "</pre>";

echo "<h2>4. Test URLs After Fix</h2>";
echo "<ul>";
echo "<li><a href='/category/ege/' target='_blank'>/category/ege/</a></li>";
echo "<li><a href='/category/oge/' target='_blank'>/category/oge/</a></li>";
echo "<li><a href='/category/vpr/' target='_blank'>/category/vpr/</a></li>";
echo "</ul>";

echo "<h2>5. Alternative: Direct Database Access</h2>";
echo "<p>If you have command line access to MySQL:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";
echo "mysql -u root -proot 11klassniki_claude\n";
echo "-- Then paste the SQL commands above\n";
echo "</pre>";
?>
<?php
// Analyze which files still need migration

echo "<h2>Template Migration Status Analysis</h2>";

// List of files using template-engine-ultimate.php
$oldTemplateFiles = [
    '/pages/404/404.php',
    '/pages/404/404-modern.php',
    '/pages/404/404-old.php',
    '/pages/login/login-unified.php',
    '/pages/login/login-modern.php',
    '/pages/login/login-old.php',
    '/pages/registration/registration.php',
    '/pages/registration/registration-with-redirect.php',
    '/pages/search/search.php',
    '/pages/search/search-process.php',
    '/pages/about/about.php',
    '/pages/write/write.php',
    '/pages/post/post-fixed.php',
    '/pages/post/post-minimal.php',
    '/pages/post/post-unified.php',
    '/pages/news/news-main.php',
    '/pages/news/news-working.php',
    '/pages/category/category-simple.php',
    '/pages/school/school-single.php',
    '/pages/account/account.php',
    '/pages/error/error.php',
    '/pages/tests/tests-main.php',
    // Add more as needed
];

// Check which routes are currently active in .htaccess
$htaccessContent = file_get_contents('.htaccess');

echo "<h3>Current Route Status:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Route</th><th>Current Target</th><th>Uses real_template?</th><th>Status</th></tr>";

$routes = [
    ['route' => '/404', 'pattern' => 'ErrorDocument 404 (.+)'],
    ['route' => '/login', 'pattern' => 'RewriteRule \^login/\?\$ ([^ ]+)'],
    ['route' => '/registration', 'pattern' => 'RewriteRule \^registration/\?\$ ([^ ]+)'],
    ['route' => '/search', 'pattern' => 'RewriteRule \^search/\?\$ ([^ ]+)'],
    ['route' => '/about', 'pattern' => 'RewriteRule \^about/\?\$ ([^ ]+)'],
    ['route' => '/write', 'pattern' => 'RewriteRule \^write/\?\$ ([^ ]+)'],
    ['route' => '/news', 'pattern' => 'RewriteRule \^news/\?\$ ([^ ]+)'],
    ['route' => '/category/*', 'pattern' => 'RewriteRule \^category/\[\\^\\/\]\+/\?\$ ([^ ]+)'],
    ['route' => '/post/*', 'pattern' => 'RewriteRule \^post/\[\\^\\/\]\+/\?\$ ([^ ]+)'],
    ['route' => '/tests', 'pattern' => 'RewriteRule \^tests/\?\$ ([^ ]+)'],
];

foreach ($routes as $route) {
    if (preg_match('/' . $route['pattern'] . '/m', $htaccessContent, $matches)) {
        $target = trim($matches[1]);
        $targetPath = '/' . $target;
        
        // Check if target uses real_template
        $usesRealTemplate = false;
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $targetPath)) {
            $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $targetPath);
            $usesRealTemplate = strpos($content, 'real_template.php') !== false;
        }
        
        $status = $usesRealTemplate ? '✅ Migrated' : '❌ Needs migration';
        $statusColor = $usesRealTemplate ? '#d4edda' : '#f8d7da';
        
        echo "<tr>";
        echo "<td>{$route['route']}</td>";
        echo "<td>$target</td>";
        echo "<td>" . ($usesRealTemplate ? 'Yes' : 'No') . "</td>";
        echo "<td style='background: $statusColor;'>$status</td>";
        echo "</tr>";
    }
}
echo "</table>";

// Check for new files that already use real_template
echo "<h3>Already Migrated Files (*-new.php):</h3>";
$newFiles = glob('*-new.php');
$migratedCount = 0;

echo "<ul>";
foreach ($newFiles as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'real_template.php') !== false) {
        echo "<li>✅ $file</li>";
        $migratedCount++;
    }
}
echo "</ul>";

echo "<h3>Summary:</h3>";
echo "<p>Total files using old template: 101</p>";
echo "<p>Already migrated (*-new.php files): $migratedCount</p>";
echo "<p>Estimated remaining: " . (101 - $migratedCount) . "</p>";

echo "<h3>High Priority Pages Still Using Old Template:</h3>";
echo "<ul>";
echo "<li>Error page (/pages/error/error.php)</li>";
echo "<li>Account pages (/pages/account/*.php)</li>";
echo "<li>Dashboard pages (/pages/dashboard/**/*.php)</li>";
echo "<li>Educational institution pages in /pages/common/</li>";
echo "</ul>";
?>
<?php
// Comprehensive Site Review
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🔍 11klassniki.ru Site Review</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";
echo "<p>Date: " . date('Y-m-d H:i:s') . "</p>";

// 1. Database Health Check
echo "<h2>1. Database Health Check</h2>";
$tables = ['users', 'posts', 'news', 'universities', 'colleges', 'schools', 'regions', 'areas', 'towns', 'comments'];
echo "<table border='1'>";
echo "<tr><th>Table</th><th>Records</th><th>Status</th></tr>";
foreach ($tables as $table) {
    $result = $connection->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        $status = $count > 0 ? "✅" : "⚠️";
        echo "<tr><td>$table</td><td>$count</td><td>$status</td></tr>";
    } else {
        echo "<tr><td>$table</td><td>-</td><td>❌ Error</td></tr>";
    }
}
echo "</table>";

// 2. Key Pages Test
echo "<h2>2. Key Pages to Test</h2>";
$pages = [
    ['/', 'Homepage'],
    ['/vpo-all-regions', 'VPO (Universities) by Region'],
    ['/spo-all-regions', 'SPO (Colleges) by Region'],
    ['/schools-all-regions', 'Schools by Region'],
    ['/news', 'News Section'],
    ['/posts', 'Posts/Articles'],
    ['/search', 'Search Page'],
    ['/login', 'Login Page'],
    ['/registration', 'Registration Page'],
    ['/about', 'About Page'],
    ['/tests', 'Tests Section']
];

echo "<ul>";
foreach ($pages as $page) {
    echo "<li><a href='{$page[0]}' target='_blank'>{$page[1]}</a> - {$page[0]}</li>";
}
echo "</ul>";

// 3. Recent Content Check
echo "<h2>3. Recent Content</h2>";

// Recent news
echo "<h3>Latest News:</h3>";
$news = $connection->query("SELECT id, title, created_at FROM news WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 5");
if ($news && $news->num_rows > 0) {
    echo "<ol>";
    while ($row = $news->fetch_assoc()) {
        echo "<li>{$row['title']} ({$row['created_at']})</li>";
    }
    echo "</ol>";
} else {
    echo "<p>⚠️ No approved news found</p>";
}

// Recent posts
echo "<h3>Latest Posts:</h3>";
$posts = $connection->query("SELECT id, title, created_at FROM posts ORDER BY created_at DESC LIMIT 5");
if ($posts && $posts->num_rows > 0) {
    echo "<ol>";
    while ($row = $posts->fetch_assoc()) {
        echo "<li>{$row['title']} ({$row['created_at']})</li>";
    }
    echo "</ol>";
} else {
    echo "<p>⚠️ No posts found</p>";
}

// 4. User Activity
echo "<h2>4. User Statistics</h2>";
$total_users = $connection->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$active_users = $connection->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1")->fetch_assoc()['count'];
$recent_registrations = $connection->query("SELECT COUNT(*) as count FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];

echo "<ul>";
echo "<li>Total Users: $total_users</li>";
echo "<li>Active Users: $active_users</li>";
echo "<li>Registrations (last 30 days): $recent_registrations</li>";
echo "</ul>";

// 5. Educational Institutions Summary
echo "<h2>5. Educational Institutions</h2>";
$vpo_approved = $connection->query("SELECT COUNT(*) as count FROM universities WHERE is_approved = 1")->fetch_assoc()['count'];
$spo_approved = $connection->query("SELECT COUNT(*) as count FROM colleges WHERE is_approved = 1")->fetch_assoc()['count'];
$schools_count = $connection->query("SELECT COUNT(*) as count FROM schools")->fetch_assoc()['count'];

echo "<ul>";
echo "<li>Universities (VPO): $vpo_approved approved</li>";
echo "<li>Colleges (SPO): $spo_approved approved</li>";
echo "<li>Schools: $schools_count</li>";
echo "</ul>";

// 6. Potential Issues
echo "<h2>6. Potential Issues Found</h2>";
$issues = [];

// Check for unapproved content
$unapproved_news = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 0")->fetch_assoc()['count'];
if ($unapproved_news > 0) {
    $issues[] = "⚠️ $unapproved_news unapproved news articles";
}

$unapproved_unis = $connection->query("SELECT COUNT(*) as count FROM universities WHERE is_approved = 0")->fetch_assoc()['count'];
if ($unapproved_unis > 0) {
    $issues[] = "⚠️ $unapproved_unis unapproved universities";
}

$unapproved_cols = $connection->query("SELECT COUNT(*) as count FROM colleges WHERE is_approved = 0")->fetch_assoc()['count'];
if ($unapproved_cols > 0) {
    $issues[] = "⚠️ $unapproved_cols unapproved colleges";
}

// Check for missing URLs
$missing_vpo_urls = $connection->query("SELECT COUNT(*) as count FROM universities WHERE url_slug IS NULL OR url_slug = ''")->fetch_assoc()['count'];
if ($missing_vpo_urls > 0) {
    $issues[] = "⚠️ $missing_vpo_urls universities without URL slugs";
}

$missing_spo_urls = $connection->query("SELECT COUNT(*) as count FROM colleges WHERE url_slug IS NULL OR url_slug = ''")->fetch_assoc()['count'];
if ($missing_spo_urls > 0) {
    $issues[] = "⚠️ $missing_spo_urls colleges without URL slugs";
}

if (empty($issues)) {
    echo "<p style='color: green;'>✅ No major issues found!</p>";
} else {
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
}

// 7. Configuration Status
echo "<h2>7. Configuration Status</h2>";
echo "<ul>";
echo "<li>Database: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</li>";
echo "<li>Environment: " . (defined('APP_ENV') ? APP_ENV : 'Not defined') . "</li>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "</ul>";

$connection->close();

echo "<hr>";
echo "<p><a href='/'>← Back to Homepage</a></p>";
?>
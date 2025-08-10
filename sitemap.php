<?php
// Sitemap generator
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Set XML header
header('Content-Type: application/xml; charset=UTF-8');

// Base URL
$baseUrl = 'https://11klassniki.ru';

// Start XML
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Homepage
echo '<url>';
echo '<loc>' . $baseUrl . '/</loc>';
echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
echo '<changefreq>daily</changefreq>';
echo '<priority>1.0</priority>';
echo '</url>';

// Main pages
$mainPages = [
    '/news' => ['freq' => 'daily', 'priority' => '0.9'],
    '/posts' => ['freq' => 'daily', 'priority' => '0.9'],
    '/vpo' => ['freq' => 'weekly', 'priority' => '0.8'],
    '/spo' => ['freq' => 'weekly', 'priority' => '0.8'],
    '/schools' => ['freq' => 'weekly', 'priority' => '0.8'],
    '/search' => ['freq' => 'monthly', 'priority' => '0.7'],
    '/login' => ['freq' => 'monthly', 'priority' => '0.5'],
    '/register' => ['freq' => 'monthly', 'priority' => '0.5'],
    '/privacy' => ['freq' => 'yearly', 'priority' => '0.3'],
];

foreach ($mainPages as $url => $data) {
    echo '<url>';
    echo '<loc>' . $baseUrl . $url . '</loc>';
    echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
    echo '<changefreq>' . $data['freq'] . '</changefreq>';
    echo '<priority>' . $data['priority'] . '</priority>';
    echo '</url>';
}

// News items
$news = db_fetch_all("
    SELECT url_news, created_at 
    FROM news 
    WHERE is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 500
");

foreach ($news as $item) {
    echo '<url>';
    echo '<loc>' . $baseUrl . '/news/' . htmlspecialchars($item['url_news']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['created_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

// Posts
$posts = db_fetch_all("
    SELECT url_slug, date_post 
    FROM posts 
    WHERE is_published = 1 
    ORDER BY date_post DESC 
    LIMIT 200
");

foreach ($posts as $item) {
    echo '<url>';
    echo '<loc>' . $baseUrl . '/post/' . htmlspecialchars($item['url_slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['date_post'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

// VPO
$vpos = db_fetch_all("
    SELECT url_slug, created_at 
    FROM vpo 
    ORDER BY name_vpo
");

foreach ($vpos as $item) {
    echo '<url>';
    echo '<loc>' . $baseUrl . '/vpo/' . htmlspecialchars($item['url_slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['created_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

// SPO
$spos = db_fetch_all("
    SELECT url_slug, created_at 
    FROM spo 
    ORDER BY name_spo
");

foreach ($spos as $item) {
    echo '<url>';
    echo '<loc>' . $baseUrl . '/spo/' . htmlspecialchars($item['url_slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['created_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.7</priority>';
    echo '</url>';
}

// Schools
$schools = db_fetch_all("
    SELECT url_slug, created_at 
    FROM schools 
    ORDER BY name_school
");

foreach ($schools as $item) {
    echo '<url>';
    echo '<loc>' . $baseUrl . '/school/' . htmlspecialchars($item['url_slug']) . '</loc>';
    echo '<lastmod>' . date('Y-m-d', strtotime($item['created_at'])) . '</lastmod>';
    echo '<changefreq>monthly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

echo '</urlset>';
?>
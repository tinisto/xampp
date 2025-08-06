<?php
/**
 * Dynamic Sitemap Generator
 * Generates sitemap.xml with current content from database
 */

// Database connection
$servername = "localhost";
$username = "franko";
$password = "I68d54M4k71N";
$dbname = "11klassnikiDB";

try {
    $connection = new mysqli($servername, $username, $password, $dbname);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    $baseUrl = "https://11klassniki.ru";
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc>' . $baseUrl . '/</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Static pages -->
    <url>
        <loc>' . $baseUrl . '/about</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/news</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/tests</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/vpo-all-regions</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/spo-all-regions</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/schools-all-regions</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
';

    // Add categories
    $categoryQuery = "SELECT url_category FROM categories";
    $categoryResult = $connection->query($categoryQuery);
    if ($categoryResult && $categoryResult->num_rows > 0) {
        while ($category = $categoryResult->fetch_assoc()) {
            $sitemap .= '    <url>
        <loc>' . $baseUrl . '/category/' . htmlspecialchars($category['url_category']) . '</loc>
        <lastmod>' . date('Y-m-d') . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
';
        }
    }
    
    // Add recent posts
    $postQuery = "SELECT url_slug, date_post FROM posts WHERE status = 1 AND url_slug IS NOT NULL AND url_slug != '' ORDER BY date_post DESC LIMIT 500";
    $postResult = $connection->query($postQuery);
    if ($postResult && $postResult->num_rows > 0) {
        while ($post = $postResult->fetch_assoc()) {
            $lastmod = $post['date_post'] ? date('Y-m-d', strtotime($post['date_post'])) : date('Y-m-d');
            $sitemap .= '    <url>
        <loc>' . $baseUrl . '/post/' . htmlspecialchars($post['url_slug']) . '</loc>
        <lastmod>' . $lastmod . '</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
';
        }
    }

    $sitemap .= '</urlset>';
    
    // Write sitemap to file
    if (file_put_contents('sitemap.xml', $sitemap)) {
        echo "Sitemap generated successfully with " . (1 + ($categoryResult ? $categoryResult->num_rows : 0) + ($postResult ? $postResult->num_rows : 0) + 6) . " URLs\n";
    } else {
        echo "Failed to generate sitemap\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
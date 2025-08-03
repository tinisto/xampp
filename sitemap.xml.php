<?php
/**
 * Dynamic XML Sitemap Generator
 * URL: /sitemap.xml
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/seo.php';

// Set XML content type
header('Content-Type: application/xml; charset=utf-8');

try {
    $sitemap = SEOHelper::generateSitemap($connection);
    echo $sitemap;
} catch (Exception $e) {
    // Fallback basic sitemap
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <url>' . "\n";
    echo '    <loc>https://11klassniki.ru/</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    echo '  </url>' . "\n";
    echo '</urlset>';
    
    error_log("Sitemap generation error: " . $e->getMessage());
}
?>
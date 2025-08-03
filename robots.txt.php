<?php
/**
 * Dynamic Robots.txt Generator
 * URL: /robots.txt
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/seo.php';

// Set text content type
header('Content-Type: text/plain; charset=utf-8');

echo SEOHelper::generateRobotsTxt();
?>
<?php
/**
 * SEO-optimized head section
 * Includes all necessary meta tags, structured data, and SEO elements
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/seo.php';

// Extract SEO configuration
$seoConfig = $additionalData['seo'] ?? [];
$pageTitle = $seoConfig['title'] ?? $pageTitle ?? 'Образовательный портал';
$metaDescription = $seoConfig['description'] ?? $additionalData['metaD'] ?? 'Образовательный портал 11-классники';
$metaKeywords = $seoConfig['keywords'] ?? $additionalData['metaK'] ?? '11 классников, образование, школа, вуз';

// Generate optimized title
$fullTitle = SEOHelper::generateTitle($pageTitle);
$canonicalUrl = $seoConfig['canonical'] ?? '';
$ogImage = $seoConfig['image'] ?? '';

// Prepare meta tag data
$metaData = [
    'title' => $fullTitle,
    'description' => $metaDescription,
    'keywords' => $metaKeywords,
    'canonical' => $canonicalUrl,
    'image' => $ogImage,
    'robots' => $seoConfig['robots'] ?? 'index, follow'
];

// Add additional meta data if provided
if (isset($seoConfig['og_type'])) $metaData['og_type'] = $seoConfig['og_type'];
if (isset($seoConfig['article_author'])) $metaData['article_author'] = $seoConfig['article_author'];
if (isset($seoConfig['article_published_time'])) $metaData['article_published_time'] = $seoConfig['article_published_time'];
if (isset($seoConfig['article_modified_time'])) $metaData['article_modified_time'] = $seoConfig['article_modified_time'];
?>
<!DOCTYPE html>
<html lang="ru" prefix="og: http://ogp.me/ns#">
<head>
    <title><?= htmlspecialchars($fullTitle) ?></title>
    
    <?= SEOHelper::generateMetaTags($metaData) ?>
    
    <?php if (isset($seoConfig['hreflang'])): ?>
        <?= SEOHelper::generateHreflangTags($seoConfig['hreflang']) ?>
    <?php endif; ?>
    
    <?php
    // Generate structured data based on page type
    $structuredDataType = $seoConfig['structured_data_type'] ?? 'WebSite';
    $structuredData = $seoConfig['structured_data'] ?? [];
    
    echo SEOHelper::generateStructuredData($structuredDataType, $structuredData);
    
    // Add organization structured data on homepage
    if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') {
        echo SEOHelper::generateStructuredData('Organization');
    }
    ?>
    
    <!-- Favicon and app icons -->
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php';
    renderAllFavicons();
    ?>
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Critical CSS inline (if provided) -->
    <?php if (isset($seoConfig['critical_css'])): ?>
        <style><?= $seoConfig['critical_css'] ?></style>
    <?php endif; ?>
    
    <!-- Critical inline CSS to prevent FOUC -->
    <style>
        /* Prevent flash of unstyled content - set initial backgrounds immediately */
        html, body {
            background-color: <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '#1a1a1a' : '#ffffff' ?>;
            color: <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '#e4e6eb' : '#333' ?>;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .content-wrapper {
            background-color: <?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? '#1a1a1a' : '#ffffff' ?>;
            min-height: 100vh;
        }
        /* Ensure smooth transitions after initial load */
        body, .content-wrapper {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
    
    <!-- External stylesheets with versioning -->
    <?php if (isset($additionalData['cssFramework']) && $additionalData['cssFramework'] === 'bootstrap'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <?php endif; ?>
    
    <?php 
    // Include performance functions for versioned assets
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/functions/performance.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/performance.php';
    }
    
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/css/style.css')): 
        $cssUrl = function_exists('versioned_asset') ? versioned_asset('/css/style.css') : '/css/style.css';
    ?>
        <link rel="stylesheet" href="<?= $cssUrl ?>">
    <?php endif; ?>
    
    <!-- Custom CSS -->
    <?php if (isset($additionalData['customCSS'])): ?>
        <style><?= $additionalData['customCSS'] ?></style>
    <?php endif; ?>
    
    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    
    <!-- Prevent automatic detection of phone numbers -->
    <meta name="format-detection" content="telephone=no">
    
    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <?php if (isset($seoConfig['custom_head'])): ?>
        <?= $seoConfig['custom_head'] ?>
    <?php endif; ?>
</head>
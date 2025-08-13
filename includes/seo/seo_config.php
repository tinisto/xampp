<?php
/**
 * SEO Configuration and Helper Functions
 * Provides SEO optimization utilities
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

/**
 * Generate SEO-friendly URL slug
 */
function generateSlug($string) {
    // Convert to lowercase
    $string = mb_strtolower($string, 'UTF-8');
    
    // Replace common Russian characters with Latin equivalents
    $transliteration = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];
    
    $string = strtr($string, $transliteration);
    
    // Replace non-alphanumeric characters with hyphens
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    
    // Remove leading/trailing hyphens
    $string = trim($string, '-');
    
    return $string;
}

/**
 * Generate meta tags for SEO
 */
function generateMetaTags($options = []) {
    $defaults = [
        'title' => '11-классники - Образовательный портал',
        'description' => 'Образовательный портал для учеников 11 классов',
        'keywords' => 'образование, школа, университет, ЕГЭ, поступление',
        'author' => '11klassniki.ru',
        'robots' => 'index, follow',
        'og_type' => 'website',
        'og_image' => '/images/og-default.jpg'
    ];
    
    $options = array_merge($defaults, $options);
    
    // Sanitize all values
    foreach ($options as $key => $value) {
        $options[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    $meta = [];
    
    // Basic meta tags
    $meta[] = '<meta name="description" content="' . $options['description'] . '">';
    $meta[] = '<meta name="keywords" content="' . $options['keywords'] . '">';
    $meta[] = '<meta name="author" content="' . $options['author'] . '">';
    $meta[] = '<meta name="robots" content="' . $options['robots'] . '">';
    
    // Open Graph tags
    $meta[] = '<meta property="og:title" content="' . $options['title'] . '">';
    $meta[] = '<meta property="og:description" content="' . $options['description'] . '">';
    $meta[] = '<meta property="og:type" content="' . $options['og_type'] . '">';
    $meta[] = '<meta property="og:url" content="' . getCurrentUrl() . '">';
    $meta[] = '<meta property="og:image" content="' . $options['og_image'] . '">';
    $meta[] = '<meta property="og:site_name" content="11-классники">';
    
    // Twitter Card tags
    $meta[] = '<meta name="twitter:card" content="summary_large_image">';
    $meta[] = '<meta name="twitter:title" content="' . $options['title'] . '">';
    $meta[] = '<meta name="twitter:description" content="' . $options['description'] . '">';
    $meta[] = '<meta name="twitter:image" content="' . $options['og_image'] . '">';
    
    return implode("\n", $meta);
}

/**
 * Get current URL for canonical and OG tags
 */
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    return $protocol . '://' . $host . $uri;
}

/**
 * Generate structured data (JSON-LD) for search engines
 */
function generateStructuredData($type = 'WebSite', $data = []) {
    $defaults = [
        '@context' => 'https://schema.org',
        '@type' => $type,
        'url' => getCurrentUrl(),
        'name' => '11-классники',
        'description' => 'Образовательный портал для учеников 11 классов'
    ];
    
    if ($type === 'WebSite') {
        $defaults['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => 'https://11klassniki.ru/search?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ];
    }
    
    $structuredData = array_merge($defaults, $data);
    
    return '<script type="application/ld+json">' . "\n" . 
           json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
           "\n" . '</script>';
}

/**
 * Generate breadcrumb structured data
 */
function generateBreadcrumbStructuredData($breadcrumbs) {
    $items = [];
    $position = 1;
    
    foreach ($breadcrumbs as $breadcrumb) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $breadcrumb['name'],
            'item' => $breadcrumb['url']
        ];
    }
    
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    ];
    
    return '<script type="application/ld+json">' . "\n" . 
           json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
           "\n" . '</script>';
}

/**
 * Optimize page title for SEO
 */
function optimizeTitle($title, $suffix = ' | 11-классники') {
    // Remove extra spaces
    $title = preg_replace('/\s+/', ' ', trim($title));
    
    // Limit length to 60 characters (including suffix)
    $maxLength = 60 - mb_strlen($suffix);
    
    if (mb_strlen($title) > $maxLength) {
        $title = mb_substr($title, 0, $maxLength - 3) . '...';
    }
    
    return $title . $suffix;
}

/**
 * Generate sitemap XML entry
 */
function generateSitemapEntry($url, $lastmod = null, $changefreq = 'weekly', $priority = '0.5') {
    $lastmod = $lastmod ?: date('Y-m-d');
    
    return <<<XML
    <url>
        <loc>{$url}</loc>
        <lastmod>{$lastmod}</lastmod>
        <changefreq>{$changefreq}</changefreq>
        <priority>{$priority}</priority>
    </url>
XML;
}
?>
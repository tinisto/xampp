<?php
/**
 * SEO Helper Functions for 11klassniki.ru
 * Comprehensive SEO optimization utilities
 */

class SEOHelper {
    
    private static $defaultMeta = [
        'title' => '11klassniki.ru - Образовательный портал для школьников',
        'description' => 'Образовательный портал 11-классники - все для успешной сдачи ЕГЭ, ОГЭ и поступления в вуз. Новости образования, тесты, информация о школах и вузах.',
        'keywords' => '11 классников, образование, школа, вуз, егэ, огэ, тесты, новости образования, поступление, университет',
        'og_type' => 'website',
        'og_locale' => 'ru_RU',
        'twitter_card' => 'summary_large_image'
    ];
    
    private static $siteUrl = 'https://11klassniki.ru';
    private static $siteName = '11klassniki.ru';
    
    /**
     * Generate complete meta tags for a page
     * 
     * @param array $meta Meta tag data
     * @return string HTML meta tags
     */
    public static function generateMetaTags($meta = []) {
        $meta = array_merge(self::$defaultMeta, $meta);
        
        $canonical = $meta['canonical'] ?? self::getCurrentUrl();
        $image = $meta['image'] ?? self::$siteUrl . '/images/og-default.jpg';
        
        $html = '';
        
        // Basic meta tags
        $html .= '<meta charset="UTF-8">' . "\n";
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        $html .= '<meta name="description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
        $html .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords']) . '">' . "\n";
        $html .= '<meta name="author" content="' . self::$siteName . '">' . "\n";
        
        // Canonical URL
        $html .= '<link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";
        
        // Open Graph tags
        $html .= '<meta property="og:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . $meta['og_type'] . '">' . "\n";
        $html .= '<meta property="og:url" content="' . htmlspecialchars($canonical) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
        $html .= '<meta property="og:site_name" content="' . self::$siteName . '">' . "\n";
        $html .= '<meta property="og:locale" content="' . $meta['og_locale'] . '">' . "\n";
        
        // Twitter Card tags
        $html .= '<meta name="twitter:card" content="' . $meta['twitter_card'] . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
        
        // Additional SEO tags
        if (isset($meta['robots'])) {
            $html .= '<meta name="robots" content="' . $meta['robots'] . '">' . "\n";
        } else {
            $html .= '<meta name="robots" content="index, follow">' . "\n";
        }
        
        // Language and geo tags
        $html .= '<meta name="language" content="Russian">' . "\n";
        $html .= '<meta name="geo.region" content="RU">' . "\n";
        $html .= '<meta name="geo.country" content="Russia">' . "\n";
        
        // Mobile optimization
        $html .= '<meta name="format-detection" content="telephone=no">' . "\n";
        $html .= '<meta name="theme-color" content="#007bff">' . "\n";
        
        return $html;
    }
    
    /**
     * Generate structured data (JSON-LD)
     * 
     * @param string $type Schema.org type
     * @param array $data Structured data
     * @return string JSON-LD script tag
     */
    public static function generateStructuredData($type, $data = []) {
        $baseData = [
            '@context' => 'https://schema.org',
            '@type' => $type
        ];
        
        switch ($type) {
            case 'WebSite':
                $structured = array_merge($baseData, [
                    'name' => self::$siteName,
                    'url' => self::$siteUrl,
                    'description' => self::$defaultMeta['description'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => self::$siteUrl . '/search?q={search_term_string}',
                        'query-input' => 'required name=search_term_string'
                    ]
                ], $data);
                break;
                
            case 'Organization':
                $structured = array_merge($baseData, [
                    'name' => self::$siteName,
                    'url' => self::$siteUrl,
                    'logo' => self::$siteUrl . '/images/logo.png',
                    'description' => self::$defaultMeta['description'],
                    'sameAs' => [
                        // Add social media URLs here if available
                    ]
                ], $data);
                break;
                
            case 'EducationalOrganization':
                $structured = array_merge($baseData, [
                    'name' => $data['name'] ?? '',
                    'url' => $data['url'] ?? '',
                    'description' => $data['description'] ?? '',
                    'address' => $data['address'] ?? '',
                    'telephone' => $data['telephone'] ?? '',
                    'email' => $data['email'] ?? ''
                ], $data);
                break;
                
            case 'Article':
                $structured = array_merge($baseData, [
                    'headline' => $data['title'] ?? '',
                    'description' => $data['description'] ?? '',
                    'image' => $data['image'] ?? '',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $data['author'] ?? self::$siteName
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => self::$siteName,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => self::$siteUrl . '/images/logo.png'
                        ]
                    ],
                    'datePublished' => $data['datePublished'] ?? date('c'),
                    'dateModified' => $data['dateModified'] ?? date('c')
                ], $data);
                break;
                
            default:
                $structured = array_merge($baseData, $data);
        }
        
        return '<script type="application/ld+json">' . json_encode($structured, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
    }
    
    /**
     * Generate breadcrumb navigation
     * 
     * @param array $breadcrumbs Array of breadcrumb items
     * @return string HTML breadcrumb navigation
     */
    public static function generateBreadcrumbs($breadcrumbs = []) {
        if (empty($breadcrumbs)) {
            return '';
        }
        
        // Add home breadcrumb if not present
        if (!isset($breadcrumbs[0]) || $breadcrumbs[0]['name'] !== 'Главная') {
            array_unshift($breadcrumbs, ['name' => 'Главная', 'url' => '/']);
        }
        
        $html = '<nav aria-label="breadcrumb" class="breadcrumb-nav">';
        $html .= '<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">';
        
        foreach ($breadcrumbs as $index => $crumb) {
            $position = $index + 1;
            $isLast = ($index === count($breadcrumbs) - 1);
            
            $html .= '<li class="breadcrumb-item' . ($isLast ? ' active' : '') . '" ';
            $html .= 'itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            
            if (!$isLast && isset($crumb['url'])) {
                $html .= '<a href="' . htmlspecialchars($crumb['url']) . '" itemprop="item">';
                $html .= '<span itemprop="name">' . htmlspecialchars($crumb['name']) . '</span>';
                $html .= '</a>';
            } else {
                $html .= '<span itemprop="name">' . htmlspecialchars($crumb['name']) . '</span>';
            }
            
            $html .= '<meta itemprop="position" content="' . $position . '">';
            $html .= '</li>';
        }
        
        $html .= '</ol>';
        $html .= '</nav>';
        
        return $html;
    }
    
    /**
     * Generate optimized page title
     * 
     * @param string $title Page title
     * @param string $suffix Site suffix
     * @return string Optimized title
     */
    public static function generateTitle($title, $suffix = null) {
        $suffix = $suffix ?? self::$siteName;
        
        if (empty($title)) {
            return $suffix;
        }
        
        // Limit title length for SEO
        $maxLength = 60;
        $fullTitle = $title . ' - ' . $suffix;
        
        if (mb_strlen($fullTitle) > $maxLength) {
            $availableLength = $maxLength - mb_strlen(' - ' . $suffix);
            $title = mb_substr($title, 0, $availableLength - 3) . '...';
            $fullTitle = $title . ' - ' . $suffix;
        }
        
        return $fullTitle;
    }
    
    /**
     * Generate XML sitemap
     * 
     * @param mysqli $connection Database connection
     * @return string XML sitemap
     */
    public static function generateSitemap($connection) {
        $urls = [];
        
        // Add static pages
        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => '/news', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/tests', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/vpo-all-regions', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/spo-all-regions', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/schools-all-regions', 'priority' => '0.9', 'changefreq' => 'weekly']
        ];
        
        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => self::$siteUrl . $page['url'],
                'lastmod' => date('Y-m-d'),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority']
            ];
        }
        
        // Add dynamic content
        try {
            // News articles
            $newsQuery = "SELECT id, title, created_at FROM news WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 1000";
            $newsResult = $connection->query($newsQuery);
            if ($newsResult) {
                while ($row = $newsResult->fetch_assoc()) {
                    $urls[] = [
                        'loc' => self::$siteUrl . '/news/' . $row['id'],
                        'lastmod' => date('Y-m-d', strtotime($row['created_at'])),
                        'changefreq' => 'monthly',
                        'priority' => '0.7'
                    ];
                }
            }
            
            // Universities
            $vpoQuery = "SELECT id, url_slug FROM universities WHERE is_approved = 1 AND url_slug IS NOT NULL LIMIT 1000";
            $vpoResult = $connection->query($vpoQuery);
            if ($vpoResult) {
                while ($row = $vpoResult->fetch_assoc()) {
                    $urls[] = [
                        'loc' => self::$siteUrl . '/vpo/' . $row['url_slug'],
                        'lastmod' => date('Y-m-d'),
                        'changefreq' => 'monthly',
                        'priority' => '0.8'
                    ];
                }
            }
            
            // Colleges
            $spoQuery = "SELECT id, url_slug FROM colleges WHERE is_approved = 1 AND url_slug IS NOT NULL LIMIT 1000";
            $spoResult = $connection->query($spoQuery);
            if ($spoResult) {
                while ($row = $spoResult->fetch_assoc()) {
                    $urls[] = [
                        'loc' => self::$siteUrl . '/spo/' . $row['url_slug'],
                        'lastmod' => date('Y-m-d'),
                        'changefreq' => 'monthly',
                        'priority' => '0.8'
                    ];
                }
            }
            
        } catch (Exception $e) {
            error_log("SEO Sitemap generation error: " . $e->getMessage());
        }
        
        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Get current URL
     * 
     * @return string Current page URL
     */
    private static function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        return $protocol . '://' . $host . $uri;
    }
    
    /**
     * Generate hreflang tags for multilingual support
     * 
     * @param array $languages Array of language codes and URLs
     * @return string HTML hreflang tags
     */
    public static function generateHreflangTags($languages = []) {
        $html = '';
        
        foreach ($languages as $lang => $url) {
            $html .= '<link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($url) . '">' . "\n";
        }
        
        return $html;
    }
    
    /**
     * Generate optimized robots.txt content
     * 
     * @return string Robots.txt content
     */
    public static function generateRobotsTxt() {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /includes/\n";
        $content .= "Disallow: /database/\n";
        $content .= "Disallow: /config/\n";
        $content .= "Disallow: /_cleanup/\n";
        $content .= "Disallow: /cache/\n";
        $content .= "Disallow: /search?*\n";
        $content .= "Disallow: /*?*utm_*\n";
        $content .= "\n";
        $content .= "# Sitemaps\n";
        $content .= "Sitemap: " . self::$siteUrl . "/sitemap.xml\n";
        $content .= "\n";
        $content .= "# Crawl-delay for specific bots\n";
        $content .= "User-agent: Yandex\n";
        $content .= "Crawl-delay: 1\n";
        $content .= "\n";
        $content .= "User-agent: Googlebot\n";
        $content .= "Crawl-delay: 1\n";
        
        return $content;
    }
}
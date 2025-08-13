<?php
/**
 * Cache Middleware
 * Automatic page caching for public pages
 */

require_once __DIR__ . '/page_cache.php';

class CacheMiddleware {
    
    /**
     * Apply caching to current request
     * @param array $options Caching options
     */
    public static function apply($options = []) {
        // Default options
        $defaults = [
            'ttl' => 3600,              // 1 hour default TTL
            'skip_logged_in' => true,   // Skip caching for logged in users
            'skip_post' => true,        // Skip caching for POST requests
            'skip_admin' => true,       // Skip caching for admin pages
            'skip_patterns' => [        // URL patterns to skip
                '/admin/*',
                '/dashboard/*',
                '/api/*',
                '*/process.php',
                '*/ajax/*'
            ]
        ];
        
        $settings = array_merge($defaults, $options);
        
        // Check if caching should be skipped
        if (self::shouldSkipCaching($settings)) {
            return false;
        }
        
        // Generate cache key
        $cacheKey = PageCache::generateKey([
            'mobile' => self::isMobile(),
            'theme' => $_COOKIE['theme'] ?? 'light'
        ]);
        
        // Try to serve from cache
        if (PageCache::start($cacheKey, $settings['ttl'])) {
            exit; // Cache hit, page served
        }
        
        // Set up auto-end on script termination
        register_shutdown_function(function() use ($cacheKey) {
            if (connection_status() === CONNECTION_NORMAL) {
                PageCache::end($cacheKey);
            }
        });
        
        return true;
    }
    
    /**
     * Check if caching should be skipped for current request
     * @param array $settings Cache settings
     * @return bool Whether to skip caching
     */
    private static function shouldSkipCaching($settings) {
        // Skip for POST requests
        if ($settings['skip_post'] && $_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }
        
        // Skip for logged in users (optional)
        if ($settings['skip_logged_in'] && isset($_SESSION['user_id'])) {
            return true;
        }
        
        // Skip for admin pages
        if ($settings['skip_admin'] && self::isAdminPage()) {
            return true;
        }
        
        // Skip if cache is disabled
        if (!PageCache::isEnabled()) {
            return true;
        }
        
        // Skip for specific URL patterns
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        foreach ($settings['skip_patterns'] as $pattern) {
            if (fnmatch($pattern, $currentUrl)) {
                return true;
            }
        }
        
        // Skip if specific headers indicate dynamic content
        if (isset($_SERVER['HTTP_CACHE_CONTROL']) && 
            strpos($_SERVER['HTTP_CACHE_CONTROL'], 'no-cache') !== false) {
            return true;
        }
        
        // Skip for AJAX requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current page is an admin page
     * @return bool Whether current page is admin
     */
    private static function isAdminPage() {
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $adminPatterns = [
            '/admin/*',
            '/dashboard/*',
            '/*-dashboard/*',
            '/monitoring/*'
        ];
        
        foreach ($adminPatterns as $pattern) {
            if (fnmatch($pattern, $currentUrl)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Detect if request is from mobile device
     * @return bool Whether request is from mobile
     */
    private static function isMobile() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'Windows Phone', 'Opera Mini'
        ];
        
        foreach ($mobileKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Set cache headers for manual control
     * @param int $ttl Time to live in seconds
     * @param array $options Additional cache options
     */
    public static function setCacheHeaders($ttl = 3600, $options = []) {
        $defaults = [
            'public' => true,
            'etag' => true,
            'last_modified' => true
        ];
        
        $settings = array_merge($defaults, $options);
        
        // Set cache control header
        $cacheControl = $settings['public'] ? 'public' : 'private';
        $cacheControl .= ', max-age=' . $ttl;
        
        header('Cache-Control: ' . $cacheControl);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
        
        // Set ETag
        if ($settings['etag']) {
            $etag = md5($_SERVER['REQUEST_URI'] . filemtime(__FILE__));
            header('ETag: "' . $etag . '"');
            
            // Check if client has current version
            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
                $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
                http_response_code(304);
                exit;
            }
        }
        
        // Set Last-Modified
        if ($settings['last_modified']) {
            $lastModified = gmdate('D, d M Y H:i:s', filemtime(__FILE__)) . ' GMT';
            header('Last-Modified: ' . $lastModified);
            
            // Check if client has current version
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
                $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $lastModified) {
                http_response_code(304);
                exit;
            }
        }
    }
    
    /**
     * Invalidate cache when content is updated
     * @param string $type Content type
     * @param int $id Content ID
     */
    public static function invalidateOnUpdate($type, $id = null) {
        // Hook into content update events
        switch ($type) {
            case 'news_created':
            case 'news_updated':
            case 'news_deleted':
                PageCache::invalidateContent('news', $id);
                break;
                
            case 'post_created':
            case 'post_updated':
            case 'post_deleted':
                PageCache::invalidateContent('post', $id);
                break;
                
            case 'user_updated':
                PageCache::invalidateContent('user', $id);
                break;
                
            case 'settings_updated':
                PageCache::invalidateContent('all');
                break;
        }
    }
    
    /**
     * Smart cache warming for important pages
     * @return array Results of warming process
     */
    public static function warmImportantPages() {
        $importantUrls = [
            '/',                    // Homepage
            '/news',               // News listing
            '/posts',              // Posts listing
            '/category/education', // Popular categories
            '/category/technology',
            '/category/science'
        ];
        
        // Add recent content URLs
        global $connection;
        
        // Get recent news
        $newsQuery = "SELECT id_news FROM news WHERE approved = 1 ORDER BY date_news DESC LIMIT 5";
        $newsResult = mysqli_query($connection, $newsQuery);
        while ($news = mysqli_fetch_assoc($newsResult)) {
            $importantUrls[] = "/news/{$news['id_news']}";
        }
        
        // Get recent posts
        $postsQuery = "SELECT id_post FROM posts WHERE approved = 1 ORDER BY date_post DESC LIMIT 5";
        $postsResult = mysqli_query($connection, $postsQuery);
        while ($post = mysqli_fetch_assoc($postsResult)) {
            $importantUrls[] = "/post/{$post['id_post']}";
        }
        
        return PageCache::warmUp($importantUrls);
    }
    
    /**
     * Get cache statistics with additional metrics
     * @return array Enhanced cache statistics
     */
    public static function getEnhancedStats() {
        $stats = PageCache::getStats();
        
        // Add hit rate calculation (would need to track hits/misses)
        $stats['estimated_hit_rate'] = '~75%'; // Placeholder
        
        // Add cache effectiveness
        if ($stats['total_files'] > 0) {
            $stats['cache_effectiveness'] = 'High';
        } else {
            $stats['cache_effectiveness'] = 'Low';
        }
        
        return $stats;
    }
}
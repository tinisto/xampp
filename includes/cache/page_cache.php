<?php
/**
 * Page Caching System
 * Provides full-page caching with smart invalidation
 */

class PageCache {
    private static $cacheDir;
    private static $defaultTTL = 3600; // 1 hour
    private static $enabled = true;
    
    /**
     * Initialize cache system
     */
    public static function init($cacheDir = null) {
        self::$cacheDir = $cacheDir ?: $_SERVER['DOCUMENT_ROOT'] . '/cache/pages';
        
        // Create cache directory if it doesn't exist
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
        
        // Check if caching is disabled via environment
        if (isset($_ENV['CACHE_ENABLED']) && $_ENV['CACHE_ENABLED'] === 'false') {
            self::$enabled = false;
        }
    }
    
    /**
     * Generate cache key for current request
     * @param array $params Additional parameters to include in key
     * @return string Cache key
     */
    public static function generateKey($params = []) {
        $baseKey = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Include query parameters
        if (!empty($_GET)) {
            $baseKey .= '?' . http_build_query($_GET);
        }
        
        // Include user-specific data if logged in
        if (isset($_SESSION['user_id'])) {
            $baseKey .= '_user_' . $_SESSION['user_id'];
        }
        
        // Include additional parameters
        if (!empty($params)) {
            $baseKey .= '_' . md5(serialize($params));
        }
        
        return md5($baseKey);
    }
    
    /**
     * Get cached page content
     * @param string $key Cache key
     * @param int $ttl Time to live in seconds
     * @return string|false Cached content or false if not found/expired
     */
    public static function get($key, $ttl = null) {
        if (!self::$enabled) {
            return false;
        }
        
        self::init();
        $ttl = $ttl ?: self::$defaultTTL;
        $cacheFile = self::$cacheDir . '/' . $key . '.cache';
        
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $cacheTime = filemtime($cacheFile);
        if (time() - $cacheTime > $ttl) {
            unlink($cacheFile);
            return false;
        }
        
        $content = file_get_contents($cacheFile);
        return $content !== false ? $content : false;
    }
    
    /**
     * Store page content in cache
     * @param string $key Cache key
     * @param string $content Content to cache
     * @return bool Success status
     */
    public static function set($key, $content) {
        if (!self::$enabled) {
            return false;
        }
        
        self::init();
        $cacheFile = self::$cacheDir . '/' . $key . '.cache';
        
        // Add cache metadata
        $cacheData = [
            'timestamp' => time(),
            'url' => $_SERVER['REQUEST_URI'] ?? '/',
            'content' => $content
        ];
        
        return file_put_contents($cacheFile, serialize($cacheData)) !== false;
    }
    
    /**
     * Delete specific cache entry
     * @param string $key Cache key
     * @return bool Success status
     */
    public static function delete($key) {
        self::init();
        $cacheFile = self::$cacheDir . '/' . $key . '.cache';
        
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }
        
        return true;
    }
    
    /**
     * Clear all cached pages
     * @return int Number of files deleted
     */
    public static function clear() {
        self::init();
        $deleted = 0;
        
        $files = glob(self::$cacheDir . '/*.cache');
        foreach ($files as $file) {
            if (unlink($file)) {
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * Start output buffering for page caching
     * @param string $key Cache key (optional)
     * @param int $ttl Time to live in seconds
     * @return bool Whether to serve from cache
     */
    public static function start($key = null, $ttl = null) {
        if (!self::$enabled) {
            return false;
        }
        
        $key = $key ?: self::generateKey();
        $cached = self::get($key, $ttl);
        
        if ($cached !== false) {
            $data = unserialize($cached);
            echo $data['content'];
            
            // Add cache headers
            header('X-Cache: HIT');
            header('X-Cache-Key: ' . $key);
            return true;
        }
        
        // Start buffering
        ob_start();
        
        // Store key for end() method
        $_SESSION['_cache_key'] = $key;
        
        return false;
    }
    
    /**
     * End output buffering and cache the content
     * @param string $key Cache key (optional)
     * @return string Output content
     */
    public static function end($key = null) {
        if (!self::$enabled) {
            return ob_get_flush();
        }
        
        $content = ob_get_contents();
        ob_end_clean();
        
        $key = $key ?: ($_SESSION['_cache_key'] ?? self::generateKey());
        
        // Only cache successful responses
        if (http_response_code() === 200 && !empty($content)) {
            self::set($key, $content);
        }
        
        // Add cache headers
        header('X-Cache: MISS');
        header('X-Cache-Key: ' . $key);
        
        echo $content;
        return $content;
    }
    
    /**
     * Invalidate cache based on patterns
     * @param array $patterns URL patterns to invalidate
     * @return int Number of files deleted
     */
    public static function invalidateByPattern($patterns) {
        self::init();
        $deleted = 0;
        
        $files = glob(self::$cacheDir . '/*.cache');
        
        foreach ($files as $cacheFile) {
            $data = unserialize(file_get_contents($cacheFile));
            if (!$data || !isset($data['url'])) {
                continue;
            }
            
            foreach ($patterns as $pattern) {
                if (fnmatch($pattern, $data['url'])) {
                    if (unlink($cacheFile)) {
                        $deleted++;
                    }
                    break;
                }
            }
        }
        
        return $deleted;
    }
    
    /**
     * Get cache statistics
     * @return array Cache statistics
     */
    public static function getStats() {
        self::init();
        
        $files = glob(self::$cacheDir . '/*.cache');
        $totalSize = 0;
        $oldestFile = null;
        $newestFile = null;
        
        foreach ($files as $file) {
            $size = filesize($file);
            $time = filemtime($file);
            
            $totalSize += $size;
            
            if (!$oldestFile || $time < filemtime($oldestFile)) {
                $oldestFile = $file;
            }
            
            if (!$newestFile || $time > filemtime($newestFile)) {
                $newestFile = $file;
            }
        }
        
        return [
            'enabled' => self::$enabled,
            'cache_dir' => self::$cacheDir,
            'total_files' => count($files),
            'total_size' => $totalSize,
            'total_size_formatted' => self::formatBytes($totalSize),
            'oldest_file' => $oldestFile ? basename($oldestFile) : null,
            'newest_file' => $newestFile ? basename($newestFile) : null,
            'oldest_time' => $oldestFile ? filemtime($oldestFile) : null,
            'newest_time' => $newestFile ? filemtime($newestFile) : null
        ];
    }
    
    /**
     * Clean expired cache files
     * @param int $maxAge Maximum age in seconds (default: 24 hours)
     * @return int Number of files cleaned
     */
    public static function cleanup($maxAge = 86400) {
        self::init();
        $cleaned = 0;
        $cutoff = time() - $maxAge;
        
        $files = glob(self::$cacheDir . '/*.cache');
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Enable or disable caching
     * @param bool $enabled Whether caching should be enabled
     */
    public static function setEnabled($enabled) {
        self::$enabled = $enabled;
    }
    
    /**
     * Check if caching is enabled
     * @return bool Whether caching is enabled
     */
    public static function isEnabled() {
        return self::$enabled;
    }
    
    /**
     * Set default TTL for cache entries
     * @param int $ttl Time to live in seconds
     */
    public static function setDefaultTTL($ttl) {
        self::$defaultTTL = $ttl;
    }
    
    /**
     * Smart cache invalidation based on content changes
     * @param string $type Content type ('news', 'post', 'user', etc.)
     * @param int $id Content ID
     */
    public static function invalidateContent($type, $id = null) {
        $patterns = [];
        
        switch ($type) {
            case 'news':
                $patterns = [
                    '/news*',
                    '/category-news*',
                    '/'
                ];
                if ($id) {
                    $patterns[] = "/news/{$id}*";
                }
                break;
                
            case 'post':
                $patterns = [
                    '/posts*',
                    '/category*',
                    '/'
                ];
                if ($id) {
                    $patterns[] = "/post/{$id}*";
                }
                break;
                
            case 'user':
                if ($id) {
                    $patterns[] = "/profile/{$id}*";
                    $patterns[] = "/account*";
                }
                break;
                
            case 'all':
                return self::clear();
        }
        
        return self::invalidateByPattern($patterns);
    }
    
    /**
     * Format bytes to human readable format
     */
    private static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Create cache warming script
     * @param array $urls URLs to warm up
     * @return array Results of warming process
     */
    public static function warmUp($urls) {
        $results = [];
        
        foreach ($urls as $url) {
            $fullUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . 
                      $_SERVER['HTTP_HOST'] . $url;
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'PageCache-Warmer/1.0'
                ]
            ]);
            
            $start = microtime(true);
            $content = file_get_contents($fullUrl, false, $context);
            $duration = round((microtime(true) - $start) * 1000, 2);
            
            $results[] = [
                'url' => $url,
                'success' => $content !== false,
                'duration_ms' => $duration,
                'size' => $content ? strlen($content) : 0
            ];
        }
        
        return $results;
    }
}
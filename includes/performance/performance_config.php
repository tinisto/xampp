<?php
/**
 * Performance Optimization Configuration
 * Centralizes all performance settings and functions
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

/**
 * Enable output compression
 */
function enableCompression() {
    if (!ob_get_level()) {
        ob_start('ob_gzhandler');
    }
}

/**
 * Set performance headers
 */
function setPerformanceHeaders() {
    // Enable browser caching for static assets
    $expires = 86400 * 30; // 30 days
    header('Cache-Control: public, max-age=' . $expires);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    
    // Enable ETags for better caching
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        $etag = md5($_SERVER['REQUEST_URI'] . filemtime($_SERVER['SCRIPT_FILENAME']));
        header('ETag: "' . $etag . '"');
        
        if (trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === $etag) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    }
}

/**
 * Database connection pooling helper
 */
class DatabasePool {
    private static $connections = [];
    private static $maxConnections = 5;
    
    public static function getConnection() {
        // Reuse existing connection if available
        foreach (self::$connections as $key => $connection) {
            if ($connection && !$connection->connect_error) {
                return $connection;
            } else {
                unset(self::$connections[$key]);
            }
        }
        
        // Create new connection if under limit
        if (count(self::$connections) < self::$maxConnections) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
            if ($connection) {
                self::$connections[] = $connection;
                return $connection;
            }
        }
        
        return null;
    }
    
    public static function closeAllConnections() {
        foreach (self::$connections as $connection) {
            if ($connection) {
                $connection->close();
            }
        }
        self::$connections = [];
    }
}

/**
 * Simple caching system
 */
class SimpleCache {
    private static $cacheDir = '/tmp/11klassniki_cache/';
    private static $defaultTTL = 3600; // 1 hour
    
    public static function init() {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }
    
    public static function get($key) {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            if ($data['expires'] > time()) {
                return $data['content'];
            } else {
                unlink($file);
            }
        }
        
        return false;
    }
    
    public static function set($key, $content, $ttl = null) {
        self::init();
        $ttl = $ttl ?: self::$defaultTTL;
        $file = self::$cacheDir . md5($key) . '.cache';
        
        $data = [
            'content' => $content,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($file, serialize($data));
    }
    
    public static function delete($key) {
        self::init();
        $file = self::$cacheDir . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    public static function clear() {
        self::init();
        $files = glob(self::$cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}

/**
 * Asset minification and combining
 */
class AssetOptimizer {
    private static $cssFiles = [];
    private static $jsFiles = [];
    
    public static function addCSS($file) {
        self::$cssFiles[] = $file;
    }
    
    public static function addJS($file) {
        self::$jsFiles[] = $file;
    }
    
    public static function renderCSS() {
        if (empty(self::$cssFiles)) return '';
        
        $hash = md5(implode('|', self::$cssFiles));
        $cacheKey = 'css_' . $hash;
        
        $cached = SimpleCache::get($cacheKey);
        if ($cached !== false) {
            return '<style>' . $cached . '</style>';
        }
        
        $combined = '';
        foreach (self::$cssFiles as $file) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $combined .= self::minifyCSS($content);
            }
        }
        
        SimpleCache::set($cacheKey, $combined);
        return '<style>' . $combined . '</style>';
    }
    
    public static function renderJS() {
        if (empty(self::$jsFiles)) return '';
        
        $html = '';
        foreach (self::$jsFiles as $file) {
            $html .= '<script src="' . $file . '" defer></script>';
        }
        
        return $html;
    }
    
    private static function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('/\/\*.*?\*\//s', '', $css);
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        // Remove unnecessary characters
        $css = str_replace(['; ', ' {', '{ ', ' }', '} ', ': '], [';', '{', '{', '}', '}', ':'], $css);
        
        return trim($css);
    }
}

/**
 * Image optimization helpers
 */
class ImageOptimizer {
    public static function getOptimizedImageUrl($imagePath, $width = null, $height = null) {
        // For now, just return original path
        // In production, this could integrate with image processing service
        return $imagePath;
    }
    
    public static function generateWebP($imagePath) {
        // Check if WebP version exists
        $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $imagePath);
        
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $webpPath)) {
            return $webpPath;
        }
        
        return $imagePath;
    }
    
    public static function lazyLoadImage($src, $alt = '', $class = '') {
        $webpSrc = self::generateWebP($src);
        
        return <<<HTML
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" 
             data-src="$webpSrc" 
             alt="$alt" 
             class="lazy-load $class"
             loading="lazy">
HTML;
    }
}

/**
 * Performance monitoring
 */
class PerformanceMonitor {
    private static $startTime;
    private static $queries = [];
    
    public static function start() {
        self::$startTime = microtime(true);
    }
    
    public static function logQuery($query, $executionTime) {
        self::$queries[] = [
            'query' => $query,
            'time' => $executionTime
        ];
    }
    
    public static function getStats() {
        $totalTime = microtime(true) - self::$startTime;
        $queryTime = array_sum(array_column(self::$queries, 'time'));
        
        return [
            'total_time' => round($totalTime * 1000, 2),
            'query_time' => round($queryTime * 1000, 2),
            'query_count' => count(self::$queries),
            'memory_usage' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
        ];
    }
    
    public static function renderDebugInfo() {
        if (isset($_GET['debug']) && $_GET['debug'] === '1') {
            $stats = self::getStats();
            
            return <<<HTML
            <div style="position: fixed; bottom: 0; right: 0; background: #000; color: #fff; padding: 10px; font-size: 12px; z-index: 9999;">
                <strong>Performance Stats:</strong><br>
                Total Time: {$stats['total_time']}ms<br>
                Query Time: {$stats['query_time']}ms<br>
                Queries: {$stats['query_count']}<br>
                Memory: {$stats['memory_usage']}MB
            </div>
HTML;
        }
        
        return '';
    }
}

// Initialize performance monitoring
PerformanceMonitor::start();

// Enable compression for this request
enableCompression();
?>
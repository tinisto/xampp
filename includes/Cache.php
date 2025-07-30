<?php
class Cache {
    private static $cacheDir = __DIR__ . '/../cache/';
    private static $defaultExpiry = 3600; // 1 hour
    
    public static function init() {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }
    
    public static function get($key) {
        self::init();
        $filename = self::getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data['expiry'] < time()) {
            unlink($filename);
            return null;
        }
        
        return $data['value'];
    }
    
    public static function set($key, $value, $expiry = null) {
        self::init();
        
        if ($expiry === null) {
            $expiry = self::$defaultExpiry;
        }
        
        $data = [
            'value' => $value,
            'expiry' => time() + $expiry
        ];
        
        $filename = self::getCacheFilename($key);
        file_put_contents($filename, serialize($data));
        
        return true;
    }
    
    public static function delete($key) {
        $filename = self::getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    public static function clear() {
        self::init();
        
        $files = glob(self::$cacheDir . '*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    public static function remember($key, $callback, $expiry = null) {
        $value = self::get($key);
        
        if ($value === null) {
            $value = $callback();
            self::set($key, $value, $expiry);
        }
        
        return $value;
    }
    
    private static function getCacheFilename($key) {
        return self::$cacheDir . md5($key) . '.cache';
    }
    
    public static function cleanup() {
        self::init();
        
        $files = glob(self::$cacheDir . '*.cache');
        $deleted = 0;
        
        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            
            if ($data['expiry'] < time()) {
                unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }
}

// Page caching middleware
class PageCache {
    private static $enabled = true;
    private static $excludePatterns = [
        '/admin/',
        '/dashboard/',
        '/account/',
        '/login',
        '/logout',
        '/register',
        '/api/'
    ];
    
    public static function start() {
        if (!self::$enabled) {
            return;
        }
        
        // Check if current URL should be cached
        $requestUri = $_SERVER['REQUEST_URI'];
        
        foreach (self::$excludePatterns as $pattern) {
            if (strpos($requestUri, $pattern) !== false) {
                return;
            }
        }
        
        // Don't cache POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }
        
        // Don't cache if user is logged in
        if (isset($_SESSION['user_id'])) {
            return;
        }
        
        $cacheKey = 'page_' . md5($requestUri);
        $cached = Cache::get($cacheKey);
        
        if ($cached !== null) {
            echo $cached;
            exit;
        }
        
        // Start output buffering
        ob_start(function($buffer) use ($cacheKey) {
            // Only cache successful responses
            if (http_response_code() === 200) {
                Cache::set($cacheKey, $buffer, 1800); // 30 minutes
            }
            return $buffer;
        });
    }
    
    public static function invalidate($pattern = null) {
        if ($pattern === null) {
            Cache::clear();
        } else {
            // Invalidate specific pattern
            $files = glob(Cache::$cacheDir . 'page_*.cache');
            
            foreach ($files as $file) {
                $data = unserialize(file_get_contents($file));
                if (strpos($data['value'], $pattern) !== false) {
                    unlink($file);
                }
            }
        }
    }
}
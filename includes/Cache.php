<?php
// Simple file-based caching system

class Cache {
    private static $cacheDir = null;
    private static $defaultTTL = 3600; // 1 hour default
    
    public static function init() {
        self::$cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache/';
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }
    }
    
    public static function get($key) {
        self::init();
        $filename = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Check expiration
        if ($data['expires'] < time()) {
            unlink($filename);
            return null;
        }
        
        return $data['value'];
    }
    
    public static function set($key, $value, $ttl = null) {
        self::init();
        if ($ttl === null) {
            $ttl = self::$defaultTTL;
        }
        
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];
        
        $filename = self::$cacheDir . md5($key) . '.cache';
        file_put_contents($filename, serialize($data));
        
        return true;
    }
    
    public static function delete($key) {
        self::init();
        $filename = self::$cacheDir . md5($key) . '.cache';
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return false;
    }
    
    public static function clear() {
        self::init();
        $files = glob(self::$cacheDir . '*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    public static function remember($key, $callback, $ttl = null) {
        $cached = self::get($key);
        
        if ($cached !== null) {
            return $cached;
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
}

// Helper functions
function cache_get($key) {
    return Cache::get($key);
}

function cache_set($key, $value, $ttl = null) {
    return Cache::set($key, $value, $ttl);
}

function cache_remember($key, $callback, $ttl = null) {
    return Cache::remember($key, $callback, $ttl);
}

function cache_delete($key) {
    return Cache::delete($key);
}

function cache_clear() {
    return Cache::clear();
}
?>
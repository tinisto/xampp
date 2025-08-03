<?php
/**
 * Simple file-based cache implementation for database queries
 */

class QueryCache {
    private static $cacheDir = null;
    private static $defaultTTL = 3600; // 1 hour default
    
    /**
     * Initialize the cache directory
     */
    private static function init() {
        if (self::$cacheDir === null) {
            self::$cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/cache/queries/';
            if (!is_dir(self::$cacheDir)) {
                mkdir(self::$cacheDir, 0755, true);
            }
        }
    }
    
    /**
     * Get a cached query result
     * 
     * @param string $key Cache key
     * @return mixed|false Cached data or false if not found/expired
     */
    public static function get($key) {
        self::init();
        
        $filename = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Check if cache has expired
        if (time() > $data['expires']) {
            unlink($filename);
            return false;
        }
        
        return $data['value'];
    }
    
    /**
     * Set a cached query result
     * 
     * @param string $key Cache key
     * @param mixed $value Data to cache
     * @param int $ttl Time to live in seconds
     */
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
    }
    
    /**
     * Delete a cached item
     * 
     * @param string $key Cache key
     */
    public static function delete($key) {
        self::init();
        
        $filename = self::$cacheDir . md5($key) . '.cache';
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    /**
     * Clear all cached queries
     */
    public static function clear() {
        self::init();
        
        $files = glob(self::$cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
    
    /**
     * Execute a cached database query
     * 
     * @param mysqli $connection Database connection
     * @param string $query SQL query
     * @param int $ttl Cache time to live
     * @return mysqli_result|false
     */
    public static function query($connection, $query, $ttl = null) {
        $cacheKey = 'query_' . md5($query);
        
        // Try to get from cache
        $cached = self::get($cacheKey);
        if ($cached !== false) {
            return $cached;
        }
        
        // Execute query
        $result = $connection->query($query);
        if (!$result) {
            return false;
        }
        
        // Fetch all results for caching
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // Cache the results
        self::set($cacheKey, $data, $ttl);
        
        // Return array result
        return $data;
    }
}

/**
 * Helper function for cached queries
 * 
 * @param mysqli $connection Database connection
 * @param string $query SQL query
 * @param int $ttl Cache time to live (default: 1 hour)
 * @return array|false Query results or false on error
 */
function cached_query($connection, $query, $ttl = 3600) {
    return QueryCache::query($connection, $query, $ttl);
}

/**
 * Clear cache for specific query patterns
 * 
 * @param string $pattern Pattern to match (e.g., 'universities', 'news')
 */
function clear_cache_pattern($pattern) {
    QueryCache::clear(); // For now, clear all cache
    // TODO: Implement pattern-based clearing
}
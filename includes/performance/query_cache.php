<?php
/**
 * Database Query Caching System for 11klassniki
 * Provides intelligent caching for database queries
 */

class QueryCache {
    
    private static $cacheDir = '/cache/queries';
    private static $defaultTTL = 3600; // 1 hour
    private static $maxCacheSize = 104857600; // 100MB
    private static $enabled = true;
    
    /**
     * Initialize query cache
     */
    public static function init() {
        self::createCacheDirectory();
        self::cleanExpiredCache();
    }
    
    /**
     * Get cached query result
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return mixed|null Cached result or null if not found
     */
    public static function get($query, $params = []) {
        if (!self::$enabled) {
            return null;
        }
        
        $cacheKey = self::generateCacheKey($query, $params);
        $cacheFile = self::getCacheFilePath($cacheKey);
        
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        $data = json_decode(file_get_contents($cacheFile), true);
        
        if (!$data || !isset($data['expires_at']) || $data['expires_at'] < time()) {
            // Cache expired
            unlink($cacheFile);
            return null;
        }
        
        // Update access time for LRU
        touch($cacheFile);
        
        return $data['result'];
    }
    
    /**
     * Store query result in cache
     * @param string $query SQL query
     * @param array $params Query parameters
     * @param mixed $result Query result
     * @param int $ttl Time to live in seconds
     */
    public static function set($query, $params, $result, $ttl = null) {
        if (!self::$enabled) {
            return;
        }
        
        if ($ttl === null) {
            $ttl = self::$defaultTTL;
        }
        
        $cacheKey = self::generateCacheKey($query, $params);
        $cacheFile = self::getCacheFilePath($cacheKey);
        
        $data = [
            'query' => $query,
            'params' => $params,
            'result' => $result,
            'cached_at' => time(),
            'expires_at' => time() + $ttl
        ];
        
        // Ensure we don't exceed cache size limits
        self::ensureCacheSize();
        
        file_put_contents($cacheFile, json_encode($data), LOCK_EX);
    }
    
    /**
     * Execute query with caching
     * @param mysqli $connection Database connection
     * @param string $query SQL query
     * @param array $params Query parameters
     * @param int $ttl Cache TTL in seconds
     * @return array Query results
     */
    public static function execute($connection, $query, $params = [], $ttl = null) {
        $startTime = microtime(true);
        
        // Check if query should be cached
        if (!self::shouldCache($query)) {
            return self::executeQuery($connection, $query, $params, $startTime);
        }
        
        // Try to get from cache first
        $cached = self::get($query, $params);
        if ($cached !== null) {
            $duration = microtime(true) - $startTime;
            PerformanceMonitor::recordQuery($query, $duration, array_merge($params, ['cached' => true]));
            return $cached;
        }
        
        // Execute query and cache result
        $result = self::executeQuery($connection, $query, $params, $startTime);
        
        if ($result !== false) {
            self::set($query, $params, $result, $ttl);
        }
        
        return $result;
    }
    
    /**
     * Execute prepared statement with caching
     * @param mysqli $connection Database connection
     * @param string $query SQL query with placeholders
     * @param string $types Parameter types
     * @param array $params Parameters
     * @param int $ttl Cache TTL
     * @return array Query results
     */
    public static function executePrepared($connection, $query, $types, $params, $ttl = null) {
        $startTime = microtime(true);
        
        // Check if query should be cached
        if (!self::shouldCache($query)) {
            return self::executePreparedQuery($connection, $query, $types, $params, $startTime);
        }
        
        // Try to get from cache first
        $cacheParams = array_combine(range(0, count($params) - 1), $params);
        $cached = self::get($query, $cacheParams);
        if ($cached !== null) {
            $duration = microtime(true) - $startTime;
            PerformanceMonitor::recordQuery($query, $duration, array_merge($cacheParams, ['cached' => true]));
            return $cached;
        }
        
        // Execute query and cache result
        $result = self::executePreparedQuery($connection, $query, $types, $params, $startTime);
        
        if ($result !== false) {
            self::set($query, $cacheParams, $result, $ttl);
        }
        
        return $result;
    }
    
    /**
     * Clear cache for specific query pattern
     * @param string $pattern Query pattern (e.g., "SELECT * FROM users")
     */
    public static function clearPattern($pattern) {
        $cacheFiles = glob(self::getCacheDir() . '/*.cache');
        
        foreach ($cacheFiles as $file) {
            $data = json_decode(file_get_contents($file), true);
            
            if ($data && stripos($data['query'], $pattern) !== false) {
                unlink($file);
            }
        }
    }
    
    /**
     * Clear cache for specific table
     * @param string $table Table name
     */
    public static function clearTable($table) {
        self::clearPattern("FROM {$table}");
        self::clearPattern("UPDATE {$table}");
        self::clearPattern("INSERT INTO {$table}");
        self::clearPattern("DELETE FROM {$table}");
    }
    
    /**
     * Clear all cache
     */
    public static function clearAll() {
        $cacheFiles = glob(self::getCacheDir() . '/*.cache');
        
        foreach ($cacheFiles as $file) {
            unlink($file);
        }
    }
    
    /**
     * Get cache statistics
     * @return array Cache stats
     */
    public static function getStats() {
        $cacheDir = self::getCacheDir();
        $cacheFiles = glob($cacheDir . '/*.cache');
        
        $totalSize = 0;
        $expiredCount = 0;
        $validCount = 0;
        $oldestFile = null;
        $newestFile = null;
        
        foreach ($cacheFiles as $file) {
            $size = filesize($file);
            $totalSize += $size;
            
            $data = json_decode(file_get_contents($file), true);
            
            if ($data && $data['expires_at'] < time()) {
                $expiredCount++;
            } else {
                $validCount++;
            }
            
            $mtime = filemtime($file);
            if ($oldestFile === null || $mtime < filemtime($oldestFile)) {
                $oldestFile = $file;
            }
            if ($newestFile === null || $mtime > filemtime($newestFile)) {
                $newestFile = $file;
            }
        }
        
        return [
            'total_files' => count($cacheFiles),
            'valid_files' => $validCount,
            'expired_files' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'oldest_cache' => $oldestFile ? date('Y-m-d H:i:s', filemtime($oldestFile)) : null,
            'newest_cache' => $newestFile ? date('Y-m-d H:i:s', filemtime($newestFile)) : null,
            'enabled' => self::$enabled
        ];
    }
    
    /**
     * Enable or disable caching
     * @param bool $enabled Whether to enable caching
     */
    public static function setEnabled($enabled) {
        self::$enabled = $enabled;
    }
    
    /**
     * Execute actual query
     */
    private static function executeQuery($connection, $query, $params, $startTime) {
        try {
            if (empty($params)) {
                $result = $connection->query($query);
            } else {
                // Simple parameter substitution (not recommended for production without proper escaping)
                $query = self::substituteParams($query, $params);
                $result = $connection->query($query);
            }
            
            if (!$result) {
                ErrorLogger::log('error', 'Query execution failed: ' . $connection->error, [
                    'query' => $query,
                    'params' => $params
                ]);
                return false;
            }
            
            $data = [];
            if ($result instanceof mysqli_result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }
            
            $duration = microtime(true) - $startTime;
            PerformanceMonitor::recordQuery($query, $duration, $params);
            
            return $data;
            
        } catch (Exception $e) {
            ErrorLogger::log('error', 'Query execution exception: ' . $e->getMessage(), [
                'query' => $query,
                'params' => $params
            ]);
            return false;
        }
    }
    
    /**
     * Execute prepared statement
     */
    private static function executePreparedQuery($connection, $query, $types, $params, $startTime) {
        try {
            $stmt = $connection->prepare($query);
            
            if (!$stmt) {
                ErrorLogger::log('error', 'Prepared statement failed: ' . $connection->error, [
                    'query' => $query
                ]);
                return false;
            }
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            
            $stmt->close();
            
            $duration = microtime(true) - $startTime;
            PerformanceMonitor::recordQuery($query, $duration, $params);
            
            return $data;
            
        } catch (Exception $e) {
            ErrorLogger::log('error', 'Prepared query exception: ' . $e->getMessage(), [
                'query' => $query,
                'params' => $params
            ]);
            return false;
        }
    }
    
    /**
     * Generate cache key for query
     */
    private static function generateCacheKey($query, $params) {
        $normalized = self::normalizeQuery($query);
        $key = $normalized . serialize($params);
        return md5($key);
    }
    
    /**
     * Normalize query for consistent caching
     */
    private static function normalizeQuery($query) {
        // Remove extra whitespace and normalize case
        $query = preg_replace('/\s+/', ' ', trim($query));
        return strtoupper($query);
    }
    
    /**
     * Check if query should be cached
     */
    private static function shouldCache($query) {
        $query = strtoupper(trim($query));
        
        // Only cache SELECT queries
        if (strpos($query, 'SELECT') !== 0) {
            return false;
        }
        
        // Don't cache queries with random functions
        $nonCacheablePatterns = [
            'RAND()',
            'NOW()',
            'CURDATE()',
            'CURTIME()',
            'CURRENT_TIMESTAMP',
            'UUID()',
            'CONNECTION_ID()'
        ];
        
        foreach ($nonCacheablePatterns as $pattern) {
            if (stripos($query, $pattern) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Simple parameter substitution (for demonstration - use prepared statements in production)
     */
    private static function substituteParams($query, $params) {
        $index = 0;
        return preg_replace_callback('/\?/', function($matches) use ($params, &$index) {
            return isset($params[$index]) ? "'" . addslashes($params[$index++]) . "'" : $matches[0];
        }, $query);
    }
    
    /**
     * Get cache file path
     */
    private static function getCacheFilePath($key) {
        return self::getCacheDir() . '/' . $key . '.cache';
    }
    
    /**
     * Get cache directory
     */
    private static function getCacheDir() {
        return $_SERVER['DOCUMENT_ROOT'] . self::$cacheDir;
    }
    
    /**
     * Create cache directory
     */
    private static function createCacheDirectory() {
        $cacheDir = self::getCacheDir();
        
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        // Create .htaccess to prevent direct access
        $htaccess = $cacheDir . '/.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\n");
        }
    }
    
    /**
     * Clean expired cache files
     */
    private static function cleanExpiredCache() {
        $cacheFiles = glob(self::getCacheDir() . '/*.cache');
        
        foreach ($cacheFiles as $file) {
            $data = json_decode(file_get_contents($file), true);
            
            if (!$data || $data['expires_at'] < time()) {
                unlink($file);
            }
        }
    }
    
    /**
     * Ensure cache doesn't exceed size limits
     */
    private static function ensureCacheSize() {
        $cacheDir = self::getCacheDir();
        $cacheFiles = glob($cacheDir . '/*.cache');
        
        // Calculate total size
        $totalSize = 0;
        $fileInfo = [];
        
        foreach ($cacheFiles as $file) {
            $size = filesize($file);
            $totalSize += $size;
            $fileInfo[] = [
                'file' => $file,
                'size' => $size,
                'mtime' => filemtime($file)
            ];
        }
        
        // If we're over the limit, remove oldest files
        if ($totalSize > self::$maxCacheSize) {
            // Sort by modification time (oldest first)
            usort($fileInfo, function($a, $b) {
                return $a['mtime'] - $b['mtime'];
            });
            
            foreach ($fileInfo as $info) {
                unlink($info['file']);
                $totalSize -= $info['size'];
                
                if ($totalSize <= self::$maxCacheSize * 0.8) { // Clean to 80% of limit
                    break;
                }
            }
        }
    }
}

// Auto-initialize query cache
QueryCache::init();
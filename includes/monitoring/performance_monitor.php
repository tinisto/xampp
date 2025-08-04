<?php
/**
 * Performance Monitoring System for 11klassniki
 */

class PerformanceMonitor {
    
    private static $timers = [];
    private static $metrics = [];
    private static $queryCount = 0;
    private static $queryTime = 0.0;
    
    /**
     * Start performance timer
     * @param string $name Timer name
     */
    public static function startTimer($name) {
        self::$timers[$name] = [
            'start' => microtime(true),
            'memory_start' => memory_get_usage(true)
        ];
    }
    
    /**
     * End performance timer
     * @param string $name Timer name
     * @return array Performance metrics
     */
    public static function endTimer($name) {
        if (!isset(self::$timers[$name])) {
            return null;
        }
        
        $timer = self::$timers[$name];
        $metrics = [
            'name' => $name,
            'duration' => microtime(true) - $timer['start'],
            'memory_used' => memory_get_usage(true) - $timer['memory_start'],
            'memory_peak' => memory_get_peak_usage(true),
            'timestamp' => time()
        ];
        
        self::$metrics[$name] = $metrics;
        unset(self::$timers[$name]);
        
        // Log slow operations
        if ($metrics['duration'] > 1.0) {
            ErrorLogger::logPerformance($name, $metrics['duration'], [
                'memory_used' => $metrics['memory_used'],
                'slow_operation' => true
            ]);
        }
        
        return $metrics;
    }
    
    /**
     * Get timer duration
     * @param string $name Timer name
     * @return float|null Duration in seconds
     */
    public static function getTimerDuration($name) {
        if (!isset(self::$timers[$name])) {
            return null;
        }
        
        return microtime(true) - self::$timers[$name]['start'];
    }
    
    /**
     * Record database query
     * @param string $query SQL query
     * @param float $duration Query duration
     * @param array $params Query parameters
     */
    public static function recordQuery($query, $duration, $params = []) {
        self::$queryCount++;
        self::$queryTime += $duration;
        
        // Log slow queries
        if ($duration > 0.1) {
            ErrorLogger::logQuery($query, $duration, $params);
        }
    }
    
    /**
     * Get current performance metrics
     * @return array Current metrics
     */
    public static function getCurrentMetrics() {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'query_count' => self::$queryCount,
            'query_time' => self::$queryTime,
            'uptime' => self::getUptime(),
            'active_timers' => array_keys(self::$timers),
            'completed_metrics' => self::$metrics
        ];
    }
    
    /**
     * Get page load metrics
     * @return array Page metrics
     */
    public static function getPageMetrics() {
        if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            return null;
        }
        
        $duration = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        
        return [
            'page_load_time' => $duration,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'query_count' => self::$queryCount,
            'query_time' => self::$queryTime,
            'included_files' => count(get_included_files()),
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
        ];
    }
    
    /**
     * Monitor database connection
     * @param mysqli $connection Database connection
     * @return array Connection metrics
     */
    public static function monitorConnection($connection) {
        if (!$connection || $connection->connect_error) {
            return ['status' => 'error', 'error' => $connection->connect_error ?? 'Unknown error'];
        }
        
        $stats = $connection->get_connection_stats();
        
        return [
            'status' => 'connected',
            'server_info' => $connection->server_info,
            'protocol_version' => $connection->protocol_version,
            'connection_stats' => $stats,
            'thread_id' => $connection->thread_id
        ];
    }
    
    /**
     * Check system health
     * @return array System health metrics
     */
    public static function getSystemHealth() {
        $health = [
            'status' => 'healthy',
            'checks' => []
        ];
        
        // Check disk space
        $diskFree = disk_free_space('.');
        $diskTotal = disk_total_space('.');
        $diskUsage = 1 - ($diskFree / $diskTotal);
        
        $health['checks']['disk_space'] = [
            'status' => $diskUsage < 0.9 ? 'ok' : 'warning',
            'usage_percent' => round($diskUsage * 100, 2),
            'free_space' => self::formatBytes($diskFree),
            'total_space' => self::formatBytes($diskTotal)
        ];
        
        // Check memory usage
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        $health['checks']['memory'] = [
            'status' => 'ok',
            'current' => self::formatBytes($memoryUsage),
            'peak' => self::formatBytes($memoryPeak),
            'limit' => $memoryLimit
        ];
        
        // Check error log size
        $errorLogSize = 0;
        $logFiles = glob($_SERVER['DOCUMENT_ROOT'] . '/logs/*.log');
        foreach ($logFiles as $file) {
            $errorLogSize += filesize($file);
        }
        
        $health['checks']['error_logs'] = [
            'status' => $errorLogSize < 50000000 ? 'ok' : 'warning', // 50MB
            'total_size' => self::formatBytes($errorLogSize),
            'file_count' => count($logFiles)
        ];
        
        // Overall status
        foreach ($health['checks'] as $check) {
            if ($check['status'] === 'warning') {
                $health['status'] = 'warning';
            } elseif ($check['status'] === 'error') {
                $health['status'] = 'error';
                break;
            }
        }
        
        return $health;
    }
    
    /**
     * Profile function execution
     * @param callable $callback Function to profile
     * @param string $name Profile name
     * @return mixed Function return value
     */
    public static function profile($callback, $name = 'anonymous') {
        self::startTimer($name);
        
        try {
            $result = $callback();
            return $result;
        } finally {
            self::endTimer($name);
        }
    }
    
    /**
     * Log request metrics at end of request
     */
    public static function logRequestMetrics() {
        $metrics = self::getPageMetrics();
        
        if ($metrics) {
            ErrorLogger::logPerformance('page_load', $metrics['page_load_time'], $metrics);
        }
    }
    
    /**
     * Get uptime
     */
    private static function getUptime() {
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        }
        
        return 0;
    }
    
    /**
     * Format bytes to human readable
     */
    private static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Register shutdown function to log request metrics
register_shutdown_function([PerformanceMonitor::class, 'logRequestMetrics']);
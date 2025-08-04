<?php
/**
 * Advanced Error Logging and Monitoring System for 11klassniki
 */

class ErrorLogger {
    
    private static $logDir = '/logs';
    private static $maxLogSize = 10485760; // 10MB
    private static $maxLogFiles = 5;
    
    /**
     * Initialize error logger
     */
    public static function init() {
        // Set custom error handler
        set_error_handler([self::class, 'errorHandler']);
        set_exception_handler([self::class, 'exceptionHandler']);
        register_shutdown_function([self::class, 'shutdownHandler']);
        
        // Create logs directory
        self::createLogDirectory();
    }
    
    /**
     * Log error with context
     * @param string $level Error level (error, warning, info, debug)
     * @param string $message Error message
     * @param array $context Additional context
     * @param string $file File where error occurred
     * @param int $line Line number where error occurred
     */
    public static function log($level, $message, $context = [], $file = null, $line = null) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'context' => $context
        ];
        
        // Add stack trace for errors
        if ($level === 'error' || $level === 'critical') {
            $logEntry['stack_trace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        
        self::writeLog($level, $logEntry);
        
        // Send critical errors via email if configured
        if ($level === 'critical' && self::shouldNotify()) {
            self::sendErrorNotification($logEntry);
        }
    }
    
    /**
     * Custom error handler
     */
    public static function errorHandler($severity, $message, $file, $line) {
        // Don't log suppressed errors
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $level = self::getErrorLevel($severity);
        
        self::log($level, $message, [
            'severity' => $severity,
            'error_type' => self::getErrorType($severity)
        ], $file, $line);
        
        // Don't prevent default error handling
        return false;
    }
    
    /**
     * Custom exception handler
     */
    public static function exceptionHandler($exception) {
        self::log('critical', $exception->getMessage(), [
            'exception_class' => get_class($exception),
            'code' => $exception->getCode(),
            'stack_trace' => $exception->getTraceAsString()
        ], $exception->getFile(), $exception->getLine());
    }
    
    /**
     * Shutdown handler for fatal errors
     */
    public static function shutdownHandler() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log('critical', $error['message'], [
                'error_type' => self::getErrorType($error['type']),
                'is_fatal' => true
            ], $error['file'], $error['line']);
        }
    }
    
    /**
     * Log application events
     * @param string $event Event name
     * @param array $data Event data
     */
    public static function logEvent($event, $data = []) {
        self::log('info', "Event: {$event}", $data);
    }
    
    /**
     * Log performance metrics
     * @param string $operation Operation name
     * @param float $duration Duration in seconds
     * @param array $metrics Additional metrics
     */
    public static function logPerformance($operation, $duration, $metrics = []) {
        self::log('info', "Performance: {$operation}", array_merge([
            'duration' => $duration,
            'duration_ms' => round($duration * 1000, 2)
        ], $metrics));
    }
    
    /**
     * Log database queries
     * @param string $query SQL query
     * @param float $duration Query duration
     * @param array $params Query parameters
     */
    public static function logQuery($query, $duration, $params = []) {
        // Only log slow queries or if in debug mode
        if ($duration > 1.0 || self::isDebugMode()) {
            self::log('info', "Database Query", [
                'query' => $query,
                'duration' => $duration,
                'params' => $params,
                'slow_query' => $duration > 1.0
            ]);
        }
    }
    
    /**
     * Get recent errors
     * @param int $limit Number of errors to retrieve
     * @param string $level Minimum error level
     * @return array Recent errors
     */
    public static function getRecentErrors($limit = 50, $level = 'warning') {
        $logFile = self::getLogFilePath('error');
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $errors = [];
        
        // Get last N lines
        $lines = array_slice($lines, -$limit * 2); // Get more lines to filter
        
        foreach (array_reverse($lines) as $line) {
            $data = json_decode($line, true);
            
            if ($data && self::isLevelIncluded($data['level'], $level)) {
                $errors[] = $data;
                
                if (count($errors) >= $limit) {
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Get error statistics
     * @param int $hours Number of hours to analyze
     * @return array Error statistics
     */
    public static function getErrorStats($hours = 24) {
        $logFile = self::getLogFilePath('error');
        
        if (!file_exists($logFile)) {
            return ['total' => 0, 'by_level' => [], 'by_hour' => []];
        }
        
        $cutoff = time() - ($hours * 3600);
        $stats = [
            'total' => 0,
            'by_level' => [],
            'by_hour' => []
        ];
        
        $handle = fopen($logFile, 'r');
        
        while (($line = fgets($handle)) !== false) {
            $data = json_decode($line, true);
            
            if (!$data) continue;
            
            $timestamp = strtotime($data['timestamp']);
            
            if ($timestamp < $cutoff) continue;
            
            $stats['total']++;
            
            // Count by level
            $level = strtolower($data['level']);
            $stats['by_level'][$level] = ($stats['by_level'][$level] ?? 0) + 1;
            
            // Count by hour
            $hour = date('Y-m-d H:00', $timestamp);
            $stats['by_hour'][$hour] = ($stats['by_hour'][$hour] ?? 0) + 1;
        }
        
        fclose($handle);
        
        // Sort hours
        ksort($stats['by_hour']);
        
        return $stats;
    }
    
    /**
     * Clear old logs
     * @param int $days Days to keep logs
     */
    public static function clearOldLogs($days = 30) {
        $logFiles = glob(self::getLogDir() . '/*.log');
        $cutoff = time() - ($days * 24 * 3600);
        
        foreach ($logFiles as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
    
    /**
     * Write log entry to file
     */
    private static function writeLog($level, $logEntry) {
        $logFile = self::getLogFilePath($level);
        
        // Rotate log if too large
        self::rotateLogIfNeeded($logFile);
        
        $logLine = json_encode($logEntry) . "\n";
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get log file path
     */
    private static function getLogFilePath($level) {
        $date = date('Y-m-d');
        
        switch ($level) {
            case 'critical':
            case 'error':
                return self::getLogDir() . "/error-{$date}.log";
            case 'warning':
                return self::getLogDir() . "/warning-{$date}.log";
            case 'info':
            case 'debug':
                return self::getLogDir() . "/app-{$date}.log";
            default:
                return self::getLogDir() . "/general-{$date}.log";
        }
    }
    
    /**
     * Get log directory
     */
    private static function getLogDir() {
        return $_SERVER['DOCUMENT_ROOT'] . self::$logDir;
    }
    
    /**
     * Create log directory if needed
     */
    private static function createLogDirectory() {
        $logDir = self::getLogDir();
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Create .htaccess to prevent direct access
        $htaccess = $logDir . '/.htaccess';
        
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\n");
        }
    }
    
    /**
     * Rotate log file if too large
     */
    private static function rotateLogIfNeeded($logFile) {
        if (!file_exists($logFile) || filesize($logFile) < self::$maxLogSize) {
            return;
        }
        
        // Move current log to backup
        for ($i = self::$maxLogFiles - 1; $i > 0; $i--) {
            $oldFile = $logFile . '.' . $i;
            $newFile = $logFile . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }
        
        rename($logFile, $logFile . '.1');
        
        // Remove oldest log
        $oldest = $logFile . '.' . self::$maxLogFiles;
        if (file_exists($oldest)) {
            unlink($oldest);
        }
    }
    
    /**
     * Get error level from severity
     */
    private static function getErrorLevel($severity) {
        switch ($severity) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
                return 'critical';
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return 'warning';
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'info';
            default:
                return 'error';
        }
    }
    
    /**
     * Get error type name
     */
    private static function getErrorType($type) {
        $types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED'
        ];
        
        return $types[$type] ?? 'UNKNOWN';
    }
    
    /**
     * Check if level should be included
     */
    private static function isLevelIncluded($errorLevel, $minLevel) {
        $levels = ['debug' => 0, 'info' => 1, 'warning' => 2, 'error' => 3, 'critical' => 4];
        
        $errorLevelNum = $levels[strtolower($errorLevel)] ?? 3;
        $minLevelNum = $levels[strtolower($minLevel)] ?? 2;
        
        return $errorLevelNum >= $minLevelNum;
    }
    
    /**
     * Check if in debug mode
     */
    private static function isDebugMode() {
        return defined('DEBUG') && DEBUG === true;
    }
    
    /**
     * Check if should send notifications
     */
    private static function shouldNotify() {
        // Only notify during business hours and limit frequency
        $hour = (int)date('H');
        return $hour >= 9 && $hour <= 18;
    }
    
    /**
     * Send error notification (placeholder)
     */
    private static function sendErrorNotification($logEntry) {
        // This would send email/slack notification in production
        // For now, just log that we would notify
        error_log("CRITICAL ERROR NOTIFICATION: " . $logEntry['message']);
    }
}

// Auto-initialize error logger
ErrorLogger::init();
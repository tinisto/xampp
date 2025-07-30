<?php
class ErrorHandler {
    private static $logDir = __DIR__ . '/../logs/';
    private static $errorLogFile = 'errors.log';
    private static $debugMode = false;
    
    public static function init($debugMode = false) {
        self::$debugMode = $debugMode;
        
        // Create logs directory if not exists
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        
        // Set error reporting
        if ($debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 0);
        }
        
        // Set custom error handler
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleError($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    
    public static function handleException($exception) {
        $error = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'time' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'url' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI'
        ];
        
        // Log the error
        self::logError($error);
        
        // Display error page
        if (!self::$debugMode) {
            self::displayErrorPage($exception);
        } else {
            self::displayDebugError($exception);
        }
    }
    
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            self::handleException($exception);
        }
    }
    
    private static function logError($error) {
        $logFile = self::$logDir . self::$errorLogFile;
        $logEntry = json_encode($error) . PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Rotate log if too large (10MB)
        if (filesize($logFile) > 10485760) {
            rename($logFile, $logFile . '.' . date('Y-m-d-H-i-s'));
        }
    }
    
    private static function displayErrorPage($exception) {
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code(500);
        
        // Check if custom error page exists
        $errorPage = __DIR__ . '/../pages/error/500.php';
        if (file_exists($errorPage)) {
            include $errorPage;
        } else {
            echo '<!DOCTYPE html>
<html>
<head>
    <title>Server Error</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #dc3545; }
        p { color: #666; }
    </style>
</head>
<body>
    <h1>Oops! Something went wrong</h1>
    <p>We\'re sorry, but something went wrong on our end.</p>
    <p>Please try again later or contact support if the problem persists.</p>
</body>
</html>';
        }
        
        exit;
    }
    
    private static function displayDebugError($exception) {
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code(500);
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Debug Error</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        .error { background: #fee; padding: 20px; border: 1px solid #fcc; }
        .trace { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        h2 { color: #c00; }
        .file { color: #666; }
        .line { color: #c00; font-weight: bold; }
    </style>
</head>
<body>
    <div class="error">
        <h2>' . get_class($exception) . ': ' . htmlspecialchars($exception->getMessage()) . '</h2>
        <p class="file">File: ' . htmlspecialchars($exception->getFile()) . '</p>
        <p class="line">Line: ' . $exception->getLine() . '</p>
        
        <h3>Stack Trace:</h3>
        <pre class="trace">' . htmlspecialchars($exception->getTraceAsString()) . '</pre>
    </div>
</body>
</html>';
        
        exit;
    }
    
    public static function log($message, $level = 'info', $context = []) {
        $logFile = self::$logDir . date('Y-m-d') . '-app.log';
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context
        ];
        
        $logLine = json_encode($logEntry) . PHP_EOL;
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}
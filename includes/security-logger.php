<?php
/**
 * Security Logger
 * 
 * Logs security-related events for monitoring and analysis
 */

class SecurityLogger {
    private $connection;
    private $logTable = 'security_logs';
    
    // Event types
    const EVENT_LOGIN_SUCCESS = 'login_success';
    const EVENT_LOGIN_FAILED = 'login_failed';
    const EVENT_LOGIN_BLOCKED = 'login_blocked';
    const EVENT_CSRF_FAILED = 'csrf_failed';
    const EVENT_SQL_INJECTION = 'sql_injection_attempt';
    const EVENT_XSS_ATTEMPT = 'xss_attempt';
    const EVENT_UNAUTHORIZED = 'unauthorized_access';
    const EVENT_SUSPICIOUS = 'suspicious_activity';
    const EVENT_PASSWORD_RESET = 'password_reset';
    const EVENT_ACCOUNT_LOCKED = 'account_locked';
    
    public function __construct($connection) {
        $this->connection = $connection;
        $this->createTableIfNotExists();
    }
    
    /**
     * Create security logs table if it doesn't exist
     */
    private function createTableIfNotExists() {
        $query = "CREATE TABLE IF NOT EXISTS {$this->logTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_type VARCHAR(50) NOT NULL,
            user_id INT NULL,
            email VARCHAR(255) NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            request_uri TEXT,
            details TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_event_type (event_type),
            INDEX idx_user_id (user_id),
            INDEX idx_ip (ip_address),
            INDEX idx_created (created_at)
        )";
        $this->connection->query($query);
    }
    
    /**
     * Log a security event
     * 
     * @param string $eventType
     * @param array $data
     */
    public function log($eventType, $data = []) {
        $userId = $data['user_id'] ?? $_SESSION['user_id'] ?? null;
        $email = $data['email'] ?? $_SESSION['email'] ?? null;
        $ipAddress = $this->getClientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $details = isset($data['details']) ? json_encode($data['details']) : null;
        
        $stmt = $this->connection->prepare(
            "INSERT INTO {$this->logTable} 
            (event_type, user_id, email, ip_address, user_agent, request_uri, details) 
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            "sisssss",
            $eventType,
            $userId,
            $email,
            $ipAddress,
            $userAgent,
            $requestUri,
            $details
        );
        
        $stmt->execute();
        $stmt->close();
        
        // For critical events, also log to error log
        if (in_array($eventType, [
            self::EVENT_SQL_INJECTION,
            self::EVENT_XSS_ATTEMPT,
            self::EVENT_UNAUTHORIZED,
            self::EVENT_ACCOUNT_LOCKED
        ])) {
            error_log("SECURITY ALERT: $eventType from IP $ipAddress - " . json_encode($data));
        }
    }
    
    /**
     * Get client IP address
     * 
     * @return string
     */
    private function getClientIp() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get recent security events
     * 
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getRecentEvents($limit = 100, $filters = []) {
        $whereClauses = [];
        $params = [];
        $types = '';
        
        if (!empty($filters['event_type'])) {
            $whereClauses[] = "event_type = ?";
            $params[] = $filters['event_type'];
            $types .= 's';
        }
        
        if (!empty($filters['user_id'])) {
            $whereClauses[] = "user_id = ?";
            $params[] = $filters['user_id'];
            $types .= 'i';
        }
        
        if (!empty($filters['ip_address'])) {
            $whereClauses[] = "ip_address = ?";
            $params[] = $filters['ip_address'];
            $types .= 's';
        }
        
        $whereClause = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
        
        $query = "SELECT * FROM {$this->logTable} 
                  $whereClause 
                  ORDER BY created_at DESC 
                  LIMIT ?";
        
        $stmt = $this->connection->prepare($query);
        
        if (!empty($params)) {
            $params[] = $limit;
            $types .= 'i';
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt->bind_param('i', $limit);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $events = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['details']) {
                $row['details'] = json_decode($row['details'], true);
            }
            $events[] = $row;
        }
        
        $stmt->close();
        return $events;
    }
    
    /**
     * Get security statistics
     * 
     * @param string $period ('day', 'week', 'month')
     * @return array
     */
    public function getStatistics($period = 'day') {
        $interval = match($period) {
            'week' => '7 DAY',
            'month' => '30 DAY',
            default => '1 DAY'
        };
        
        $query = "SELECT 
                    event_type,
                    COUNT(*) as count,
                    COUNT(DISTINCT ip_address) as unique_ips,
                    COUNT(DISTINCT user_id) as unique_users
                  FROM {$this->logTable}
                  WHERE created_at > DATE_SUB(NOW(), INTERVAL $interval)
                  GROUP BY event_type
                  ORDER BY count DESC";
        
        $result = $this->connection->query($query);
        
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[$row['event_type']] = [
                'count' => $row['count'],
                'unique_ips' => $row['unique_ips'],
                'unique_users' => $row['unique_users']
            ];
        }
        
        return $stats;
    }
    
    /**
     * Clean old logs
     * 
     * @param int $days
     */
    public function cleanOldLogs($days = 90) {
        $stmt = $this->connection->prepare(
            "DELETE FROM {$this->logTable} 
             WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)"
        );
        $stmt->bind_param('i', $days);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * Helper function to log security events
 * 
 * @param mysqli $connection
 * @param string $eventType
 * @param array $data
 */
function logSecurityEvent($connection, $eventType, $data = []) {
    $logger = new SecurityLogger($connection);
    $logger->log($eventType, $data);
}

/**
 * Helper function to detect and log SQL injection attempts
 * 
 * @param string $input
 * @param mysqli $connection
 * @return bool
 */
function detectSQLInjection($input, $connection = null) {
    $patterns = [
        '/(\bunion\b.*\bselect\b)/i',
        '/(\bor\b.*\b1\s*=\s*1\b)/i',
        '/(\band\b.*\b1\s*=\s*1\b)/i',
        '/(\bdrop\b.*\btable\b)/i',
        '/(\binsert\b.*\binto\b)/i',
        '/(\bupdate\b.*\bset\b)/i',
        '/(\bdelete\b.*\bfrom\b)/i',
        '/(--|\#|\/\*)/i',
        '/(\bexec\b|\bexecute\b)/i',
        '/(\bscript\b.*\b>\b)/i',
        '/(\bjavascript\b:)/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            if ($connection) {
                logSecurityEvent($connection, SecurityLogger::EVENT_SQL_INJECTION, [
                    'details' => [
                        'input' => substr($input, 0, 200),
                        'pattern' => $pattern
                    ]
                ]);
            }
            return true;
        }
    }
    
    return false;
}

/**
 * Helper function to detect and log XSS attempts
 * 
 * @param string $input
 * @param mysqli $connection
 * @return bool
 */
function detectXSSAttempt($input, $connection = null) {
    $patterns = [
        '/<script[^>]*>.*?<\/script>/is',
        '/<iframe[^>]*>.*?<\/iframe>/is',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<img[^>]*src[^>]*>/i',
        '/<object[^>]*>/i',
        '/<embed[^>]*>/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            if ($connection) {
                logSecurityEvent($connection, SecurityLogger::EVENT_XSS_ATTEMPT, [
                    'details' => [
                        'input' => substr($input, 0, 200),
                        'pattern' => $pattern
                    ]
                ]);
            }
            return true;
        }
    }
    
    return false;
}
?>
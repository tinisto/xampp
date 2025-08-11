<?php
/**
 * Rate Limiter for Login Attempts
 * 
 * Prevents brute force attacks by limiting login attempts
 * Uses database to track failed attempts
 */

class RateLimiter {
    private $connection;
    private $maxAttempts = 5;
    private $blockDuration = 900; // 15 minutes in seconds
    
    public function __construct($connection) {
        $this->connection = $connection;
        $this->createTableIfNotExists();
    }
    
    /**
     * Create rate limit table if it doesn't exist
     */
    private function createTableIfNotExists() {
        $query = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255) NOT NULL,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip (ip_address),
            INDEX idx_email (email),
            INDEX idx_time (attempt_time)
        )";
        $this->connection->query($query);
    }
    
    /**
     * Check if user is rate limited
     * 
     * @param string $identifier Email or IP address
     * @return array ['limited' => bool, 'remaining_time' => int]
     */
    public function isRateLimited($identifier) {
        $ip = $this->getClientIp();
        
        // Clean old attempts
        $this->cleanOldAttempts();
        
        // Check attempts by IP
        $stmt = $this->connection->prepare(
            "SELECT COUNT(*) as attempts, 
                    MIN(attempt_time) as first_attempt 
             FROM login_attempts 
             WHERE ip_address = ? 
             AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $stmt->bind_param("si", $ip, $this->blockDuration);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        if ($data['attempts'] >= $this->maxAttempts) {
            $firstAttemptTime = strtotime($data['first_attempt']);
            $blockEndTime = $firstAttemptTime + $this->blockDuration;
            $remainingTime = $blockEndTime - time();
            
            if ($remainingTime > 0) {
                return [
                    'limited' => true,
                    'remaining_time' => $remainingTime,
                    'remaining_minutes' => ceil($remainingTime / 60)
                ];
            }
        }
        
        // Also check by email if provided
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $stmt = $this->connection->prepare(
                "SELECT COUNT(*) as attempts, 
                        MIN(attempt_time) as first_attempt 
                 FROM login_attempts 
                 WHERE email = ? 
                 AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)"
            );
            $stmt->bind_param("si", $identifier, $this->blockDuration);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            
            if ($data['attempts'] >= $this->maxAttempts) {
                $firstAttemptTime = strtotime($data['first_attempt']);
                $blockEndTime = $firstAttemptTime + $this->blockDuration;
                $remainingTime = $blockEndTime - time();
                
                if ($remainingTime > 0) {
                    return [
                        'limited' => true,
                        'remaining_time' => $remainingTime,
                        'remaining_minutes' => ceil($remainingTime / 60)
                    ];
                }
            }
        }
        
        return ['limited' => false, 'remaining_time' => 0];
    }
    
    /**
     * Record a failed login attempt
     * 
     * @param string $email
     */
    public function recordFailedAttempt($email) {
        $ip = $this->getClientIp();
        
        $stmt = $this->connection->prepare(
            "INSERT INTO login_attempts (ip_address, email) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $ip, $email);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Clear login attempts for a user (after successful login)
     * 
     * @param string $email
     */
    public function clearAttempts($email) {
        $ip = $this->getClientIp();
        
        $stmt = $this->connection->prepare(
            "DELETE FROM login_attempts WHERE ip_address = ? OR email = ?"
        );
        $stmt->bind_param("ss", $ip, $email);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Clean old attempts from database
     */
    private function cleanOldAttempts() {
        $stmt = $this->connection->prepare(
            "DELETE FROM login_attempts 
             WHERE attempt_time < DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $cleanupTime = $this->blockDuration * 2; // Keep records for 2x block duration
        $stmt->bind_param("i", $cleanupTime);
        $stmt->execute();
        $stmt->close();
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
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get remaining attempts
     * 
     * @param string $identifier
     * @return int
     */
    public function getRemainingAttempts($identifier) {
        $ip = $this->getClientIp();
        
        $stmt = $this->connection->prepare(
            "SELECT COUNT(*) as attempts 
             FROM login_attempts 
             WHERE (ip_address = ? OR email = ?) 
             AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $stmt->bind_param("ssi", $ip, $identifier, $this->blockDuration);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return max(0, $this->maxAttempts - $data['attempts']);
    }
}

/**
 * Helper function to check rate limit
 * 
 * @param mysqli $connection
 * @param string $identifier
 * @return array
 */
function checkRateLimit($connection, $identifier) {
    $rateLimiter = new RateLimiter($connection);
    return $rateLimiter->isRateLimited($identifier);
}

/**
 * Helper function to record failed attempt
 * 
 * @param mysqli $connection
 * @param string $email
 */
function recordFailedLogin($connection, $email) {
    $rateLimiter = new RateLimiter($connection);
    $rateLimiter->recordFailedAttempt($email);
}

/**
 * Helper function to clear attempts after successful login
 * 
 * @param mysqli $connection
 * @param string $email
 */
function clearLoginAttempts($connection, $email) {
    $rateLimiter = new RateLimiter($connection);
    $rateLimiter->clearAttempts($email);
}
?>
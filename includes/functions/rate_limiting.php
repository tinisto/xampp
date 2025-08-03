<?php
/**
 * Rate limiting functions to prevent brute force attacks
 */

/**
 * Check if user has exceeded rate limit
 * @param string $identifier - IP address or email
 * @param string $action - login, registration, reset_password
 * @param int $maxAttempts - Maximum attempts allowed
 * @param int $timeWindow - Time window in seconds
 * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => timestamp]
 */
function checkRateLimit($identifier, $action, $maxAttempts = 5, $timeWindow = 900) {
    global $connection;
    
    // Clean old attempts
    $cleanupTime = time() - $timeWindow;
    $cleanupStmt = $connection->prepare("DELETE FROM rate_limits WHERE action = ? AND timestamp < ?");
    $cleanupStmt->bind_param("si", $action, $cleanupTime);
    $cleanupStmt->execute();
    
    // Count recent attempts
    $countStmt = $connection->prepare("SELECT COUNT(*) as attempts FROM rate_limits WHERE identifier = ? AND action = ? AND timestamp > ?");
    $countStmt->bind_param("ssi", $identifier, $action, $cleanupTime);
    $countStmt->execute();
    $result = $countStmt->get_result();
    $row = $result->fetch_assoc();
    $attempts = $row['attempts'];
    
    $allowed = $attempts < $maxAttempts;
    $remaining = max(0, $maxAttempts - $attempts);
    $resetTime = time() + $timeWindow;
    
    return [
        'allowed' => $allowed,
        'remaining' => $remaining,
        'reset_time' => $resetTime,
        'message' => $allowed ? '' : "Слишком много попыток. Попробуйте через " . ceil($timeWindow / 60) . " минут."
    ];
}

/**
 * Record an attempt
 * @param string $identifier - IP address or email
 * @param string $action - login, registration, reset_password
 */
function recordAttempt($identifier, $action) {
    global $connection;
    
    $timestamp = time();
    $stmt = $connection->prepare("INSERT INTO rate_limits (identifier, action, timestamp) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $identifier, $action, $timestamp);
    $stmt->execute();
}

/**
 * Reset rate limit for a specific identifier
 * @param string $identifier - IP address or email
 * @param string $action - login, registration, reset_password
 */
function resetRateLimit($identifier, $action) {
    global $connection;
    
    $stmt = $connection->prepare("DELETE FROM rate_limits WHERE identifier = ? AND action = ?");
    $stmt->bind_param("ss", $identifier, $action);
    $stmt->execute();
}

/**
 * Get user's IP address
 * @return string
 */
function getUserIP() {
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        // Cloudflare
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Behind proxy
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } else {
        // Direct connection
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

/**
 * Create rate limits table if it doesn't exist
 */
function createRateLimitsTable() {
    global $connection;
    
    $sql = "CREATE TABLE IF NOT EXISTS rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(255) NOT NULL,
        action VARCHAR(50) NOT NULL,
        timestamp INT NOT NULL,
        INDEX idx_identifier_action (identifier, action),
        INDEX idx_timestamp (timestamp)
    )";
    
    $connection->query($sql);
}
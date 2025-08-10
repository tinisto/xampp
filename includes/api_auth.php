<?php
/**
 * API Authentication System
 * JWT-like token system for mobile API authentication
 */

class ApiAuth {
    private static $secretKey = 'your-secret-key-change-in-production';
    private static $issuer = '11klassniki.ru';
    private static $audience = 'mobile-app';
    
    /**
     * Generate JWT-like token for user
     */
    public static function generateToken($userId) {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        $payload = [
            'iss' => self::$issuer,
            'aud' => self::$audience,
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60), // 24 hours
            'user_id' => $userId
        ];
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secretKey, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    /**
     * Verify token and return user data
     */
    public static function verifyToken() {
        $token = self::getBearerToken();
        
        if (!$token) {
            return null;
        }
        
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verify signature
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secretKey, true);
        $signatureCheck = self::base64UrlEncode($signature);
        
        if (!hash_equals($signatureEncoded, $signatureCheck)) {
            return null;
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        if (!$payload) {
            return null;
        }
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }
        
        // Check issuer and audience
        if ($payload['iss'] !== self::$issuer || $payload['aud'] !== self::$audience) {
            return null;
        }
        
        // Get user data
        if (!isset($payload['user_id'])) {
            return null;
        }
        
        $user = db_fetch_one("SELECT * FROM users WHERE id = ? AND is_active = 1", [$payload['user_id']]);
        
        return $user ?: null;
    }
    
    /**
     * Get Bearer token from request headers
     */
    private static function getBearerToken() {
        $headers = apache_request_headers();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        } else {
            return null;
        }
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Invalidate token (for logout)
     * In a production app, you'd maintain a blacklist of invalidated tokens
     */
    public static function invalidateToken() {
        // In a simple implementation, we just rely on the client discarding the token
        // For enhanced security, implement a token blacklist in the database
        return true;
    }
    
    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    /**
     * Get current user ID from token
     */
    public static function getCurrentUserId() {
        $user = self::verifyToken();
        return $user ? $user['id'] : null;
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        $user = self::verifyToken();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Require specific role or return error
     */
    public static function requireRole($role) {
        if (!self::hasRole($role)) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }
    }
}
?>
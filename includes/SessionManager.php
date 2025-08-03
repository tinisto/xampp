<?php
/**
 * Centralized Session Management Class
 * Handles all session operations in a secure and consistent manner
 */
class SessionManager {
    
    /**
     * Start session if not already started
     * @return bool True if session was started successfully
     */
    public static function start(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            return session_start();
        }
        return true;
    }
    
    /**
     * Check if session is active
     * @return bool True if session is active
     */
    public static function isActive(): bool {
        return session_status() === PHP_SESSION_ACTIVE;
    }
    
    /**
     * Get session value
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Session value or default
     */
    public static function get(string $key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Set session value
     * @param string $key Session key
     * @param mixed $value Value to store
     */
    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Remove session value
     * @param string $key Session key to remove
     */
    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Check if session key exists
     * @param string $key Session key
     * @return bool True if key exists
     */
    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Get and remove session value (flash data)
     * @param string $key Session key
     * @param mixed $default Default value
     * @return mixed Value or default
     */
    public static function flash(string $key, $default = null) {
        $value = self::get($key, $default);
        self::remove($key);
        return $value;
    }
    
    /**
     * Clear all session data
     */
    public static function clear(): void {
        self::start();
        $_SESSION = [];
    }
    
    /**
     * Destroy session completely
     */
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
    
    /**
     * Regenerate session ID for security
     * @param bool $deleteOldSession Whether to delete old session
     */
    public static function regenerate(bool $deleteOldSession = true): void {
        self::start();
        session_regenerate_id($deleteOldSession);
    }
    
    // Authentication helpers
    
    /**
     * Check if user is logged in
     * @return bool True if user is authenticated
     */
    public static function isLoggedIn(): bool {
        return self::has('user_id') && self::get('logged_in') === true;
    }
    
    /**
     * Get current user ID
     * @return int|null User ID or null if not logged in
     */
    public static function getUserId(): ?int {
        return self::isLoggedIn() ? (int)self::get('user_id') : null;
    }
    
    /**
     * Get current username
     * @return string|null Username or null if not logged in
     */
    public static function getUsername(): ?string {
        return self::isLoggedIn() ? self::get('username') : null;
    }
    
    /**
     * Get current user email
     * @return string|null Email or null if not logged in
     */
    public static function getUserEmail(): ?string {
        return self::isLoggedIn() ? self::get('email') : null;
    }
    
    /**
     * Login user
     * @param int $userId User ID
     * @param string $username Username
     * @param string $email User email
     * @param array $additionalData Additional user data
     */
    public static function login(int $userId, string $username, string $email, array $additionalData = []): void {
        // Regenerate session ID for security
        self::regenerate();
        
        // Set core authentication data
        self::set('user_id', $userId);
        self::set('username', $username);
        self::set('email', $email);
        self::set('logged_in', true);
        self::set('login_time', time());
        
        // Set additional user data
        foreach ($additionalData as $key => $value) {
            self::set($key, $value);
        }
    }
    
    /**
     * Logout user
     */
    public static function logout(): void {
        // Clear user-specific data but keep session structure
        $keysToRemove = ['user_id', 'username', 'email', 'logged_in', 'login_time', 'avatar', 'role', 'occupation'];
        
        foreach ($keysToRemove as $key) {
            self::remove($key);
        }
        
        // Regenerate session ID
        self::regenerate();
    }
    
    // Error and message helpers
    
    /**
     * Set error message
     * @param string $message Error message
     */
    public static function setError(string $message): void {
        self::set('error', $message);
    }
    
    /**
     * Get and clear error message
     * @return string|null Error message or null
     */
    public static function getError(): ?string {
        return self::flash('error');
    }
    
    /**
     * Set success message
     * @param string $message Success message
     */
    public static function setSuccess(string $message): void {
        self::set('success', $message);
    }
    
    /**
     * Get and clear success message
     * @return string|null Success message or null
     */
    public static function getSuccess(): ?string {
        return self::flash('success');
    }
    
    /**
     * Set form data for persistence across redirects
     * @param array $data Form data
     */
    public static function setFormData(array $data): void {
        self::set('oldData', $data);
    }
    
    /**
     * Get and clear form data
     * @return array Form data or empty array
     */
    public static function getFormData(): array {
        return self::flash('oldData', []);
    }
    
    /**
     * Get session information for debugging
     * @return array Session debug info
     */
    public static function getDebugInfo(): array {
        return [
            'status' => session_status(),
            'id' => session_id(),
            'name' => session_name(),
            'cookie_params' => session_get_cookie_params(),
            'keys' => array_keys($_SESSION ?? [])
        ];
    }
}
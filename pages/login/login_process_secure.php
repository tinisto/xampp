<?php
/**
 * Secure Login Process with modern security features
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security/security_bootstrap.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Validate POST request with CSRF and rate limiting
SecurityBootstrap::validatePOST('login', 'login_attempt', 5, 900); // 5 attempts per 15 minutes

// Sanitize input data
$sanitized = SecurityBootstrap::sanitizePOST([
    'email' => ['type' => 'email'],
    'password' => ['type' => 'string'],
    'remember' => ['type' => 'string']
]);

$email = $sanitized['email'];
$password = $sanitized['password'];
$remember = !empty($sanitized['remember']);

// Validate required fields
if (!$email) {
    SecurityBootstrap::logSecurityEvent('Invalid login attempt - invalid email', ['email' => $_POST['email'] ?? '']);
    $_SESSION['error'] = 'Неверный формат email';
    header('Location: /login');
    exit;
}

if (empty($password)) {
    SecurityBootstrap::logSecurityEvent('Invalid login attempt - empty password', ['email' => $email]);
    $_SESSION['error'] = 'Пароль не может быть пустым';
    header('Location: /login');
    exit;
}

try {
    // Check user credentials
    $stmt = $connection->prepare("SELECT id, first_name, last_name, email, password, is_active, role, failed_login_attempts, last_failed_login FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        SecurityBootstrap::logSecurityEvent('Login attempt - user not found', ['email' => $email]);
        $_SESSION['error'] = 'Неверные учетные данные';
        header('Location: /login');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if account is active
    if (!$user['is_active']) {
        SecurityBootstrap::logSecurityEvent('Login attempt - inactive account', ['user_id' => $user['id'], 'email' => $email]);
        $_SESSION['error'] = 'Аккаунт заблокирован. Обратитесь к администратору.';
        header('Location: /login');
        exit;
    }
    
    // Check for account lockout (too many failed attempts)
    $failed_attempts = $user['failed_login_attempts'] ?: 0;
    $last_failed = $user['last_failed_login'];
    
    if ($failed_attempts >= 5) {
        $lockout_time = strtotime($last_failed) + (15 * 60); // 15 minutes lockout
        if (time() < $lockout_time) {
            SecurityBootstrap::logSecurityEvent('Login attempt - account locked', ['user_id' => $user['id'], 'email' => $email]);
            $remaining = ceil(($lockout_time - time()) / 60);
            $_SESSION['error'] = "Аккаунт временно заблокирован. Попробуйте через {$remaining} минут.";
            header('Location: /login');
            exit;
        } else {
            // Reset failed attempts after lockout period
            $reset_stmt = $connection->prepare("UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL WHERE id = ?");
            $reset_stmt->bind_param("i", $user['id']);
            $reset_stmt->execute();
            $failed_attempts = 0;
        }
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Increment failed login attempts
        $new_failed_attempts = $failed_attempts + 1;
        $update_stmt = $connection->prepare("UPDATE users SET failed_login_attempts = ?, last_failed_login = NOW() WHERE id = ?");
        $update_stmt->bind_param("ii", $new_failed_attempts, $user['id']);
        $update_stmt->execute();
        
        SecurityBootstrap::logSecurityEvent('Login attempt - wrong password', [
            'user_id' => $user['id'], 
            'email' => $email,
            'failed_attempts' => $new_failed_attempts
        ]);
        
        $_SESSION['error'] = 'Неверные учетные данные';
        header('Location: /login');
        exit;
    }
    
    // Successful login - reset failed attempts
    if ($failed_attempts > 0) {
        $reset_stmt = $connection->prepare("UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL WHERE id = ?");
        $reset_stmt->bind_param("i", $user['id']);
        $reset_stmt->execute();
    }
    
    // Update last login
    $update_login_stmt = $connection->prepare("UPDATE users SET last_login = NOW(), last_login_ip = ? WHERE id = ?");
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $update_login_stmt->bind_param("si", $ip, $user['id']);
    $update_login_stmt->execute();
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'] ?: 'user';
    $_SESSION['is_authenticated'] = true;
    
    // Clear rate limiting on successful login
    RateLimiter::clearAttempts('login_attempt');
    
    // Set remember me cookie if requested
    if ($remember) {
        $remember_token = bin2hex(random_bytes(32));
        
        // Store remember token in database
        $remember_stmt = $connection->prepare("UPDATE users SET remember_token = ?, remember_token_expires = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = ?");
        $remember_stmt->bind_param("si", $remember_token, $user['id']);
        $remember_stmt->execute();
        
        // Set cookie for 30 days
        setcookie('remember_token', $remember_token, time() + (30 * 24 * 60 * 60), '/', '', SecurityHeaders::isHTTPS(), true);
    }
    
    SecurityBootstrap::logSecurityEvent('Successful login', [
        'user_id' => $user['id'],
        'email' => $email,
        'remember' => $remember
    ]);
    
    $_SESSION['success'] = 'Добро пожаловать!';
    
    // Redirect to home page
    header('Location: /');
    exit;
    
} catch (Exception $e) {
    SecurityBootstrap::logSecurityEvent('Login error', [
        'email' => $email,
        'error' => $e->getMessage()
    ]);
    
    $_SESSION['error'] = 'Произошла ошибка при входе. Попробуйте позже.';
    header('Location: /login');
    exit;
}
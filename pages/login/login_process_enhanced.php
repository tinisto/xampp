<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/rate_limiting.php';

// Create rate limits table if needed
createRateLimitsTable();

// CSRF validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Недействительный токен безопасности';
    header('Location: /login');
    exit;
}

// Get user IP for rate limiting
$userIP = getUserIP();

// Check rate limit
$rateLimit = checkRateLimit($userIP, 'login', 5, 900); // 5 attempts per 15 minutes
if (!$rateLimit['allowed']) {
    $_SESSION['error'] = $rateLimit['message'];
    header('Location: /login');
    exit;
}

// Validate input
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) ? true : false;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Неверный формат email';
    recordAttempt($userIP, 'login');
    header('Location: /login');
    exit;
}

if (empty($password)) {
    $_SESSION['error'] = 'Пароль не может быть пустым';
    recordAttempt($userIP, 'login');
    header('Location: /login');
    exit;
}

try {
    // Check user credentials
    $stmt = $connection->prepare("SELECT id, firstname, lastname, email, password, is_active, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Неверный email или пароль';
        $_SESSION['old_email'] = $email;
        recordAttempt($userIP, 'login');
        header('Location: /login');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Неверный email или пароль';
        $_SESSION['old_email'] = $email;
        recordAttempt($userIP, 'login');
        header('Location: /login');
        exit;
    }
    
    // Check if account is activated
    if ($user['is_active'] != 1) {
        $_SESSION['error'] = 'Ваш аккаунт не активирован. Проверьте email для активации.';
        $_SESSION['old_email'] = $email;
        recordAttempt($userIP, 'login');
        header('Location: /login');
        exit;
    }
    
    // Login successful - reset rate limit
    resetRateLimit($userIP, 'login');
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_firstname'] = $user['firstname'];
    $_SESSION['user_lastname'] = $user['lastname'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    // Handle "Remember Me"
    if ($remember) {
        // Generate a secure token
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        
        // Store in database
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        $stmt = $connection->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user['id'], $hashedToken, $expiry);
        $stmt->execute();
        
        // Set cookie
        setcookie('remember_token', $token, $expiry, '/', '', true, true);
    }
    
    // Update last login
    $stmt = $connection->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    
    // Redirect based on role
    if ($user['role'] === 'admin') {
        header('Location: /dashboard');
    } else {
        header('Location: /account');
    }
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['error'] = 'Произошла ошибка при входе. Попробуйте позже.';
    header('Location: /login');
    exit;
}
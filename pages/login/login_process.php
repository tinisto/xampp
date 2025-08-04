<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../database/db_connections.php';

// Validate CSRF token
if (!Security::isValidCSRFToken()) {
    $_SESSION['error'] = 'Invalid security token. Please try again.';
    header('Location: /login');
    exit();
}

// Get and validate input
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    $_SESSION['error'] = 'Please provide valid email and password.';
    header('Location: /login');
    exit();
}

// Use secure database query
$db = Database::getInstance($connection);

try {
    // Get user by email
    $user = $db->queryOne(
        "SELECT id_users, username, password, email, status FROM users WHERE email = ?",
        [$email]
    );
    
    if (!$user) {
        ErrorHandler::log('Failed login attempt for email: ' . $email);
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: /login');
        exit();
    }
    
    // Check if account is suspended
    if ($user['status'] === 'suspended') {
        $_SESSION['error'] = 'Your account has been suspended.';
        header('Location: /login');
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        ErrorHandler::log('Failed login attempt for email: ' . $email);
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: /login');
        exit();
    }
    
    // Successful login
    $_SESSION['user_id'] = $user['id_users'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    
    // Update last login
    $db->update('users', [
        'last_login' => date('Y-m-d H:i:s')
    ], 'id_users = ?', [$user['id_users']]);
    
    // Log successful login
    ErrorHandler::log('Successful login', 'info', [
        'user_id' => $user['id_users'],
        'email' => $email
    ]);
    
    // Redirect to home page
    header('Location: /');
    exit();
    
} catch (Exception $e) {
    ErrorHandler::log('Login error: ' . $e->getMessage(), 'error');
    $_SESSION['error'] = 'An error occurred. Please try again.';
    header('Location: /login');
    exit();
}
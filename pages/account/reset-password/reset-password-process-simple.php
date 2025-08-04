<?php
session_start();

// Basic configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// CSRF validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['reset_error'] = 'Недействительный токен безопасности';
    header('Location: /forgot-password');
    exit;
}

// Validate email
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reset_error'] = 'Неверный формат email';
    header('Location: /forgot-password');
    exit;
}

try {
    // Check if user exists
    $stmt = $connection->prepare("SELECT id, first_name, email FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Database error");
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Don't reveal if email exists or not for security
        $_SESSION['reset_success'] = 'Если этот email зарегистрирован, вы получите инструкции по восстановлению пароля.';
        header('Location: /forgot-password');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Check if password_resets table exists
    $tableCheck = $connection->query("SHOW TABLES LIKE 'password_resets'");
    if ($tableCheck->num_rows == 0) {
        // Create the table if it doesn't exist
        $createTable = "CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(255) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            used BOOLEAN DEFAULT FALSE,
            INDEX idx_user_id (user_id),
            INDEX idx_token (token)
        )";
        $connection->query($createTable);
    }
    
    // Save token to database
    $stmt = $connection->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Database error");
    }
    
    $stmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
    $stmt->execute();
    
    // For now, since email might not be configured, show the reset link
    $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
    
    // Store reset info in session temporarily (for demo purposes)
    $_SESSION['reset_link'] = $resetLink;
    $_SESSION['reset_success'] = 'Ссылка для восстановления пароля создана. В реальной системе она будет отправлена на email.';
    
    // In production, this would send an email
    // For now, we'll display the link on the next page
    
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['reset_error'] = 'Произошла ошибка. Попробуйте позже.';
}

header('Location: /forgot-password');
exit;
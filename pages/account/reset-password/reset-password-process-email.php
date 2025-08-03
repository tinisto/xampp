<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/rate_limiting.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';

// Create rate limits table if needed
createRateLimitsTable();

// CSRF validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['reset_error'] = 'Недействительный токен безопасности';
    header('Location: /forgot-password');
    exit;
}

// Get user IP for rate limiting
$userIP = getUserIP();

// Check rate limit (3 attempts per 30 minutes)
$rateLimit = checkRateLimit($userIP, 'reset_password', 3, 1800);
if (!$rateLimit['allowed']) {
    $_SESSION['reset_error'] = $rateLimit['message'];
    header('Location: /forgot-password');
    exit;
}

// Validate email
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reset_error'] = 'Неверный формат email';
    recordAttempt($userIP, 'reset_password');
    header('Location: /forgot-password');
    exit;
}

try {
    // Check if user exists
    $stmt = $connection->prepare("SELECT id, firstname, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Don't reveal if email exists or not for security
        $_SESSION['reset_success'] = 'Если этот email зарегистрирован, вы получите инструкции по восстановлению пароля.';
        recordAttempt($userIP, 'reset_password');
        header('Location: /forgot-password');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Create password_resets table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(255) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        used BOOLEAN DEFAULT FALSE,
        INDEX idx_user_id (user_id),
        INDEX idx_token (token),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $connection->query($createTable);
    
    // Save token to database
    $stmt = $connection->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
    $stmt->execute();
    
    // Generate reset link
    $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
    
    // Get email template
    $emailBody = getPasswordResetEmailTemplate($user['firstname'], $resetLink);
    
    // Send email
    $subject = "Восстановление пароля - 11классники";
    
    // Check if email is configured
    $emailConfigured = !empty($_ENV['SMTP_HOST']) && 
                      !empty($_ENV['SMTP_USERNAME']) && 
                      !empty($_ENV['SMTP_PASSWORD']) &&
                      $_ENV['SMTP_PASSWORD'] !== 'your_app_password_here';
    
    if ($emailConfigured) {
        try {
            // Use the existing sendPasswordResetEmail function
            sendPasswordResetEmail($user['email'], $resetLink, $subject, $emailBody);
            
            // Reset rate limit on success
            resetRateLimit($userIP, 'reset_password');
            
            $_SESSION['reset_success'] = 'Инструкции по восстановлению пароля отправлены на ваш email. Проверьте почту.';
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            // Fallback to showing link if email fails
            $_SESSION['reset_link'] = $resetLink;
            $_SESSION['reset_success'] = 'Не удалось отправить email. Используйте эту ссылку для сброса пароля:';
            $_SESSION['reset_error_details'] = 'Ошибка: ' . $e->getMessage();
        }
    } else {
        // Email not configured - show link directly
        $_SESSION['reset_link'] = $resetLink;
        $_SESSION['reset_success'] = 'Email не настроен. Используйте эту ссылку для сброса пароля:';
        $_SESSION['reset_info'] = 'Для отправки email необходимо настроить SMTP. См. EMAIL_CONFIGURATION_GUIDE.md';
        
        // Log for admin
        error_log("Password reset requested but email not configured. User: " . $email);
    }
    
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['reset_error'] = 'Произошла ошибка. Попробуйте позже.';
}

header('Location: /forgot-password');
exit;
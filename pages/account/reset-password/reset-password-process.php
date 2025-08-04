<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/rate_limiting.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

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
    $stmt = $connection->prepare("SELECT id, first_name, email FROM users WHERE email = ?");
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
    
    // Save token to database
    $stmt = $connection->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
    $stmt->execute();
    
    // Send reset email
    $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
    
    $subject = "Восстановление пароля - 11классники";
    $message = "
        <h2>Восстановление пароля</h2>
        <p>Здравствуйте, {$user['first_name']}!</p>
        <p>Мы получили запрос на восстановление пароля для вашего аккаунта.</p>
        <p>Для сброса пароля перейдите по ссылке:</p>
        <p><a href='{$resetLink}' style='display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Сбросить пароль</a></p>
        <p>Или скопируйте эту ссылку в браузер:</p>
        <p>{$resetLink}</p>
        <p>Ссылка действительна в течение 1 часа.</p>
        <p>Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.</p>
    ";
    
    // Send email (using email_functions.php)
    if (sendPasswordResetEmail($user['email'], $user['first_name'], $resetLink)) {
        resetRateLimit($userIP, 'reset_password');
        $_SESSION['reset_success'] = 'Инструкции по восстановлению пароля отправлены на ваш email.';
    } else {
        $_SESSION['reset_error'] = 'Не удалось отправить email. Попробуйте позже.';
    }
    
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['reset_error'] = 'Произошла ошибка. Попробуйте позже.';
}

header('Location: /forgot-password');
exit;
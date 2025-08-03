<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        // Generate reset link
        $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
        
        $_SESSION['reset_link'] = $resetLink;
        $_SESSION['reset_success'] = 'Используйте эту ссылку для сброса пароля:';
    } else {
        $_SESSION['reset_error'] = 'Неверный формат email';
    }
    
    header('Location: /forgot-password');
    exit;
}

// If not POST, redirect to forgot password page
header('Location: /forgot-password');
exit;
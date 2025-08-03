<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';

// Initialize CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Always show the same message for security
        $_SESSION['reset_message'] = 'Если этот email зарегистрирован в системе, вы получите инструкции для восстановления пароля на указанный адрес.';
        
        // Check if email exists and send reset email
        try {
            // First, create the password_resets table if it doesn't exist
            $createTableQuery = "CREATE TABLE IF NOT EXISTS password_resets (
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
            $connection->query($createTableQuery);
            
            $stmt = $connection->prepare("SELECT id, first_name FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    // Generate secure token
                    $token = bin2hex(random_bytes(32));
                    $hashedToken = hash('sha256', $token);
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Delete old tokens for this user
                    $deleteStmt = $connection->prepare("DELETE FROM password_resets WHERE user_id = ?");
                    if ($deleteStmt) {
                        $deleteStmt->bind_param("i", $user['id']);
                        $deleteStmt->execute();
                        $deleteStmt->close();
                    }
                    
                    // Store token in database
                    $insertStmt = $connection->prepare("
                        INSERT INTO password_resets (user_id, token, expires_at) 
                        VALUES (?, ?, ?)
                    ");
                    
                    if ($insertStmt) {
                        $insertStmt->bind_param("iss", $user['id'], $hashedToken, $expiresAt);
                        if ($insertStmt->execute()) {
                            // Send email with reset link
                            $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
                            $emailBody = getPasswordResetEmailTemplate($user['first_name'], $resetLink);
                            
                            // Send email using PHPMailer - log result
                            try {
                                sendPasswordResetEmail($email, $resetLink, 'Восстановление пароля - 11классники', $emailBody);
                                error_log("Password reset email sent successfully to: {$email}");
                            } catch (Exception $mailException) {
                                error_log("Failed to send password reset email to {$email}: " . $mailException->getMessage());
                            }
                        }
                        $insertStmt->close();
                    }
                }
                
                $stmt->close();
            }
        } catch (Exception $e) {
            // Log error but don't expose it to user
            error_log("Password reset error: " . $e->getMessage());
        }
        
        // Redirect to prevent form resubmission
        header('Location: /forgot-password-standalone.php');
        exit;
    } else {
        $_SESSION['reset_error'] = 'Пожалуйста, введите корректный email адрес.';
        header('Location: /forgot-password-standalone.php');
        exit;
    }
}

// Get messages from session
$message = $_SESSION['reset_message'] ?? '';
$error = $_SESSION['reset_error'] ?? '';

// Clear session messages
unset($_SESSION['reset_message']);
unset($_SESSION['reset_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля - 11классники</title>
    <link rel="stylesheet" href="/css/site-logo.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="email"]:focus {
            outline: none;
            border-color: #28a745;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #218838;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #28a745;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .info-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        
        .info-box li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <?php 
            require_once __DIR__ . '/includes/components/site-logo.php';
            echo renderSiteLogo(['showText' => true]);
            ?>
        </div>
        
        <h1>Восстановление пароля</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
            <div class="info-box">
                <strong>Что делать дальше:</strong>
                <ul>
                    <li>Проверьте вашу электронную почту</li>
                    <li>Найдите письмо от 11классники</li>
                    <li>Нажмите на ссылку в письме</li>
                    <li>Создайте новый пароль</li>
                </ul>
                <p style="margin-top: 10px;">
                    <small>Если письмо не пришло в течение 5 минут, проверьте папку "Спам".</small>
                </p>
            </div>
            <div class="back-link">
                <a href="/login">Вернуться к входу</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                Введите email адрес, который вы использовали при регистрации. Мы отправим вам инструкции для восстановления пароля.
            </p>
            
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="form-group">
                    <label for="email">Email адрес</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           placeholder="your@email.com"
                           autofocus>
                </div>
                
                <button type="submit">Отправить инструкции</button>
            </form>
            
            <div class="back-link">
                <a href="/login">Вернуться к входу</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
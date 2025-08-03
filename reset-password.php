<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Get token and email from URL
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

$error = '';
$success = false;

// Validate token
if (empty($token) || empty($email)) {
    $error = 'Недействительная ссылка для сброса пароля.';
} else {
    // Check if token is valid
    $hashedToken = hash('sha256', $token);
    
    $stmt = $connection->prepare("
        SELECT pr.*, u.email 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND u.email = ? AND pr.expires_at > NOW() AND pr.used = FALSE
    ");
    $stmt->bind_param("ss", $hashedToken, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = 'Ссылка для сброса пароля недействительна или устарела.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword)) {
        $error = 'Введите новый пароль.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Пароли не совпадают.';
    } else {
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Get user ID
        $stmt = $connection->prepare("
            SELECT u.id 
            FROM users u 
            JOIN password_resets pr ON u.id = pr.user_id 
            WHERE pr.token = ? AND u.email = ?
        ");
        $stmt->bind_param("ss", $hashedToken, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            // Update password
            $stmt = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $user['id']);
            $stmt->execute();
            
            // Mark token as used
            $stmt = $connection->prepare("UPDATE password_resets SET used = TRUE WHERE token = ?");
            $stmt->bind_param("s", $hashedToken);
            $stmt->execute();
            
            $success = true;
            $_SESSION['password_reset_success'] = 'Пароль успешно изменен! Теперь вы можете войти с новым паролем.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - 11-классники</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --danger-color: #dc3545;
            --text-primary: #333;
            --text-secondary: #666;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
            --input-focus: #80bdff;
            --shadow: 0 0 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .reset-container {
            width: 100%;
            max-width: 400px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideIn 0.4s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-section {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .logo-link {
            display: inline-block;
            text-decoration: none;
            color: var(--primary-color);
            transition: var(--transition);
        }
        
        .logo-link:hover {
            transform: scale(1.05);
            color: var(--primary-hover);
        }
        
        .reset-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        
        .reset-header h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .reset-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 13px;
        }
        
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            transition: var(--transition);
            background-color: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .success-message {
            text-align: center;
            padding: 40px 20px;
        }
        
        .success-icon {
            font-size: 60px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .success-message h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .success-message p {
            color: var(--text-secondary);
            margin-bottom: 25px;
        }
        
        .btn-login {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .password-requirements {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-section">
            <a href="/" class="logo-link">
                <div class="logo-placeholder">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2"/>
                        <text x="20" y="26" text-anchor="middle" fill="currentColor" font-size="18" font-weight="bold">11</text>
                    </svg>
                </div>
            </a>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✓</div>
                <h2>Пароль успешно изменен!</h2>
                <p>Теперь вы можете войти с новым паролем.</p>
                <a href="/login" class="btn-login">Перейти к входу</a>
            </div>
        <?php else: ?>
            <div class="reset-header">
                <h1>Создайте новый пароль</h1>
            </div>
            
            <div class="reset-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($error) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <form method="post">
                        <div class="form-group">
                            <label for="password" class="form-label">Новый пароль</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-input" 
                                   required
                                   minlength="6">
                            <p class="password-requirements">Минимум 6 символов</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Подтвердите пароль</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="form-input" 
                                   required
                                   minlength="6">
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            Изменить пароль
                        </button>
                    </form>
                <?php else: ?>
                    <p style="text-align: center; margin: 20px 0;">
                        <a href="/forgot-password" style="color: var(--primary-color); text-decoration: none;">
                            Запросить новую ссылку для сброса пароля
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
// Forgot password page - standalone without header/footer
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/logo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email.php';

$success = false;
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Введите email адрес';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Неверный формат email адреса';
    } else {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
        
        if ($user) {
            // Generate password reset token
            $resetToken = bin2hex(random_bytes(32));
            $resetExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Save reset token to database
            try {
                // First check if reset_token column exists
                $columnExists = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'reset_token'");
                if (!$columnExists) {
                    // Add columns if they don't exist
                    $pdo = $db->getConnection();
                    $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL");
                    $pdo->exec("ALTER TABLE users ADD COLUMN reset_expires DATETIME NULL");
                }
                
                // Update user with reset token
                $db->update('users', 
                    ['reset_token' => $resetToken, 'reset_expires' => $resetExpiry],
                    'id = ?',
                    [$user['id']]
                );
                
                // Generate reset link
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $resetLink = "{$protocol}://{$host}/reset-password.php?token={$resetToken}";
                
                // Send password reset email
                $userName = trim($user['first_name'] . ' ' . $user['last_name']);
                if (empty($userName)) {
                    $userName = explode('@', $user['email'])[0]; // Use email username as fallback
                }
                EmailNotification::sendPasswordResetEmail(
                    $user['email'],
                    $userName,
                    $resetLink
                );
                
                $success = true;
                $message = "Если аккаунт с email {$email} существует, на него будет отправлена ссылка для сброса пароля.";
                
                // For development - show the reset link directly
                if ($_SERVER['SERVER_NAME'] === 'localhost') {
                    $message .= "<br><br><div style='background: #f0f0f0; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
                    $message .= "<h3 style='margin: 0 0 10px 0;'>🔗 Reset Password Link:</h3>";
                    $message .= "<a href='{$resetLink}' style='color: #007bff; word-break: break-all;'>{$resetLink}</a>";
                    $message .= "</div>";
                }
            } catch (Exception $e) {
                $error = 'Произошла ошибка. Попробуйте позже.';
            }
        } else {
            // Don't reveal if email exists or not for security
            $success = true;
            $message = "Если аккаунт с email {$email} существует, на него будет отправлена ссылка для сброса пароля.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля - 11klassniki.ru</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
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
        
        .info-text {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
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
        
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .form-footer p {
            color: var(--text-secondary);
            font-size: 13px;
            margin: 0;
        }
        
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        }
        
        body.dark-mode .reset-container {
            background: #2d2d2d;
        }
        
        body.dark-mode .logo-section {
            border-color: #555;
        }
        
        body.dark-mode .logo-section h1,
        body.dark-mode .logo-section p {
            color: #e0e0e0;
        }
        
        body.dark-mode .reset-header {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
        
        body.dark-mode .info-text {
            color: #b0b0b0;
        }
        
        body.dark-mode .form-label {
            color: #e0e0e0;
        }
        
        body.dark-mode .form-input {
            background: #3a3a3a;
            border-color: #555;
            color: #e0e0e0;
        }
        
        body.dark-mode .form-input:focus {
            border-color: #4299e1;
        }
        
        body.dark-mode .submit-btn {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
        
        body.dark-mode .form-footer {
            border-color: #555;
        }
        
        body.dark-mode .form-footer p {
            color: #b0b0b0;
        }
        
        body.dark-mode .form-footer a {
            color: #4299e1;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-section" style="text-align: center; padding: 20px; border-bottom: 1px solid #e1e5e9;">
            <?php logo('small'); ?>
        </div>
        <div class="reset-header">
            <h1>Восстановление пароля</h1>
        </div>
        
        <div class="reset-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Готово!</strong> <?= $message ?>
                </div>
                
                <div class="form-footer">
                    <p><a href="/login_modern.php"><i class="fas fa-sign-in-alt"></i> Вернуться к входу</a></p>
                </div>
                
            <?php else: ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Ошибка:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <p class="info-text">
                    Введите ваш email адрес и мы отправим ссылку для восстановления пароля
                </p>
                
                <form method="post">
                    <div class="form-group">
                        <label for="email" class="form-label">Email адрес</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input" 
                               required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="your@email.com"
                               autocomplete="email">
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Отправить ссылку
                    </button>
                </form>
                
                <div class="form-footer">
                    <p><a href="/login_modern.php"><i class="fas fa-arrow-left"></i> Вернуться к входу</a>
                    <span style="color: #ddd;">•</span>
                    <a href="/register_modern.php"><i class="fas fa-user-plus"></i> Создать аккаунт</a></p>
                </div>
                
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>
</body>
</html>
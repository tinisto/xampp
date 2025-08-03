<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config/loadEnv.php';
require_once 'includes/functions/email_functions.php';

// Check if user is admin (basic check)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $testEmail = filter_var($_POST['test_email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        // Check configuration
        $emailConfigured = !empty($_ENV['SMTP_HOST']) && 
                          !empty($_ENV['SMTP_USERNAME']) && 
                          !empty($_ENV['SMTP_PASSWORD']) &&
                          $_ENV['SMTP_PASSWORD'] !== 'your_app_password_here';
        
        if (!$emailConfigured) {
            $message = "Email не настроен. Пожалуйста, обновите .env файл с правильными SMTP настройками.";
        } else {
            try {
                $subject = 'Тест Email - 11классники';
                $body = '<h2>Тестовое сообщение</h2>
                        <p>Если вы видите это сообщение, значит email настроен правильно!</p>
                        <p>Отправлено с сайта 11klassniki.ru</p>
                        <hr>
                        <p style="color: #666; font-size: 12px;">
                        SMTP Host: ' . $_ENV['SMTP_HOST'] . '<br>
                        SMTP User: ' . $_ENV['SMTP_USERNAME'] . '<br>
                        Время отправки: ' . date('Y-m-d H:i:s') . '
                        </p>';
                
                sendToUser($testEmail, $subject, $body);
                $message = "Email успешно отправлен на $testEmail! Проверьте почту.";
                $success = true;
            } catch (Exception $e) {
                $message = "Ошибка отправки: " . $e->getMessage();
            }
        }
    } else {
        $message = "Неверный формат email адреса.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест Email Конфигурации</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .status.configured {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.not-configured {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .config-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-family: monospace;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #28a745;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover {
            background: #218838;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #28a745;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Тест Email Конфигурации</h1>
        
        <?php
        // Check configuration
        $emailConfigured = !empty($_ENV['SMTP_HOST']) && 
                          !empty($_ENV['SMTP_USERNAME']) && 
                          !empty($_ENV['SMTP_PASSWORD']) &&
                          $_ENV['SMTP_PASSWORD'] !== 'your_app_password_here';
        ?>
        
        <div class="status <?= $emailConfigured ? 'configured' : 'not-configured' ?>">
            <?php if ($emailConfigured): ?>
                ✓ Email настроен
            <?php else: ?>
                ✗ Email не настроен - обновите .env файл
            <?php endif; ?>
        </div>
        
        <div class="config-info">
            <strong>Текущая конфигурация:</strong><br><br>
            SMTP_HOST: <?= !empty($_ENV['SMTP_HOST']) ? $_ENV['SMTP_HOST'] : 'Не установлен' ?><br>
            SMTP_USERNAME: <?= !empty($_ENV['SMTP_USERNAME']) ? $_ENV['SMTP_USERNAME'] : 'Не установлен' ?><br>
            SMTP_PASSWORD: <?= !empty($_ENV['SMTP_PASSWORD']) ? (($_ENV['SMTP_PASSWORD'] === 'your_app_password_here') ? 'Используется пример пароля!' : '***настроен***') : 'Не установлен' ?><br>
            SMTP_SECURITY: <?= !empty($_ENV['SMTP_SECURITY']) ? $_ENV['SMTP_SECURITY'] : 'Не установлен' ?><br>
            SMTP_PORT: <?= !empty($_ENV['SMTP_PORT']) ? $_ENV['SMTP_PORT'] : 'Не установлен' ?><br>
            ADMIN_EMAIL: <?= !empty($_ENV['ADMIN_EMAIL']) ? $_ENV['ADMIN_EMAIL'] : 'Не установлен' ?>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($emailConfigured): ?>
            <form method="post">
                <div class="form-group">
                    <label for="test_email">Отправить тестовое письмо на:</label>
                    <input type="email" 
                           id="test_email" 
                           name="test_email" 
                           placeholder="test@example.com" 
                           required
                           value="<?= isset($_POST['test_email']) ? htmlspecialchars($_POST['test_email']) : '' ?>">
                </div>
                <button type="submit">Отправить тест</button>
            </form>
        <?php else: ?>
            <p>Пожалуйста, настройте email в файле .env перед тестированием. См. EMAIL_CONFIGURATION_GUIDE.md для инструкций.</p>
        <?php endif; ?>
        
        <a href="/dashboard" class="back-link">← Вернуться в панель управления</a>
    </div>
</body>
</html>
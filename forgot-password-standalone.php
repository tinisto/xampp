<?php
session_start();

// Initialize CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email exists in database
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
        
        if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if (!$connection->connect_error) {
                $connection->set_charset("utf8mb4");
                
                // Check if email exists
                $stmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
                if ($stmt) {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Email exists - generate reset link
                        $token = substr(md5(time() . $email . rand()), 0, 32);
                        $resetLink = "https://11klassniki.ru/reset-password?token=" . $token . "&email=" . urlencode($email);
                        
                        $_SESSION['reset_link'] = $resetLink;
                        $_SESSION['reset_success'] = true;
                    } else {
                        // Email doesn't exist - show generic message for security
                        $_SESSION['reset_error'] = 'Если этот email зарегистрирован в системе, вы получите инструкции для восстановления пароля.';
                    }
                    
                    $stmt->close();
                } else {
                    $_SESSION['reset_error'] = 'Произошла ошибка. Попробуйте позже.';
                }
                
                $connection->close();
            } else {
                $_SESSION['reset_error'] = 'Произошла ошибка подключения. Попробуйте позже.';
            }
        } else {
            $_SESSION['reset_error'] = 'Сервис временно недоступен. Попробуйте позже.';
        }
    } else {
        $_SESSION['reset_error'] = 'Пожалуйста, введите корректный email адрес.';
    }
    
    // Redirect to self to prevent form resubmission
    header('Location: /forgot-password-standalone.php');
    exit;
}

// Get messages from session
$showSuccess = isset($_SESSION['reset_success']);
$resetLink = $_SESSION['reset_link'] ?? '';
$error = $_SESSION['reset_error'] ?? '';

// Clear session messages
unset($_SESSION['reset_success']);
unset($_SESSION['reset_link']);
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
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .reset-link {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
            word-break: break-all;
            font-family: monospace;
            font-size: 14px;
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
        
        <?php if ($showSuccess && $resetLink): ?>
            <div class="alert alert-success">
                <strong>Ссылка для сброса пароля создана!</strong>
                <div class="reset-link">
                    <a href="<?= htmlspecialchars($resetLink) ?>" target="_blank">
                        <?= htmlspecialchars($resetLink) ?>
                    </a>
                </div>
                <p style="margin-top: 10px; font-size: 14px;">
                    Скопируйте эту ссылку или нажмите на неё, чтобы сбросить пароль.
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
                Введите email адрес, который вы использовали при регистрации.
            </p>
            
            <form method="post">
                <div class="form-group">
                    <label for="email">Email адрес</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           placeholder="your@email.com"
                           autofocus>
                </div>
                
                <button type="submit">Получить ссылку для сброса</button>
            </form>
            
            <div class="back-link">
                <a href="/login">Вернуться к входу</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
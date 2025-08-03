<?php
// Simple login page without complex security checks
session_start();

// Generate CSRF token if needed
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - 11-классники</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #28a745;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #28a745;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            color: #28a745;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Вход</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="success">Регистрация успешна! Теперь вы можете войти.</div>
        <?php endif; ?>
        
        <div class="info">
            Тестовые данные:<br>
            Email: test@example.com<br>
            Пароль: password123
        </div>
        
        <form method="post" action="/login-process-debug.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email адрес" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            
            <button type="submit">Войти</button>
        </form>
        
        <div class="link">
            <p>Нет аккаунта? <a href="/registration-test.php">Зарегистрируйтесь</a></p>
        </div>
        
        <div class="link">
            <p><a href="/">← На главную</a> | <a href="/login">Обычная страница входа</a></p>
        </div>
    </div>
</body>
</html>
<?php
// Admin login
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// If already logged in as admin, redirect to admin panel
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: /admin/contact-messages.php');
    exit;
}

$error = '';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        // Check admin user
        $admin = $db->fetchOne("SELECT * FROM users WHERE email = ? AND role = 'admin' AND is_active = 1", [$email]);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['user_role'] = $admin['role'];
            
            // Update last login
            $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$admin['id']]);
            
            // Redirect to admin panel
            header('Location: /admin/contact-messages.php');
            exit;
        } else {
            $error = 'Неверный email или пароль';
        }
    }
}

$page_title = 'Вход в админ панель - 11klassniki.ru';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .logo .eleven {
            color: #667eea;
            font-weight: 700;
        }
        
        .logo .ru {
            color: #764ba2;
            font-weight: 500;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .demo-info {
            margin-top: 30px;
            padding: 15px;
            background: #f0f4ff;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        
        .demo-info h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .demo-info p {
            color: #666;
            font-size: 13px;
            margin: 5px 0;
        }
        
        .demo-info strong {
            color: #333;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1><span class="eleven">11</span>klassniki<span class="ru">.ru</span></h1>
            <p>Админ панель</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="email">Email администратора</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="admin@11klassniki.ru">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required
                       placeholder="Введите пароль">
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Войти в админ панель
            </button>
        </form>
        
        <div class="demo-info">
            <h4><i class="fas fa-info-circle"></i> Демо доступ:</h4>
            <p><strong>Email:</strong> admin@11klassniki.ru</p>
            <p><strong>Пароль:</strong> admin123</p>
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.8;">
                <i class="fas fa-shield-alt"></i> В продакшене измените пароль администратора
            </p>
        </div>
        
        <div class="back-link">
            <a href="/"><i class="fas fa-arrow-left"></i> Вернуться на сайт</a>
        </div>
    </div>
</body>
</html>
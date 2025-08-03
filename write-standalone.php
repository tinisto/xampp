<?php
// Standalone write page
session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Ошибка безопасности. Попробуйте снова.';
    } else {
        // Get form data
        $name = htmlspecialchars($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $subject = htmlspecialchars($_POST['subject'] ?? '');
        $message_text = htmlspecialchars($_POST['message'] ?? '');
        
        if ($name && $email && $subject && $message_text) {
            // In production, you would send email here
            $message = 'Спасибо за ваше сообщение! Мы свяжемся с вами в ближайшее время.';
            
            // Clear form data
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $error = 'Пожалуйста, заполните все поля.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Напишите нам - 11-классники</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        header {
            background: #28a745;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .nav-links a:hover {
            opacity: 0.8;
        }
        
        main {
            flex: 1;
            padding: 40px 0;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .write-form {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #28a745;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            outline: none;
            border-color: #28a745;
        }
        
        textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        button {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #218838;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
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
        
        footer {
            background: #333;
            color: #ccc;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        
        .info-text {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="/" class="logo">11-классники</a>
            <nav class="nav-links">
                <a href="/">Главная</a>
                <a href="/vpo-all-regions">ВПО</a>
                <a href="/spo-all-regions">СПО</a>
                <a href="/schools-all-regions">Школы</a>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <div class="write-form">
                <h1>Напишите нам</h1>
                
                <p class="info-text">
                    Есть вопросы или предложения? Мы будем рады услышать от вас!
                </p>
                
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-group">
                        <label for="name">Ваше имя</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Тема</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Сообщение</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    
                    <button type="submit">Отправить сообщение</button>
                </form>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> 11-классники. Все права защищены.</p>
    </footer>
</body>
</html>
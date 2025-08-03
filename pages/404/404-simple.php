<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Страница не найдена</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        
        .logo-link {
            margin-bottom: 2rem;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: 300;
            color: #333;
            margin: 0;
            line-height: 1;
        }
        
        .error-title {
            font-size: 2rem;
            color: #666;
            margin: 1rem 0;
            font-weight: normal;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #888;
            margin: 2rem 0;
            line-height: 1.6;
        }
        
        .back-link {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .back-link:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="error-container">
<?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/logo.php'; renderLogo('large'); ?>
        
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Ой-ой!</h2>
        <p class="error-message">
            Возможно, это ваша ошибка, а может быть, это наша,<br>
            но здесь нет нужной вам страницы.
        </p>
        
        <a href="/" class="back-link">Вернуться на главную</a>
    </div>
</body>
</html>

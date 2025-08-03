<?php
// Simple error page without header/footer
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибка - 11 Классники</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        
        .logo-link {
            display: inline-block;
            margin-bottom: 40px;
            transition: transform 0.3s ease;
        }
        
        .logo-link:hover {
            transform: scale(1.05);
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 40px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 700;
            color: #28a745;
            line-height: 1;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .error-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        .back-button {
            display: inline-block;
            padding: 12px 32px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
        }
        
        .back-button:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }
        
        @media (max-width: 480px) {
            .error-container {
                padding: 40px 20px;
            }
            
            .error-code {
                font-size: 80px;
            }
            
            .error-title {
                font-size: 24px;
            }
            
            .logo-icon {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <a href="/" class="logo-link">
            <div class="logo-icon">11</div>
        </a>
        
        <div class="error-code">500</div>
        <h1 class="error-title">Что-то пошло не так</h1>
        <p class="error-message">
            Произошла непредвиденная ошибка. Мы уже работаем над её устранением. 
            Пожалуйста, попробуйте обновить страницу или вернитесь позже.
        </p>
        
        <a href="/" class="back-button">Вернуться на главную</a>
    </div>
</body>
</html>
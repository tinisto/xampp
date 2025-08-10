<?php
// Standalone privacy page without header/footer
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Политика конфиденциальности - 11klassniki.ru</title>
    
    <!-- New Favicon -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3VnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3VnPgo=" type="image/svg+xml">
    
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
            padding: 20px;
        }
        
        .privacy-container {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .site-icon {
            width: 60px;
            height: 60px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .last-updated {
            color: #666;
            font-size: 14px;
        }
        
        h2 {
            color: #333;
            margin-bottom: 15px;
            margin-top: 30px;
            font-size: 24px;
            font-weight: 600;
        }
        
        h2:first-of-type {
            margin-top: 20px;
        }
        
        p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
            text-align: justify;
        }
        
        ul {
            color: #666;
            line-height: 1.8;
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        li {
            margin-bottom: 8px;
        }
        
        a {
            color: #007bff;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .contact-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            text-align: center;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .back-link:hover {
            background: #0056b3;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .privacy-container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            h2 {
                font-size: 20px;
            }
            
            ul {
                margin-left: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="privacy-container">
        <div class="header">
            <a href="/" class="site-icon">11</a>
            <h1>Политика конфиденциальности</h1>
            <div class="last-updated">Последнее обновление: <?= date('d.m.Y') ?></div>
        </div>
        
        <h2>1. Общие положения</h2>
        <p>
            Настоящая политика конфиденциальности (далее – Политика) действует в отношении всей информации, 
            которую сайт 11klassniki.ru может получить о пользователе во время использования сайта.
        </p>
        
        <h2>2. Сбор информации</h2>
        <p>Мы собираем следующую информацию:</p>
        <ul>
            <li>Имя и фамилия</li>
            <li>Адрес электронной почты</li>
            <li>Информация о посещаемых страницах</li>
            <li>IP-адрес</li>
            <li>Информация о браузере и устройстве</li>
        </ul>
        
        <h2>3. Использование информации</h2>
        <p>Собранная информация используется для:</p>
        <ul>
            <li>Предоставления доступа к функциям сайта</li>
            <li>Улучшения качества обслуживания</li>
            <li>Связи с пользователями</li>
            <li>Проведения статистических исследований</li>
            <li>Обеспечения безопасности</li>
        </ul>
        
        <h2>4. Защита данных</h2>
        <p>
            Мы принимаем необходимые организационные и технические меры для защиты персональной информации 
            пользователей от неправомерного или случайного доступа, уничтожения, изменения, блокирования, 
            копирования, распространения.
        </p>
        
        <h2>5. Использование cookies</h2>
        <p>
            Сайт использует файлы cookies для улучшения работы сайта. Cookies представляют собой небольшие 
            файлы данных, которые сохраняются на вашем устройстве. Вы можете отключить использование cookies 
            в настройках браузера.
        </p>
        
        <h2>6. Передача данных третьим лицам</h2>
        <p>
            Мы не передаем персональные данные пользователей третьим лицам без согласия пользователей, 
            за исключением случаев, предусмотренных законодательством РФ.
        </p>
        
        <h2>7. Права пользователей</h2>
        <p>Пользователи имеют право:</p>
        <ul>
            <li>Получать информацию о своих персональных данных</li>
            <li>Требовать уточнения или удаления своих данных</li>
            <li>Отозвать согласие на обработку данных</li>
            <li>Обратиться с жалобой в уполномоченный орган</li>
        </ul>
        
        <h2>8. Изменения в политике</h2>
        <p>
            Мы оставляем за собой право вносить изменения в настоящую Политику. При внесении изменений 
            в актуальной редакции указывается дата последнего обновления.
        </p>
        
        <h2>9. Контакты</h2>
        <p>
            По всем вопросам, связанным с настоящей Политикой, вы можете связаться с нами через 
            <a href="/write">форму обратной связи</a>.
        </p>
        
        <div class="contact-section">
            <a href="/" class="back-link">Вернуться на главную</a>
        </div>
    </div>
</body>
</html>
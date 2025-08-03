<?php
// Simple test page to check cookie banner
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/components/cookie-consent.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cookie Banner - 11классники</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .test-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        h1 { color: #28a745; }
        .status {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="test-content">
        <h1>🍪 Тест Cookie Banner</h1>
        
        <div class="status">
            <h3>Статус cookies:</h3>
            <p><strong>cookie_consent:</strong> <?= isset($_COOKIE['cookie_consent']) ? $_COOKIE['cookie_consent'] : 'НЕ УСТАНОВЛЕН' ?></p>
            <p><strong>preferred-theme:</strong> <?= isset($_COOKIE['preferred-theme']) ? $_COOKIE['preferred-theme'] : 'НЕ УСТАНОВЛЕН' ?></p>
            <p><strong>analytics_consent:</strong> <?= isset($_COOKIE['analytics_consent']) ? $_COOKIE['analytics_consent'] : 'НЕ УСТАНОВЛЕН' ?></p>
        </div>
        
        <h3>Тестовая страница</h3>
        <p>Эта страница предназначена для тестирования системы согласия на использование cookies.</p>
        <p>Если вы видите баннер внизу страницы - система работает корректно.</p>
        <p>Если баннера нет - проверьте консоль браузера на наличие ошибок.</p>
        
        <button onclick="window.location.reload()">Обновить страницу</button>
        <button onclick="document.cookie='cookie_consent=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'; location.reload()">Сбросить cookies</button>
    </div>

    <!-- Cookie Consent Banner -->
    <?= renderCookieConsent() ?>
    
    <script>
        console.log('🍪 Test page loaded');
        console.log('Cookies:', document.cookie);
        
        // Check if banner is present
        setTimeout(() => {
            const banner = document.getElementById('cookie-consent-banner');
            if (banner) {
                console.log('✅ Cookie banner found:', banner);
            } else {
                console.log('❌ Cookie banner not found');
            }
        }, 100);
    </script>
</body>
</html>
<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/comments/timezone-handler.php';

// Test data
$test_times = [
    'now' => date('Y-m-d H:i:s'),
    '30_seconds_ago' => date('Y-m-d H:i:s', strtotime('-30 seconds')),
    '5_minutes_ago' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
    '2_hours_ago' => date('Y-m-d H:i:s', strtotime('-2 hours')),
    '1_day_ago' => date('Y-m-d H:i:s', strtotime('-1 day')),
    '5_days_ago' => date('Y-m-d H:i:s', strtotime('-5 days')),
    '2_months_ago' => date('Y-m-d H:i:s', strtotime('-2 months')),
];

$userTimezone = getUserTimezone();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timezone Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .test-item {
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-left: 4px solid #3b82f6;
            border-radius: 5px;
        }
        .timestamp {
            color: #666;
            font-size: 0.9em;
        }
        .formatted {
            color: #333;
            font-weight: bold;
        }
        button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Тест определения временной зоны</h1>
        
        <div class="info">
            <p><strong>Текущая временная зона сервера:</strong> <?= date_default_timezone_get() ?></p>
            <p><strong>Ваша временная зона:</strong> <span id="userTimezone"><?= htmlspecialchars($userTimezone) ?></span></p>
            <p><strong>Время сервера:</strong> <?= date('Y-m-d H:i:s') ?></p>
            <p><strong>Ваше местное время:</strong> <span id="localTime"></span></p>
        </div>
        
        <h2>Тестовые комментарии:</h2>
        
        <?php foreach ($test_times as $label => $timestamp): ?>
            <div class="test-item">
                <div class="timestamp">
                    <?= str_replace('_', ' ', $label) ?>: <?= $timestamp ?>
                </div>
                <div class="formatted">
                    Отображается как: <?= formatTimeAgoUserTZ($timestamp) ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <button onclick="clearTimezoneAndReload()">Сбросить временную зону и перезагрузить</button>
    </div>

    <script>
    // Display local time
    function updateLocalTime() {
        const now = new Date();
        document.getElementById('localTime').textContent = now.toLocaleString('ru-RU');
    }
    updateLocalTime();
    setInterval(updateLocalTime, 1000);
    
    // Clear timezone cookie and reload
    function clearTimezoneAndReload() {
        sessionStorage.removeItem('timezone_detected');
        document.cookie = 'user_timezone_set=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC';
        
        // Clear session on server
        fetch('/comments/timezone-handler.php?clear=1')
            .then(() => location.reload());
    }
    
    // Detect timezone on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (!sessionStorage.getItem('timezone_detected')) {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            
            fetch('/comments/timezone-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'timezone=' + encodeURIComponent(timezone)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    sessionStorage.setItem('timezone_detected', 'true');
                    location.reload(); // Reload to show correct timezone
                }
            })
            .catch(error => console.error('Error setting timezone:', error));
        }
    });
    </script>
    
    <?php echo getTimezoneDetectionScript(); ?>
</body>
</html>
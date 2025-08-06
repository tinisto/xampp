<?php
$pageTitle = 'Выберите тест - Режим обучения';
$metaD = 'Выберите тест для изучения в режиме персонального преподавателя - получайте мгновенные объяснения и изучайте материал пошагово';
$metaK = 'онлайн тесты, обучение, персональный преподаватель, объяснения, пошаговое обучение';

// Available tests
$tests = [
    'math-test' => [
        'title' => 'Математика',
        'description' => 'Алгебра, геометрия, тригонометрия',
        'icon' => 'fas fa-calculator',
        'color' => '#667eea'
    ],
    'russian-test' => [
        'title' => 'Русский язык',
        'description' => 'Орфография, пунктуация, грамматика',
        'icon' => 'fas fa-book',
        'color' => '#764ba2'
    ],
    'physics-test' => [
        'title' => 'Физика',
        'description' => 'Механика, электричество, оптика',
        'icon' => 'fas fa-atom',
        'color' => '#4facfe'
    ],
    'chemistry-test' => [
        'title' => 'Химия',
        'description' => 'Органическая и неорганическая химия',
        'icon' => 'fas fa-flask',
        'color' => '#00f2fe'
    ],
    'biology-test' => [
        'title' => 'Биология',
        'description' => 'Ботаника, зоология, анатомия',
        'icon' => 'fas fa-leaf',
        'color' => '#43e97b'
    ],
    'geography-test' => [
        'title' => 'География',
        'description' => 'Физическая и экономическая география',
        'icon' => 'fas fa-globe-europe',
        'color' => '#38f9d7'
    ],
    'iq-test' => [
        'title' => 'IQ Тест',
        'description' => 'Логическое мышление и интеллект',
        'icon' => 'fas fa-brain',
        'color' => '#667eea'
    ],
    'career-test' => [
        'title' => 'Профориентация',
        'description' => 'Определение склонностей и способностей',
        'icon' => 'fas fa-compass',
        'color' => '#764ba2'
    ]
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <meta name="description" content="<?= htmlspecialchars($metaD) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaK) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitle {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.4;
        }
        
        .mode-badge {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 12px 24px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .test-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .test-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .test-card:hover::before {
            opacity: 1;
        }
        
        .test-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--card-color);
        }
        
        .test-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .test-description {
            font-size: 16px;
            opacity: 0.8;
            line-height: 1.4;
        }
        
        .footer-actions {
            text-align: center;
            margin-top: 50px;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }
        
        .features {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .features-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }
        
        .feature-icon {
            color: #4facfe;
            font-size: 18px;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }
            
            .title {
                font-size: 36px;
            }
            
            .subtitle {
                font-size: 18px;
            }
            
            .tests-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .test-card {
                padding: 25px;
            }
            
            .features-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Режим обучения</h1>
            <p class="subtitle">
                Изучайте материал с персональным преподавателем.<br>
                Получайте мгновенные объяснения и учитесь на своих ошибках.
            </p>
            <div class="mode-badge">
                <i class="fas fa-graduation-cap"></i>
                Персональный преподаватель
            </div>
        </div>
        
        <div class="features">
            <h2 class="features-title">
                <i class="fas fa-star"></i>
                Особенности режима обучения
            </h2>
            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-check-circle feature-icon"></i>
                    Мгновенная проверка ответов
                </div>
                <div class="feature-item">
                    <i class="fas fa-lightbulb feature-icon"></i>
                    Подробные объяснения ошибок
                </div>
                <div class="feature-item">
                    <i class="fas fa-forward feature-icon"></i>
                    Автоматический переход при правильном ответе
                </div>
                <div class="feature-item">
                    <i class="fas fa-book-open feature-icon"></i>
                    Возможность изучить материал
                </div>
            </div>
        </div>
        
        <div class="tests-grid">
            <?php foreach ($tests as $testKey => $test): ?>
                <a href="/pages/tests/test-simple.php?test=<?= $testKey ?>&q=0" class="test-card" style="--card-color: <?= $test['color'] ?>">
                    <div class="test-icon">
                        <i class="<?= $test['icon'] ?>"></i>
                    </div>
                    <h3 class="test-title"><?= htmlspecialchars($test['title']) ?></h3>
                    <p class="test-description"><?= htmlspecialchars($test['description']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="footer-actions">
            <a href="/tests" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Вернуться к обычным тестам
            </a>
        </div>
    </div>
</body>
</html>
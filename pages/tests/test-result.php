<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Test configurations
$testsConfig = [
    'iq-test' => [
        'title' => 'IQ Тест',
        'description' => 'Результаты теста на интеллект',
        'color' => '#e74c3c',
        'icon' => 'lightbulb',
        'scale' => [
            [130, 'Высокий IQ', 'Превосходный результат! Ваш интеллект значительно выше среднего.'],
            [115, 'Выше среднего', 'Отличный результат! Ваш IQ выше среднего уровня.'],
            [85, 'Средний IQ', 'Хороший результат! Ваш интеллект соответствует среднему уровню.'],
            [70, 'Ниже среднего', 'Результат ниже среднего. Рекомендуется дополнительная подготовка.'],
            [0, 'Низкий IQ', 'Низкий результат. Стоит больше тренировать логическое мышление.']
        ]
    ],
    'career-test' => [
        'title' => 'Тест на профориентацию',
        'description' => 'Результаты профориентационного теста',
        'color' => '#9b59b6',
        'icon' => 'user-tie',
        'scale' => [
            [90, 'Отличное соответствие', 'У вас четкие профессиональные предпочтения и склонности.'],
            [70, 'Хорошее соответствие', 'Ваши интересы достаточно определены для выбора направления.'],
            [50, 'Среднее соответствие', 'Стоит глубже изучить свои интересы и способности.'],
            [30, 'Слабое соответствие', 'Рекомендуется дополнительная профориентационная работа.'],
            [0, 'Неопределенность', 'Необходима помощь специалиста по профориентации.']
        ]
    ],
    'math-test' => [
        'title' => 'Тест по математике',
        'description' => 'Результаты теста по математике',
        'color' => '#3498db',
        'icon' => 'calculator',
        'scale' => [
            [90, 'Отлично', 'Превосходные знания по математике! Вы отлично разбираетесь в предмете.'],
            [70, 'Хорошо', 'Хорошие знания по математике. Есть небольшие пробелы для улучшения.'],
            [50, 'Удовлетворительно', 'Базовые знания есть, но нужно больше практики.'],
            [30, 'Неудовлетворительно', 'Серьезные пробелы в знаниях. Нужна дополнительная подготовка.'],
            [0, 'Плохо', 'Критический уровень знаний. Требуется системное изучение предмета.']
        ]
    ],
    'russian-test' => [
        'title' => 'Тест по русскому языку',
        'description' => 'Результаты теста по русскому языку',
        'color' => '#e67e22',
        'icon' => 'spell-check',
        'scale' => [
            [90, 'Отлично', 'Превосходная грамотность! Вы отлично владеете русским языком.'],
            [70, 'Хорошо', 'Хорошее знание русского языка с небольшими недочетами.'],
            [50, 'Удовлетворительно', 'Базовый уровень грамотности. Рекомендуется больше читать.'],
            [30, 'Неудовлетворительно', 'Есть серьезные проблемы с грамотностью.'],
            [0, 'Плохо', 'Критический уровень. Необходимо изучение основ русского языка.']
        ]
    ]
];

$test = $_GET['test'] ?? 'iq-test';
$testConfig = $testsConfig[$test] ?? $testsConfig['iq-test'];

// Check if test was completed
if (!isset($_SESSION['answers']) || empty($_SESSION['answers'])) {
    header("Location: /test/" . $test);
    exit;
}

$score = $_SESSION['score'] ?? 0;
$answers = $_SESSION['answers'] ?? [];
$totalQuestions = count($answers);
$percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;
$totalTime = ($_SESSION['end_time'] ?? time()) - ($_SESSION['start_time'] ?? time());
$averageTime = $totalQuestions > 0 ? round($totalTime / $totalQuestions) : 0;

// Calculate rating based on percentage
$rating = end($testConfig['scale']);
foreach ($testConfig['scale'] as $scale) {
    if ($percentage >= $scale[0]) {
        $rating = $scale;
        break;
    }
}

$pageTitle = 'Результаты: ' . $testConfig['title'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            padding: 40px 0;
        }
        .result-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }
        .result-header {
            background: <?= $testConfig['color'] ?>;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .result-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .result-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .result-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        .score-display {
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        .score-main {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .score-details {
            font-size: 12px;
            opacity: 0.9;
        }
        .result-body {
            padding: 20px;
        }
        .rating-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .rating-badge {
            background: <?= $testConfig['color'] ?>;
            color: white;
            padding: 8px 16px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 10px;
        }
        .rating-description {
            font-size: 13px;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: <?= $testConfig['color'] ?>;
            margin-bottom: 8px;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .progress-section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        .progress-bar-custom {
            background: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .progress-fill {
            background: linear-gradient(90deg, <?= $testConfig['color'] ?>, <?= $testConfig['color'] ?>88);
            height: 100%;
            border-radius: 10px;
            transition: width 2s ease;
            position: relative;
        }
        .progress-text {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        .answers-review {
            margin-top: 40px;
        }
        .answer-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid <?= $testConfig['color'] ?>;
        }
        .answer-item.wrong {
            border-left-color: #e74c3c;
            background: #fdeded;
        }
        .answer-question {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .answer-details {
            font-size: 14px;
            color: #666;
        }
        .answer-status {
            float: right;
            font-size: 20px;
        }
        .correct { color: #27ae60; }
        .wrong { color: #e74c3c; }
        .action-buttons {
            text-align: center;
            margin-top: 40px;
        }
        .btn-action {
            background: <?= $testConfig['color'] ?>;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: #6c757d;
        }
        @media (max-width: 768px) {
            body {
                background: white !important;
            }
            .main-content {
                background: white !important;
                min-height: 100vh;
            }
            .result-body {
                padding: 15px;
            }
            .result-header {
                padding: 20px;
            }
            .score-main {
                font-size: 24px;
            }
            .score-display {
                padding: 15px;
                margin-top: 15px;
            }
            .result-icon {
                font-size: 32px;
                margin-bottom: 10px;
            }
            .result-title {
                font-size: 20px;
            }
            .result-subtitle {
                font-size: 12px;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 15px;
            }
            .stat-card {
                padding: 12px;
            }
            .stat-number {
                font-size: 18px;
            }
            .stat-label {
                font-size: 10px;
            }
            .rating-badge {
                font-size: 12px;
                padding: 6px 12px;
            }
            .rating-description {
                font-size: 11px;
            }
            .section-title {
                font-size: 16px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <div class="result-container">
                <div class="result-header">
                    <i class="fas fa-<?= $testConfig['icon'] ?> result-icon"></i>
                    <h1 class="result-title">Тест завершен!</h1>
                    <p class="result-subtitle"><?= htmlspecialchars($testConfig['description']) ?></p>
                    
                    <div class="score-display">
                        <div class="score-main"><?= $score ?>/<?= $totalQuestions ?></div>
                        <div class="score-details">Правильных ответов: <?= $percentage ?>%</div>
                    </div>
                </div>
                
                <div class="result-body">
                    <div class="rating-section">
                        <div class="rating-badge"><?= htmlspecialchars($rating[1]) ?></div>
                        <p class="rating-description"><?= htmlspecialchars($rating[2]) ?></p>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?= $percentage ?>%</div>
                            <div class="stat-label">Точность</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= gmdate("i:s", $totalTime) ?></div>
                            <div class="stat-label">Общее время</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $averageTime ?>с</div>
                            <div class="stat-label">Среднее время на вопрос</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $totalQuestions - $score ?></div>
                            <div class="stat-label">Ошибок</div>
                        </div>
                    </div>
                    
                    <div class="progress-section">
                        <h3 class="section-title">Ваш результат</h3>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: <?= $percentage ?>%">
                                <span class="progress-text"><?= $percentage ?>%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="answers-review">
                        <h3 class="section-title">Разбор ответов</h3>
                        <?php foreach ($answers as $index => $answer): ?>
                            <div class="answer-item <?= $answer['is_correct'] ? '' : 'wrong' ?>">
                                <span class="answer-status <?= $answer['is_correct'] ? 'correct' : 'wrong' ?>">
                                    <i class="fas fa-<?= $answer['is_correct'] ? 'check' : 'times' ?>"></i>
                                </span>
                                <div class="answer-question">
                                    <?= $index + 1 ?>. <?= htmlspecialchars($answer['question']) ?>
                                </div>
                                <div class="answer-details">
                                    <strong>Ваш ответ:</strong> <?= htmlspecialchars($answer['selected']) ?><br>
                                    <?php if (!$answer['is_correct']): ?>
                                        <strong>Правильный ответ:</strong> <?= htmlspecialchars($answer['correct']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($answer['explanation'])): ?>
                                        <strong>Объяснение:</strong> <?= htmlspecialchars($answer['explanation']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="/test/<?= $test ?>?reset=true" class="btn-action">
                            <i class="fas fa-redo me-2"></i>Пройти снова
                        </a>
                        <a href="/tests" class="btn-action btn-secondary">
                            <i class="fas fa-list me-2"></i>Другие тесты
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate progress bar on load
        window.addEventListener('load', function() {
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                progressFill.style.width = '0%';
                setTimeout(() => {
                    progressFill.style.width = '<?= $percentage ?>%';
                }, 500);
            }
        });
    </script>
</body>
</html>

<?php
// Clear session data after showing results
unset($_SESSION['test_type']);
unset($_SESSION['question_index']);
unset($_SESSION['score']);
unset($_SESSION['answers']);
unset($_SESSION['start_time']);
unset($_SESSION['end_time']);
?>
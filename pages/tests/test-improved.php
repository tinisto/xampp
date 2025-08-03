<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Test configurations
$testsConfig = [
    'iq-test' => [
        'title' => 'IQ Тест',
        'description' => 'Классический тест на определение уровня интеллекта',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/iq-test/questions.php',
        'time_limit' => 20,
        'color' => '#e74c3c',
        'icon' => 'lightbulb'
    ],
    'career-test' => [
        'title' => 'Тест на профориентацию',
        'description' => 'Определите свои профессиональные склонности',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/career-test/questions.php',
        'time_limit' => 15,
        'color' => '#9b59b6',
        'icon' => 'user-tie'
    ],
    'math-test' => [
        'title' => 'Тест по математике',
        'description' => 'Проверьте свои знания по математике',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/math-test/questions.php',
        'time_limit' => 25,
        'color' => '#3498db',
        'icon' => 'calculator'
    ],
    'russian-test' => [
        'title' => 'Тест по русскому языку',
        'description' => 'Проверьте свою грамотность',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/russian-test/questions.php',
        'time_limit' => 20,
        'color' => '#e67e22',
        'icon' => 'spell-check'
    ],
    'biology-test' => [
        'title' => 'Тест по биологии',
        'description' => 'Проверьте свои знания по биологии',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/biology-test/questions.php',
        'time_limit' => 25,
        'color' => '#27ae60',
        'icon' => 'leaf'
    ],
    'chemistry-test' => [
        'title' => 'Тест по химии',
        'description' => 'Проверьте свои знания по химии',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/chemistry-test/questions.php',
        'time_limit' => 25,
        'color' => '#f39c12',
        'icon' => 'flask'
    ],
    'physics-test' => [
        'title' => 'Тест по физике',
        'description' => 'Проверьте свои знания по физике',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/physics-test/questions.php',
        'time_limit' => 25,
        'color' => '#2980b9',
        'icon' => 'atom'
    ],
    'geography-test' => [
        'title' => 'Тест по географии',
        'description' => 'Проверьте свои знания по географии',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/geography-test/questions.php',
        'time_limit' => 20,
        'color' => '#16a085',
        'icon' => 'globe'
    ],
    'personality-test' => [
        'title' => 'Тест личности',
        'description' => 'Узнайте особенности своей личности',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/personality-test/questions.php',
        'time_limit' => 15,
        'color' => '#8e44ad',
        'icon' => 'user-circle'
    ],
    'aptitude-test' => [
        'title' => 'Тест на профпригодность',
        'description' => 'Определите свою профессиональную пригодность',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/aptitude-test/questions.php',
        'time_limit' => 20,
        'color' => '#34495e',
        'icon' => 'briefcase'
    ]
];

$test = $_GET['test'] ?? 'iq-test';
$testConfig = $testsConfig[$test] ?? $testsConfig['iq-test'];
$questions = $testConfig['questions'] ?? [];

if (empty($questions)) {
    header("Location: /tests");
    exit;
}

$pageTitle = $testConfig['title'];

// Handle test reset
if (isset($_GET['reset']) && $_GET['reset'] == 'true') {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['test_type'] = $test;
    $_SESSION['question_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['answers'] = [];
    $_SESSION['start_time'] = time();
    header('Location: /test/' . $test);
    exit;
}

// Initialize session
if (!isset($_SESSION['question_index']) || $_SESSION['test_type'] !== $test) {
    $_SESSION['test_type'] = $test;
    $_SESSION['question_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['answers'] = [];
    $_SESSION['start_time'] = time();
}

$questionIndex = $_SESSION['question_index'];
$currentQuestion = $questions[$questionIndex] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedAnswer = $_POST['answer'] ?? null;
    $timeSpent = (int)($_POST['time_spent'] ?? 0);
    
    if ($selectedAnswer && $currentQuestion) {
        $isCorrect = $selectedAnswer === (string)$currentQuestion['correct_answer'];
        if ($isCorrect) {
            $_SESSION['score']++;
        }
        
        $_SESSION['answers'][] = [
            'question' => $currentQuestion['question'],
            'selected' => $selectedAnswer,
            'correct' => $currentQuestion['correct_answer'],
            'is_correct' => $isCorrect,
            'explanation' => $currentQuestion['explanation'] ?? '',
            'time_spent' => $timeSpent
        ];
        
        $_SESSION['question_index']++;
        
        if ($_SESSION['question_index'] >= count($questions)) {
            $_SESSION['end_time'] = time();
            header('Location: /test-result/' . $test);
            exit;
        }
        header('Location: /test/' . $test);
        exit;
    }
}

$totalQuestions = count($questions);
$progress = (($questionIndex + 1) / $totalQuestions) * 100;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - 11-классники</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        /* Dark mode styles */
        [data-theme="dark"] body {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        }
        
        [data-theme="dark"] .test-container {
            background: transparent;
            color: #f9fafb;
            box-shadow: none;
        }
        
        [data-theme="dark"] .question-text {
            color: #f9fafb;
        }
        
        [data-theme="dark"] .answer-option {
            background: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        [data-theme="dark"] .answer-option:hover {
            background: #4b5563;
        }
        
        [data-theme="dark"] .answer-text {
            color: #f9fafb !important;
        }
        
        [data-theme="dark"] .test-controls {
            background: #1f2937;
            border-color: #374151;
        }
        
        /* Top buttons container */
        .top-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        /* Theme toggle button */
        .theme-toggle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #333;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.05);
        }
        
        [data-theme="dark"] .theme-toggle {
            background: rgba(31, 41, 55, 0.9);
            color: #f9fafb;
        }
        
        [data-theme="dark"] .theme-toggle:hover {
            background: rgba(31, 41, 55, 1);
        }
        
        [data-theme="dark"] .timer {
            background: #1f2937;
            color: #f9fafb;
        }
        
        [data-theme="dark"] .timer.warning {
            background: #92400e;
            color: #fcd34d;
        }
        
        [data-theme="dark"] .btn-leave {
            background: #374151 !important;
            color: #fca5a5 !important;
            border-color: #fca5a5 !important;
        }
        
        [data-theme="dark"] .btn-leave:hover {
            background: #ef4444 !important;
            color: white !important;
            border-color: #ef4444 !important;
        }
        
        /* Main Content - Consistent with site padding */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 20px; /* Consistent with site container padding */
            justify-content: center;
        }
        
        .test-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            min-height: 0; /* Allow flexbox children to shrink */
        }
        
        /* Container wrapper for centering */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        
        .test-header {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        
        .test-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        [data-theme="dark"] .test-header {
            border-bottom-color: #374151;
        }
        
        [data-theme="dark"] .test-title {
            color: #f9fafb;
        }
        
        /* Test Body - Consistent padding with site */
        .test-body {
            padding: 20px; /* Consistent with site padding */
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0; /* Allow content to shrink */
        }
        
        /* Question Content Area - Scrollable */
        .question-content {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        
        .question-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .question-counter {
            font-weight: 600;
        }
        
        .questions-remaining {
            color: #999;
        }
        
        .question-text {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
            line-height: 1.4;
        }
        
        .answer-option {
            background: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px;
            padding: 15px !important;
            margin-bottom: 12px;
            cursor: pointer !important;
            transition: all 0.3s ease;
            position: relative;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: all !important;
            z-index: 1;
        }
        
        .answer-option:hover {
            border-color: <?= $testConfig['color'] ?>;
            background: <?= $testConfig['color'] ?>10;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .answer-option:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .answer-option.selected {
            border-color: <?= $testConfig['color'] ?> !important;
            background: <?= $testConfig['color'] ?>30 !important;
            border-width: 3px !important;
        }
        
        .answer-option input[type="radio"] {
            position: absolute;
            opacity: 0.01;
            width: calc(100% - 30px);
            height: calc(100% - 30px);
            top: 15px;
            left: 15px;
            cursor: pointer !important;
            z-index: 10;
            pointer-events: all !important;
            margin: 0;
            padding: 0;
        }
        
        .answer-text {
            font-size: 16px !important;
            font-weight: 500;
            margin: 0;
            color: #333 !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            line-height: 1.5 !important;
            text-align: left !important;
        }
        
        /* Test Controls - Always visible */
        .test-controls {
            flex-shrink: 0; /* Never shrink */
            display: flex;
            justify-content: center;
            gap: 15px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            background: white; /* Ensure background */
        }
        
        .btn-next {
            background: <?= $testConfig['color'] ?>;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.5;
            pointer-events: none;
            display: block;
            width: 100%;
            max-width: 400px;
            margin: 20px auto 0;
        }
        
        .btn-next:hover.enabled {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        .btn-next:active.enabled {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .btn-next.enabled {
            opacity: 1;
            pointer-events: auto;
        }
        
        .btn-next:hover.enabled {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        
        /* Exit button as circular icon */
        .btn-leave {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            border: 2px solid #dc3545;
            color: #dc3545;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-leave:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220,53,69,0.3);
        }
        
        .btn-leave:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(220,53,69,0.3);
        }
        
        .timer {
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-weight: 600;
            color: #333;
            z-index: 1000;
        }
        
        .timer.warning {
            background: #ffeaa7;
            color: #d63031;
        }
        
        /* Mobile Styles - Optimized for long questions */
        @media (max-width: 768px) {
            .main-content {
                padding: 0 15px; /* Standard mobile padding */
                margin: 0;
                align-items: flex-start; /* Don't stretch */
                position: relative; /* For absolute positioning */
            }
            
            
            .test-container {
                border-radius: 16px;
                margin: 10px 0; /* Simple margins */
                width: 100%; /* Full container width */
                height: auto; /* Auto height, not 100vh */
                max-height: calc(100vh - 100px); /* Leave space for buttons */
                min-height: auto; /* No minimum height */
                overflow-y: auto; /* Allow scrolling if needed */
            }
            
            .test-header {
                padding: 60px 20px 20px !important; /* Extra top padding for buttons */
            }
            
            .test-title {
                font-size: 22px !important; /* Smaller on mobile */
                line-height: 1.3 !important;
                margin: 0 !important;
            }
            
            .test-body {
                padding: 15px; /* Standard mobile padding */
                margin: 0;
                height: auto; /* Auto height */
                min-height: 0;
                max-height: none; /* No height constraints */
                overflow: visible; /* Show all content */
            }
            
            .question-content {
                flex: 1;
                min-height: 0;
                overflow-y: auto;
                /* Add some padding to ensure content doesn't touch edges */
                padding-right: 5px;
            }
            
            .question-text {
                font-size: 18px;
                margin-bottom: 20px;
                text-align: left; /* Better for mobile reading */
            }
            
            .answer-option {
                padding: 12px;
                margin-bottom: 10px;
            }
            
            .answer-text {
                font-size: 14px;
            }
            
            /* Next button - full width on mobile */
            .btn-next {
                width: 100% !important;
                max-width: none !important;
                margin: 20px 0 !important;
                padding: 14px 20px !important;
                font-size: 16px !important;
                display: block !important;
                position: static !important;
                background: <?= $testConfig['color'] ?> !important;
                color: white !important;
                border: none !important;
                border-radius: 25px !important;
                font-weight: 600 !important;
                cursor: pointer !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
            }
            
            .btn-next.enabled {
                opacity: 1 !important;
            }
            
            
            .top-buttons {
                top: 15px !important;
                right: 15px !important;
                gap: 8px !important;
            }
            
            .theme-toggle {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
            }
            
            .question-info {
                font-size: 12px;
                margin-bottom: 15px;
            }
            
            /* Ensure test controls are visible on mobile */
            .test-controls {
                position: sticky !important;
                bottom: 0 !important;
                background: white !important;
                z-index: 50 !important;
                padding: 15px !important;
                border-top: 1px solid #eee !important;
                margin: 0 -15px !important; /* Negative margin to full width */
                width: calc(100% + 30px) !important;
            }
            
            [data-theme="dark"] .test-controls {
                background: #1f2937 !important;
                border-top-color: #374151 !important;
            }
            
            
            /* Exit button on mobile - keep circular */
            .btn-leave {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
            }
        }
        
        /* Very small screens - ensure button is always visible */
        @media (max-width: 480px) {
            .test-container {
                max-height: calc(100vh - 180px); /* More space for buttons */
            }
            
            .main-content {
                padding: 10px;
            }
            
            .question-text {
                font-size: 16px;
                line-height: 1.3;
            }
            
            .test-controls {
                padding: 12px 15px;
            }
            
            .btn-next {
                padding: 14px 20px; /* Larger touch target */
                font-size: 16px;
            }
        }
        
        /* Landscape mobile - optimize for wide screens with less height */
        @media (max-width: 768px) and (max-height: 500px) {
            .test-container {
                max-height: calc(100vh - 140px); /* Space for buttons */
            }
            
            .main-content {
                padding: 5px;
                align-items: stretch;
            }
            
            .question-text {
                font-size: 14px;
                margin-bottom: 15px;
            }
            
            .answer-option {
                padding: 8px 12px;
                margin-bottom: 8px;
            }
            
            .test-controls {
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Buttons Container -->
    <div class="top-buttons">
        <button class="theme-toggle" onclick="toggleTheme()" aria-label="Переключить тему">
            <i class="fas fa-moon" id="theme-icon"></i>
        </button>
        <button type="button" class="btn-leave" onclick="leaveTest()" aria-label="Покинуть тест">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <?php // Header hidden during test - include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <?php if ($currentQuestion): ?>
                
                <div class="test-container">
                    <div class="test-header">
                        <h1 class="test-title"><?= htmlspecialchars($testConfig['title']) ?></h1>
                    </div>
                    
                    <div class="test-body">
                        <div class="question-info">
                            <span class="question-counter">Вопрос <?= $questionIndex + 1 ?> из <?= $totalQuestions ?></span>
                            <span class="questions-remaining">Осталось: <?= $totalQuestions - $questionIndex - 1 ?></span>
                        </div>
                        
                        <h2 class="question-text"><?= htmlspecialchars($currentQuestion['question']) ?></h2>
                        
                        <form method="POST" id="test-form">
                            <input type="hidden" name="time_spent" id="time_spent" value="0">
                            
                            <?php foreach ($currentQuestion['choices'] as $index => $choice): ?>
                                <label class="answer-option" for="choice-<?= $index ?>">
                                    <input type="radio" name="answer" value="<?= htmlspecialchars((string)$choice) ?>" id="choice-<?= $index ?>">
                                    <p class="answer-text"><?= htmlspecialchars((string)$choice) ?></p>
                                </label>
                            <?php endforeach; ?>
                            
                            <button type="submit" class="btn-next" id="next-btn">
                                <?= $questionIndex + 1 < $totalQuestions ? 'Следующий вопрос' : 'Завершить тест' ?>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
            <?php else: ?>
                <div class="test-container">
                    <div class="test-body text-center">
                        <h2>Тест завершен!</h2>
                        <p>Все вопросы пройдены.</p>
                        <a href="/test/<?= $test ?>?reset=true" class="btn-next">Пройти снова</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php // Footer hidden during test - include \$_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        // Leave test function
        function leaveTest() {
            // Remove beforeunload protection to allow leaving without confirmation
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            window.location.href = '/tests';
        }
        
        
        // Answer selection - Enhanced click handling
        document.querySelectorAll('input[name="answer"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Remove selected class from all options
                document.querySelectorAll('.answer-option').forEach(function(option) {
                    option.classList.remove('selected');
                });
                
                // Add selected class to chosen option
                this.closest('.answer-option').classList.add('selected');
                
                // Enable next button
                document.getElementById('next-btn').classList.add('enabled');
            });
        });
        
        // Additional click handling for labels
        document.querySelectorAll('.answer-option').forEach(function(label) {
            label.addEventListener('click', function(e) {
                const radio = this.querySelector('input[type="radio"]');
                if (radio && !radio.checked) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });
        });
        
        // Form validation
        document.getElementById('test-form').addEventListener('submit', function(e) {
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                e.preventDefault();
                alert('Пожалуйста, выберите ответ перед продолжением');
                return false;
            }
            
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key >= '1' && e.key <= '4') {
                const index = parseInt(e.key) - 1;
                const radio = document.getElementById(`choice-${index}`);
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            } else if (e.key === 'Enter') {
                const nextBtn = document.getElementById('next-btn');
                if (nextBtn.classList.contains('enabled')) {
                    nextBtn.click();
                }
            }
        });
        
        
        // Prevent accidental page refresh
        function beforeUnloadHandler(e) {
            if (document.querySelector('input[name="answer"]:checked')) {
                return;
            }
            e.preventDefault();
            e.returnValue = '';
        }
        
        window.addEventListener('beforeunload', beforeUnloadHandler);
        
        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('preferred-theme', newTheme);
            
            // Update theme icon
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        }
        
        // Initialize theme on page load
        (function() {
            const savedTheme = localStorage.getItem('preferred-theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            html.setAttribute('data-theme', savedTheme);
            if (themeIcon) {
                themeIcon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }
        })();
    </script>
</body>
</html>
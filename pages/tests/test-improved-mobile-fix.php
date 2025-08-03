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
        $isCorrect = $selectedAnswer === $currentQuestion['correct_answer'];
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
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .answer-option:hover {
            border-color: <?= $testConfig['color'] ?>;
            background: <?= $testConfig['color'] ?>10;
        }
        
        .answer-option.selected {
            border-color: <?= $testConfig['color'] ?>;
            background: <?= $testConfig['color'] ?>20;
        }
        
        .answer-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .answer-text {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
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
        }
        
        .btn-next.enabled {
            opacity: 1;
            pointer-events: auto;
        }
        
        .btn-next:hover.enabled {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .leave-test-container {
            text-align: center;
            margin-top: 20px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }
        
        .btn-leave {
            background: transparent;
            color: #dc3545;
            border: 2px solid #dc3545;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-leave:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.05);
        }
        
        .timer {
            position: fixed;
            top: 20px;
            right: 20px;
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
                padding: 15px; /* Consistent mobile padding */
                align-items: stretch; /* Full height on mobile */
            }
            
            .test-container {
                border-radius: 16px;
                height: calc(100vh - 30px); /* Full height minus padding */
                max-height: none; /* Remove max-height restriction */
            }
            
            .test-body {
                padding: 15px; /* Consistent mobile padding */
                height: 100%;
                min-height: 0;
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
            
            /* Controls are always visible and accessible */
            .test-controls {
                flex-shrink: 0;
                position: sticky;
                bottom: 0;
                background: white;
                border-top: 2px solid #eee;
                margin: 0 -15px -15px; /* Extend to edges */
                padding: 15px;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            }
            
            .btn-next {
                width: 100%;
                padding: 12px 20px;
                font-size: 16px; /* Larger touch target */
            }
            
            .timer {
                position: static;
                margin-bottom: 15px;
                text-align: center;
                font-size: 14px;  
                padding: 10px 15px;
            }
            
            .question-info {
                font-size: 12px;
                margin-bottom: 15px;
            }
            
            .leave-test-container {
                margin-top: 15px;
                padding: 15px;
            }
            
            .btn-leave {
                width: 100%;
                padding: 10px 20px;
            }
        }
        
        /* Very small screens - ensure button is always visible */
        @media (max-width: 480px) {
            .test-container {
                height: calc(100vh - 20px);
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
                height: calc(100vh - 10px);
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
    <main class="main-content">
        <?php if ($currentQuestion): ?>
            <div class="timer" id="timer">
                <i class="fas fa-clock me-2"></i>
                <span id="time-display">00:00</span>
            </div>
            
            <div class="test-container">
                <div class="test-body">
                    <div class="question-content">
                        <div class="question-info">
                            <span class="question-counter">Вопрос <?= $questionIndex + 1 ?> из <?= $totalQuestions ?></span>
                            <span class="questions-remaining">Осталось: <?= $totalQuestions - $questionIndex - 1 ?></span>
                        </div>
                        
                        <h2 class="question-text"><?= htmlspecialchars($currentQuestion['question']) ?></h2>
                        
                        <form method="POST" id="test-form">
                            <input type="hidden" name="time_spent" id="time_spent" value="0">
                            
                            <?php foreach ($currentQuestion['choices'] as $index => $choice): ?>
                                <label class="answer-option" for="choice-<?= $index ?>">
                                    <input type="radio" name="answer" value="<?= htmlspecialchars($choice) ?>" id="choice-<?= $index ?>">
                                    <p class="answer-text"><?= htmlspecialchars($choice) ?></p>
                                </label>
                            <?php endforeach; ?>
                        </form>
                    </div>
                    
                    <div class="test-controls">
                        <button type="submit" form="test-form" class="btn-next" id="next-btn">
                            <?= $questionIndex + 1 < $totalQuestions ? 'Следующий вопрос' : 'Завершить тест' ?>
                            <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="leave-test-container">
                <button type="button" class="btn-leave" onclick="leaveTest()">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>
                    Покинуть тест
                </button>
            </div>
        <?php else: ?>
            <div class="test-container">
                <div class="test-body" style="text-align: center;">
                    <h2>Тест завершен!</h2>
                    <p>Все вопросы пройдены.</p>
                    <a href="/test/<?= $test ?>?reset=true" class="btn-next" style="display: inline-block; margin-top: 20px;">Пройти снова</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <script>
        let questionStartTime = Date.now();
        let timerInterval;
        
        // Leave test function
        function leaveTest() {
            window.location.href = '/tests';
        }
        
        // Timer functionality
        function startTimer() {
            timerInterval = setInterval(function() {
                const elapsed = Math.floor((Date.now() - questionStartTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                const timeDisplay = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.getElementById('time-display').textContent = timeDisplay;
                document.getElementById('time_spent').value = elapsed;
                
                // Warning after 2 minutes per question
                if (elapsed > 120) {
                    document.getElementById('timer').classList.add('warning');
                }
            }, 1000);
        }
        
        // Answer selection
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
        
        // Form validation
        document.getElementById('test-form').addEventListener('submit', function(e) {
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                e.preventDefault();
                alert('Пожалуйста, выберите ответ перед продолжением');
                return false;
            }
            
            clearInterval(timerInterval);
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
        
        // Start timer when page loads
        startTimer();
        
        // Prevent accidental page refresh
        window.addEventListener('beforeunload', function(e) {
            if (document.querySelector('input[name="answer"]:checked')) {
                return;
            }
            e.preventDefault();
            e.returnValue = '';
        });
        
        // Ensure controls are always visible on mobile
        function ensureControlsVisible() {
            if (window.innerWidth <= 768) {
                const controls = document.querySelector('.test-controls');
                const container = document.querySelector('.test-container');
                
                if (controls && container) {
                    // Make sure controls are always at the bottom
                    controls.style.position = 'sticky';
                    controls.style.bottom = '0';
                    controls.style.zIndex = '100';
                }
            }
        }
        
        // Run on load and resize
        window.addEventListener('load', ensureControlsVisible);
        window.addEventListener('resize', ensureControlsVisible);
    </script>
</body>
</html>
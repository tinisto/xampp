<?php
// Full test mode with single question display
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$test = $_GET['test'] ?? '';
$questionIndex = (int)($_GET['q'] ?? 0);

// Validate test exists
$testPath = $_SERVER['DOCUMENT_ROOT'] . "/pages/tests/{$test}/questions.php";
if (!file_exists($testPath)) {
    header("Location: /tests");
    exit();
}

// Load questions
$questions = include $testPath;
$totalQuestions = count($questions);

// Check if question index is valid
if (!isset($questions[$questionIndex])) {
    header("Location: /test-full/$test?q=0");
    exit();
}

$question = $questions[$questionIndex];

// Test titles
$testTitles = [
    'iq-test' => 'IQ Тест',
    'career-test' => 'Тест на профориентацию',
    'math-test' => 'Тест по математике',
    'russian-test' => 'Тест по русскому языку',
    'physics-test' => 'Тест по физике',
    'geography-test' => 'Тест по географии',
    'personality-test' => 'Тест типа личности',
    'aptitude-test' => 'Тест способностей',
    'biology-test' => 'Тест по биологии',
    'chemistry-test' => 'Тест по химии'
];

$pageTitle = $testTitles[$test] ?? 'Онлайн тест';

// Handle form submission
if ($_POST && isset($_POST['answer'])) {
    // Store answer in session
    session_start();
    if (!isset($_SESSION['test_answers'])) {
        $_SESSION['test_answers'] = [];
    }
    if (!isset($_SESSION['test_answers'][$test])) {
        $_SESSION['test_answers'][$test] = [];
    }
    $_SESSION['test_answers'][$test][$questionIndex] = $_POST['answer'];
    
    // Move to next question or finish
    if ($questionIndex + 1 < $totalQuestions) {
        header("Location: /test-full/$test?q=" . ($questionIndex + 1));
    } else {
        // Submit all answers for results
        $_POST['answers'] = $_SESSION['test_answers'][$test];
        include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/test-improved.php';
        exit();
    }
    exit();
}

// Get stored answer if exists
session_start();
$storedAnswer = $_SESSION['test_answers'][$test][$questionIndex] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Вопрос <?= $questionIndex + 1 ?></title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background, #ffffff);
            color: var(--text-primary, #212529);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Close Button */
        .close-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--surface, #f8f9fa);
            border: 2px solid var(--border-color, #dee2e6);
            color: var(--text-primary, #212529);
            width: 50px;
            height: 50px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,0.08));
        }
        
        .close-btn:hover {
            background: var(--primary-color, #28a745);
            border-color: var(--primary-color, #28a745);
            color: white;
            transform: scale(1.1);
            box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.1));
        }
        
        /* Progress Bar */
        .progress-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            z-index: 999;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
        
        /* Test Content Container */
        .test-content {
            max-width: 800px;
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        
        @media (min-width: 769px) {
            .test-content {
                background: var(--surface, #f8f9fa);
                border: 1px solid var(--border-color, #dee2e6);
                border-radius: 20px;
                padding: 40px;
                box-shadow: var(--shadow-lg, 0 10px 25px rgba(0,0,0,0.15));
            }
        }
        
        .question-number {
            color: var(--text-secondary, #6c757d);
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .question-text {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            line-height: 1.4;
            color: var(--text-primary, #212529);
        }
        
        /* Choice Styling */
        .choices {
            display: grid;
            gap: 12px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .choice {
            background: var(--surface, #f8f9fa);
            border: 2px solid var(--border-color, #dee2e6);
            border-radius: 15px;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            position: relative;
            display: flex;
            align-items: center;
            color: var(--text-primary, #212529);
        }
        
        .choice:hover {
            background: var(--background, #ffffff);
            border-color: var(--primary-color, #28a745);
            transform: translateX(10px);
            box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.1));
        }
        
        .choice input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.3);
            accent-color: #667eea;
        }
        
        .choice.selected {
            background: var(--background, #ffffff);
            border-color: var(--primary-color, #28a745);
            transform: translateX(10px);
            box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.1));
        }
        
        .choice.selected span {
            color: var(--primary-color, #28a745);
            font-weight: 600;
        }
        
        
        /* Toggle Mode Button - Sun/Moon */
        .toggle-mode-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--surface, #f8f9fa);
            border: 2px solid var(--border-color, #dee2e6);
            color: var(--text-primary, #212529);
            width: 50px;
            height: 50px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,0.08));
        }
        
        .toggle-mode-btn:hover {
            background: var(--primary-color, #28a745);
            border-color: var(--primary-color, #28a745);
            color: white;
            transform: scale(1.1);
            box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.1));
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .test-content {
                padding: 15px;
                width: 100%;
            }
            
            .question-text {
                font-size: 20px;
            }
            
            .choice {
                padding: 15px 20px;
                font-size: 15px;
            }
            
            /* Remove transform shifts on mobile */
            .choice:hover,
            .choice.selected {
                transform: none !important;
            }
            
            .close-btn {
                top: 15px;
                right: 15px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            
            .toggle-mode-btn {
                top: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body data-theme="light">
    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-bar" style="width: <?= round((($questionIndex + 1) / $totalQuestions) * 100) ?>%;"></div>
    </div>
    
    <!-- Theme Toggle Button -->
    <button class="toggle-mode-btn" onclick="toggleTheme()" title="Изменить тему">
        <i class="fas fa-sun" id="themeIcon"></i>
    </button>
    
    <!-- Close Button -->
    <button class="close-btn" onclick="window.location.href='/tests'" title="Закрыть тест">
        <i class="fas fa-times"></i>
    </button>
    
    <!-- Question Content -->
    <div class="test-content">
        <div class="question-number">
            Вопрос <?= $questionIndex + 1 ?> из <?= $totalQuestions ?>
        </div>
        
        <div class="question-text">
            <?= htmlspecialchars($question['question']) ?>
        </div>
        
        <form method="post" id="questionForm">
            <div class="choices">
                <?php foreach ($question['choices'] as $index => $choice): ?>
                    <label class="choice" data-value="<?= htmlspecialchars($choice) ?>">
                        <input type="radio" name="answer" value="<?= htmlspecialchars($choice) ?>" 
                               <?= $storedAnswer === $choice ? 'checked' : '' ?>>
                        <span><?= htmlspecialchars($choice) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <!-- Hidden submit button - form will auto-submit on answer selection -->
            <button type="submit" id="hiddenSubmit" style="display: none;"></button>
        </form>
    </div>
    
    <script>
        // Toggle theme function
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = body.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'light');
                themeIcon.className = 'fas fa-sun';
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-moon';
            }
            
            localStorage.setItem('theme', body.getAttribute('data-theme'));
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const themeIcon = document.getElementById('themeIcon');
            
            document.body.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                themeIcon.className = 'fas fa-moon';
            } else {
                themeIcon.className = 'fas fa-sun';
            }
        });
        
        const choices = document.querySelectorAll('.choice');
        const hiddenSubmit = document.getElementById('hiddenSubmit');
        const questionForm = document.getElementById('questionForm');
        
        // Check if answer is already selected
        const checkedRadio = document.querySelector('input[name="answer"]:checked');
        if (checkedRadio) {
            const selectedChoice = checkedRadio.closest('.choice');
            selectedChoice.classList.add('selected');
        }
        
        // Handle choice selection - auto-submit on click
        choices.forEach(choice => {
            choice.addEventListener('click', function() {
                // Prevent multiple clicks
                if (this.classList.contains('submitting')) return;
                
                // Mark all choices as submitting to prevent multiple submissions
                choices.forEach(c => {
                    c.classList.add('submitting');
                    c.style.pointerEvents = 'none';
                });
                
                // Remove selected class from all choices
                choices.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked choice
                this.classList.add('selected');
                
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                // Add a slight delay for visual feedback before submitting
                setTimeout(() => {
                    questionForm.submit();
                }, 300);
            });
        });
        
        // Clear session storage on first question
        <?php if ($questionIndex === 0): ?>
        if (typeof(Storage) !== "undefined") {
            sessionStorage.removeItem('test_answers_<?= $test ?>');
        }
        <?php endif; ?>
        
        // Keyboard navigation - numbers 1-4 for answers
        document.addEventListener('keydown', function(e) {
            if (e.key >= '1' && e.key <= '4') {
                const choiceIndex = parseInt(e.key) - 1;
                if (choices[choiceIndex] && !choices[choiceIndex].classList.contains('submitting')) {
                    choices[choiceIndex].click();
                }
            }
        });
        
        // Add entrance animation
        document.querySelector('.test-content').style.transform = 'translateY(20px)';
        document.querySelector('.test-content').style.opacity = '0';
        
        setTimeout(() => {
            document.querySelector('.test-content').style.transform = 'translateY(0)';
            document.querySelector('.test-content').style.opacity = '1';
        }, 100);
    </script>
</body>
</html>
<?php
// Test implementation with original beautiful design
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$test = $_GET['test'] ?? '';

// Validate test exists
$testPath = $_SERVER['DOCUMENT_ROOT'] . "/pages/tests/{$test}/questions.php";
if (!file_exists($testPath)) {
    // Show unavailable message for non-existent tests
    $pageTitle = 'Тест недоступен';
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
            .hero-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0; text-align: center; }
        </style>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
        
        <section class="hero-section">
            <div class="container">
                <h1>Тест недоступен</h1>
                <p>Запрашиваемый тест "<?= htmlspecialchars($test) ?>" временно недоступен или находится в разработке.</p>
                <a href="/tests" class="btn btn-light btn-lg mt-3">
                    <i class="fas fa-arrow-left"></i> Вернуться к списку тестов
                </a>
            </div>
        </section>
        
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    </body>
    </html>
    <?php
    exit();
}

// Load questions
$questions = include $testPath;

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
if ($_POST && isset($_POST['answers'])) {
    // Calculate score
    $correctAnswers = 0;
    $totalQuestions = count($questions);
    $userAnswers = $_POST['answers'];
    $incorrectQuestions = [];
    
    foreach ($questions as $index => $question) {
        $userAnswer = $userAnswers[$index] ?? '';
        if ($userAnswer === $question['correct_answer']) {
            $correctAnswers++;
        } else {
            $incorrectQuestions[] = [
                'question' => $question['question'],
                'user_answer' => $userAnswer,
                'correct_answer' => $question['correct_answer'],
                'explanation' => $question['explanation']
            ];
        }
    }
    
    $score = round(($correctAnswers / $totalQuestions) * 100);
    
    // Store results in session for result page
    session_start();
    $_SESSION['test_results'] = [
        'test_type' => $test,
        'test_title' => $pageTitle,
        'score' => $score,
        'correct_answers' => $correctAnswers,
        'total_questions' => $totalQuestions,
        'incorrect_questions' => $incorrectQuestions
    ];
    
    // Redirect to results
    header("Location: /test-result/$test");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background, #ffffff);
            color: var(--text-primary, #212529);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .main-content {
            flex: 1;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        .hero-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .hero-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        .progress-section {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .progress-bar-custom {
            background: #e9ecef;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .progress-fill {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            transition: width 0.3s ease;
            width: 0%;
        }
        .progress-text {
            text-align: center;
            color: #666;
            font-weight: 500;
        }
        .question-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .question-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .question-number {
            color: #667eea;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .question-text {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            line-height: 1.4;
        }
        .choice {
            display: block;
            padding: 20px 25px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            font-size: 16px;
        }
        .choice:hover {
            background: #e9ecef;
            border-color: #667eea;
            transform: translateX(5px);
        }
        .choice input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.2);
        }
        .choice input[type="radio"]:checked + span {
            color: #667eea;
            font-weight: 600;
        }
        .choice:has(input[type="radio"]:checked) {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-color: #667eea;
            transform: translateX(5px);
        }
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 50px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 30px;
        }
        .submit-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        .submit-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Toggle Mode Button */
        .toggle-teacher-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
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
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .toggle-teacher-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .test-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 28px;
            }
            .question-card {
                padding: 30px 20px;
            }
            .question-text {
                font-size: 18px;
            }
            .choice {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Toggle to Teacher Mode Button -->
    <a href="/test/<?= $test ?>" class="toggle-teacher-btn" title="Переключить в режим обучения">
        <i class="fas fa-graduation-cap"></i>
    </a>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <h1 class="hero-title"><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
        </section>

        <!-- Progress Section -->
        <section class="progress-section">
            <div class="container">
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="progressBar"></div>
                </div>
            </div>
        </section>

        <!-- Test Questions -->
        <section class="py-5">
            <div class="test-container">
                <form method="POST" id="testForm">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question-card">
                            <div class="question-number">Вопрос <?= $index + 1 ?> из <?= count($questions) ?></div>
                            <div class="question-text"><?= htmlspecialchars($question['question']) ?></div>
                            
                            <?php foreach ($question['choices'] as $choice): ?>
                                <label class="choice">
                                    <input type="radio" name="answers[<?= $index ?>]" value="<?= htmlspecialchars($choice) ?>" onchange="updateProgress()">
                                    <span><?= htmlspecialchars($choice) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="text-center">
                        <button type="submit" class="submit-btn" id="submitBtn" disabled>
                            <i class="fas fa-check-circle me-2"></i>Завершить тест и посмотреть результаты
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
    
    <script>
        function updateProgress() {
            const totalQuestions = <?= count($questions) ?>;
            const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
            const progress = (answeredQuestions / totalQuestions) * 100;
            
            document.getElementById('progressBar').style.width = progress + '%';
            
            document.getElementById('submitBtn').disabled = answeredQuestions < totalQuestions;
        }
        
        // Smooth scrolling between questions
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateProgress();
                
                // Auto-scroll to next question
                const currentCard = this.closest('.question-card');
                const nextCard = currentCard.nextElementSibling;
                if (nextCard && nextCard.classList.contains('question-card')) {
                    setTimeout(() => {
                        nextCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 400);
                } else {
                    // Scroll to submit button if this was the last question
                    setTimeout(() => {
                        document.getElementById('submitBtn').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 400);
                }
            });
        });

        // Initial progress update
        updateProgress();
    </script>
</body>
</html>
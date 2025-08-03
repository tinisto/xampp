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

// Prepare data for template
$additionalData = [
    'test' => $test,
    'testConfig' => $testConfig,
    'testsConfig' => $testsConfig,
    'questions' => $questions,
    'currentQuestion' => $currentQuestion,
    'questionIndex' => $questionIndex,
    'layoutType' => 'default', // Use default layout to include header/footer
    'noHeader' => false, // We'll control header visibility in content
    'noFooter' => false  // We'll control footer visibility in content
];

// Include the unified template
$mainContent = 'pages/tests/test-improved-content-v2.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-unified.php';
renderTemplate($pageTitle, $mainContent, $additionalData);
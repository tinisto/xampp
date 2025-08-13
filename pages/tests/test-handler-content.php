<?php

// Define test configurations directly in the handler file
$testsConfig = [
    'iq-test' => [
        'title' => 'Тест на IQ',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/iq-test/questions.php',
    ],
    'aptitude-test' => [
        'title' => 'Тест на профпригодность',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/aptitude-test/questions.php',
    ],
    // Add more tests here as needed
];

$test = $_GET['test'] ?? 'iq-test'; // Default to IQ test if no test is specified

// Check if test config exists
if (!isset($testsConfig[$test])) {
    $test = 'iq-test'; // Fallback to IQ test
}

$testConfig = $testsConfig[$test];
$questions = $testConfig['questions'];

if (isset($_GET['reset']) && $_GET['reset'] == 'true') {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['question_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['failed_questions'] = [];
    header('Location: /' . $test); // Redirect to the short URL
    exit;
}

// Initialize session variables if not set
if (!isset($_SESSION['question_index'])) {
    $_SESSION['question_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['failed_questions'] = [];
}

// Get the current question index
$questionIndex = $_SESSION['question_index'];
$currentQuestion = $questions[$questionIndex] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedAnswer = $_POST['answer'] ?? null;
    if ($selectedAnswer === $currentQuestion['correct_answer']) {
        $_SESSION['score']++;
    } else {
        $_SESSION['failed_questions'][] = [
            'question' => $currentQuestion['question'],
            'correct_answer' => $currentQuestion['correct_answer'],
            'explanation' => $currentQuestion['explanation'],
        ];
    }
    $_SESSION['question_index']++;
    if ($_SESSION['question_index'] >= count($questions)) {
        // Store test name in session for result page
        $_SESSION['completed_test'] = $test;
        // Redirect to the result page
        header('Location: /test-result');
        exit;
    }
    header('Location: /' . $test); // Redirect to the next question
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h3 class="text-center mb-3"><?php echo htmlspecialchars($testConfig['title']); ?></h3>
        <?php if ($currentQuestion): ?>
            <div class="card">
                <div class="card-body">
                    <p class="text-center text-muted mb-1">
                        Вопрос <?php echo $_SESSION['question_index'] + 1; ?> из <?php echo count($questions); ?>
                    </p>
                    <h5 class="card-title"><?php echo htmlspecialchars($currentQuestion['question']); ?></h5>
                    <!-- Display choices for the question -->
                    <form method="POST" action="/<?php echo $test; ?>" onsubmit="validateForm(event)">
                        <?php foreach ($currentQuestion['choices'] as $choice): ?>
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input" type="radio" name="answer" value="<?php echo htmlspecialchars($choice); ?>" id="choice-<?php echo htmlspecialchars($choice); ?>">
                                <label class="form-check-label ms-2 p-2" for="choice-<?php echo htmlspecialchars($choice); ?>">
                                    <?php echo htmlspecialchars($choice); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div class="d-flex justify-content-center align-items-center mb-1">
                            <button type="submit" class="btn btn-primary mt-3">Следующий</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p>Больше нет доступных вопросов.</p>
            <a href="/<?php echo htmlspecialchars($test); ?>?reset=true" class="btn btn-primary btn-sm">Пройти тест снова</a>
        <?php endif; ?>
    </div>
</div>

<script>
    function validateForm(event) {
        const choices = document.querySelectorAll('input[name="answer"]');
        let isChecked = false;
        choices.forEach(choice => {
            if (choice.checked) {
                isChecked = true;
            }
        });
        if (!isChecked) {
            event.preventDefault();
            alert('Please select an answer before submitting.');
        }
    }
</script>
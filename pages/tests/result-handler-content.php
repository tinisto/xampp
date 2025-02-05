<?php


// Define test configurations directly in the handler file
$testsConfig = [
    'iq-test' => [
        'title' => 'Тест на IQ',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/iq-test/questions.php',
    ],
    'aptitude-test' => [
        'title' => 'Тесты на профпригодность',
        'questions' => include $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/aptitude-test/questions.php',
    ],
    // Add more tests here as needed
];

$test = $_GET['test'] ?? 'iq-test'; // Default to IQ test if no test is specified
$testConfig = $testsConfig[$test];
$questions = $testConfig['questions'];

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/getIQRating.php";

$score = $_SESSION['score'] ?? 0;
$total_questions = count($questions) ?? 0;
$failed_questions = $_SESSION['failed_questions'] ?? [];
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Result Heading -->
        <h1 class="text-center mb-4">Тест завершен!</h1>

        <h5 class="d-flex justify-content-between align-items-center mb-4 text-secondary">
            <span>
                Ваш счет: <strong><?php echo htmlspecialchars((string)$score); ?></strong>
                из <strong><?php echo htmlspecialchars((string)$total_questions); ?></strong>
            </span>
            <span>
                Ваш рейтинг IQ: <strong><?php echo htmlspecialchars(getIQRating($score, $total_questions)); ?></strong>
            </span>
        </h5>

        <!-- Failed Questions Section -->
        <?php if (!empty($failed_questions)): ?>
            <h4 class="mb-3 text-center">Вопросы, на которые вы ответили неправильно:</h4>
            <?php foreach ($failed_questions as $failed_question): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($failed_question['question']); ?></h5>
                        <p class="card-text">Правильный ответ: <?php echo htmlspecialchars($failed_question['correct_answer']); ?></p>
                        <p class="card-text">Объяснение: <?php echo htmlspecialchars($failed_question['explanation']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="d-flex justify-content-center align-items-center mb-1">
            <a href="/<?php echo htmlspecialchars($test); ?>?reset=true" class="btn btn-primary btn-sm">Пройти тест снова</a>
        </div>
    </div>
</div>
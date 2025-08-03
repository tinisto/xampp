<?php
session_start();

// Test biology test display
$testsConfig = [
    'biology-test' => [
        'title' => 'Тест по биологии',
        'description' => 'Проверьте свои знания по биологии',
        'questions' => include '/Applications/XAMPP/xamppfiles/htdocs/pages/tests/biology-test/questions.php',
        'time_limit' => 25,
        'color' => '#27ae60',
        'icon' => 'leaf'
    ]
];

$test = 'biology-test';
$testConfig = $testsConfig[$test];
$questions = $testConfig['questions'];

// Get question 2 (the one with integers)
$questionIndex = 1; // 0-based, so 1 = question 2
$currentQuestion = $questions[$questionIndex];

echo "<h1>Biology Test Display Debug</h1>";
echo "<h2>Question " . ($questionIndex + 1) . ":</h2>";
echo "<p><strong>Question:</strong> " . htmlspecialchars($currentQuestion['question']) . "</p>";
echo "<h3>Choices as they would appear in HTML:</h3>";

foreach ($currentQuestion['choices'] as $index => $choice) {
    echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 5px; background: #f8f9fa;">';
    echo '<label class="answer-option" for="choice-' . $index . '">';
    echo '<input type="radio" name="answer" value="' . htmlspecialchars((string)$choice) . '" id="choice-' . $index . '">';
    echo '<p class="answer-text" style="color: #333 !important; font-weight: bold;">' . htmlspecialchars((string)$choice) . '</p>';
    echo '</label>';
    echo '</div>';
}

echo "<h3>Raw choice data:</h3>";
echo "<pre>";
var_dump($currentQuestion['choices']);
echo "</pre>";
?>

<style>
.answer-option {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 12px;
    cursor: pointer;
    position: relative;
    display: block;
}

.answer-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.answer-text {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    color: #333 !important;
}
</style>
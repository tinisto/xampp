<?php
// Full test page - migrated to use real_template.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get parameters
$test = $_GET['test'] ?? '';
$questionIndex = (int)($_GET['q'] ?? 0);

// Test configurations
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

// Validate test exists
$testPath = $_SERVER['DOCUMENT_ROOT'] . "/pages/tests/{$test}/questions.php";
if (!file_exists($testPath) || !isset($testTitles[$test])) {
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
$pageTitle = $testTitles[$test];

// Initialize test answers in session
if (!isset($_SESSION['test_answers'])) {
    $_SESSION['test_answers'] = [];
}
if (!isset($_SESSION['test_answers'][$test])) {
    $_SESSION['test_answers'][$test] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $_SESSION['test_answers'][$test][$questionIndex] = $_POST['answer'];
    
    // Check if this is the last question
    if ($questionIndex + 1 >= $totalQuestions) {
        // Calculate score
        $score = 0;
        $failed_questions = [];
        
        foreach ($questions as $idx => $q) {
            $userAnswer = $_SESSION['test_answers'][$test][$idx] ?? null;
            if ($userAnswer == $q['correct']) {
                $score++;
            } else {
                $failed_questions[] = [
                    'question' => $q['question'],
                    'your_answer' => $q['options'][$userAnswer] ?? 'Не отвечено',
                    'correct_answer' => $q['options'][$q['correct']],
                    'explanation' => $q['explanation'] ?? ''
                ];
            }
        }
        
        // Store results in session
        $_SESSION['score'] = $score;
        $_SESSION['total_questions'] = $totalQuestions;
        $_SESSION['failed_questions'] = $failed_questions;
        
        // Clear test answers
        unset($_SESSION['test_answers'][$test]);
        
        // Redirect to results
        header("Location: /test-result/$test");
        exit();
    } else {
        // Go to next question
        header("Location: /test-full/$test?q=" . ($questionIndex + 1));
        exit();
    }
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($pageTitle, [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'Вопрос ' . ($questionIndex + 1) . ' из ' . $totalQuestions
]);
$greyContent1 = ob_get_clean();

// Section 2: Progress bar
ob_start();
$progress = (($questionIndex + 1) / $totalQuestions) * 100;
?>
<div style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background: #e9ecef; height: 10px; border-radius: 5px; overflow: hidden;">
            <div style="background: #28a745; height: 100%; width: <?= $progress ?>%; transition: width 0.3s ease;"></div>
        </div>
        <div style="margin-top: 10px; text-align: center; color: var(--text-secondary, #666);">
            Прогресс: <?= round($progress) ?>%
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Question and answers
ob_start();
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <h2 style="color: var(--text-primary, #333); margin-bottom: 30px; font-size: 24px; line-height: 1.4;">
            <?= htmlspecialchars($question['question']) ?>
        </h2>
        
        <form method="POST" id="questionForm">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($question['options'] as $key => $option): ?>
                    <?php $isSelected = isset($_SESSION['test_answers'][$test][$questionIndex]) && 
                                       $_SESSION['test_answers'][$test][$questionIndex] == $key; ?>
                    <label class="answer-option <?= $isSelected ? 'selected' : '' ?>">
                        <input type="radio" name="answer" value="<?= $key ?>" 
                               <?= $isSelected ? 'checked' : '' ?> required>
                        <span class="option-text"><?= htmlspecialchars($option) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <div style="display: flex; gap: 15px; justify-content: space-between; margin-top: 40px;">
                <?php if ($questionIndex > 0): ?>
                    <a href="/test-full/<?= $test ?>?q=<?= $questionIndex - 1 ?>" class="nav-button secondary">
                        <i class="fas fa-arrow-left"></i> Назад
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                
                <button type="submit" class="nav-button primary">
                    <?= ($questionIndex + 1 < $totalQuestions) ? 'Далее' : 'Завершить тест' ?>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Quick navigation -->
    <div style="margin-top: 30px; text-align: center;">
        <p style="color: var(--text-secondary, #666); margin-bottom: 15px;">Быстрая навигация:</p>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
            <?php for ($i = 0; $i < $totalQuestions; $i++): ?>
                <?php 
                $isAnswered = isset($_SESSION['test_answers'][$test][$i]);
                $isCurrent = $i === $questionIndex;
                ?>
                <a href="/test-full/<?= $test ?>?q=<?= $i ?>" 
                   class="question-nav <?= $isCurrent ? 'current' : '' ?> <?= $isAnswered ? 'answered' : '' ?>">
                    <?= $i + 1 ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</div>

<style>
.answer-option {
    display: flex;
    align-items: center;
    padding: 20px;
    background: var(--surface-light, #f8f9fa);
    border: 2px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.answer-option:hover {
    background: var(--surface-hover, #e9ecef);
    border-color: #28a745;
}

.answer-option.selected {
    background: #e8f5e9;
    border-color: #28a745;
}

.answer-option input[type="radio"] {
    margin-right: 15px;
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.option-text {
    flex: 1;
    color: var(--text-primary, #333);
    font-size: 16px;
}

.nav-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
}

.nav-button.primary {
    background: #28a745;
    color: white;
}

.nav-button.primary:hover {
    background: #218838;
    transform: translateY(-2px);
}

.nav-button.secondary {
    background: #6c757d;
    color: white;
}

.nav-button.secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.question-nav {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--surface-light, #f8f9fa);
    color: var(--text-primary, #333);
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.question-nav:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.question-nav.current {
    background: #28a745;
    color: white;
}

.question-nav.answered {
    background: #6c757d;
    color: white;
}

[data-theme="dark"] .answer-option {
    background: var(--surface-dark, #2d3748);
}

[data-theme="dark"] .answer-option:hover {
    background: var(--surface-darker, #1a202c);
}

[data-theme="dark"] .answer-option.selected {
    background: rgba(40, 167, 69, 0.2);
}

[data-theme="dark"] div[style*="background: var(--surface, #ffffff)"] {
    background: var(--surface-dark, #2d3748) !important;
}

[data-theme="dark"] .question-nav {
    background: var(--surface-dark, #2d3748);
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] .question-nav:hover {
    background: var(--surface-darker, #1a202c);
}
</style>

<script>
// Auto-submit when option is selected
document.querySelectorAll('input[name="answer"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Add visual feedback
        document.querySelectorAll('.answer-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        this.closest('.answer-option').classList.add('selected');
    });
});
</script>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
// Test result handler - migrated to use real_template.php

// Start session to get test results
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get test type
$test = $_GET['test'] ?? '';
if (empty($test)) {
    header('Location: /tests');
    exit();
}

// Get score and failed questions from session
$score = $_SESSION['score'] ?? 0;
$total_questions = $_SESSION['total_questions'] ?? 0;
$failed_questions = $_SESSION['failed_questions'] ?? [];

// Clear session data after getting it
unset($_SESSION['score']);
unset($_SESSION['total_questions']);
unset($_SESSION['failed_questions']);

// Calculate percentage
$percentage = $total_questions > 0 ? round(($score / $total_questions) * 100) : 0;

// Get rating based on percentage
function getTestRating($percentage) {
    if ($percentage >= 90) return 'Отлично';
    if ($percentage >= 75) return 'Хорошо';
    if ($percentage >= 60) return 'Удовлетворительно';
    if ($percentage >= 40) return 'Нужна практика';
    return 'Требуется подготовка';
}

$rating = getTestRating($percentage);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Тест завершен!', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Результаты вашего тестирования'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty navigation
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Test Results
ob_start();
?>
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    
    <!-- Score Card -->
    <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; margin-bottom: 30px;">
        
        <div style="margin-bottom: 30px;">
            <div style="font-size: 72px; font-weight: bold; color: #28a745; margin-bottom: 10px;">
                <?= $score ?>/<?= $total_questions ?>
            </div>
            <div style="font-size: 36px; color: var(--text-primary, #333); margin-bottom: 10px;">
                <?= $percentage ?>%
            </div>
            <div style="font-size: 24px; color: var(--text-secondary, #666);">
                <?= $rating ?>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden; margin-bottom: 30px;">
            <div style="background: #28a745; height: 100%; width: <?= $percentage ?>%; transition: width 1s ease;"></div>
        </div>
        
        <!-- Action Buttons -->
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/test/<?= htmlspecialchars($test) ?>?reset=true" class="result-button primary">
                <i class="fas fa-redo"></i> Пройти тест снова
            </a>
            <a href="/tests" class="result-button secondary">
                <i class="fas fa-list"></i> Все тесты
            </a>
        </div>
    </div>
    
    <!-- Failed Questions Section -->
    <?php if (!empty($failed_questions)): ?>
    <div style="background: var(--surface, #ffffff); padding: 30px; border-radius: 12px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="color: var(--text-primary, #333); margin-bottom: 25px; text-align: center;">
            Вопросы, на которые вы ответили неправильно
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($failed_questions as $index => $failed): ?>
                <div style="background: var(--surface-light, #f8f9fa); padding: 20px; border-radius: 8px;
                            border-left: 4px solid #dc3545;">
                    <h5 style="color: var(--text-primary, #333); margin-bottom: 15px;">
                        <?= ($index + 1) ?>. <?= htmlspecialchars($failed['question']) ?>
                    </h5>
                    
                    <div style="margin-bottom: 10px;">
                        <span style="color: #dc3545; font-weight: 500;">Ваш ответ:</span>
                        <span style="color: var(--text-secondary, #666);">
                            <?= htmlspecialchars($failed['your_answer'] ?? 'Не отвечено') ?>
                        </span>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <span style="color: #28a745; font-weight: 500;">Правильный ответ:</span>
                        <span style="color: var(--text-primary, #333);">
                            <?= htmlspecialchars($failed['correct_answer']) ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($failed['explanation'])): ?>
                    <div style="margin-top: 15px; padding: 15px; background: var(--info-bg, #e7f3ff); 
                                border-radius: 6px;">
                        <i class="fas fa-info-circle" style="color: #0066cc; margin-right: 8px;"></i>
                        <span style="color: var(--text-secondary, #666);">
                            <?= htmlspecialchars($failed['explanation']) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
        <?php if ($percentage === 100): ?>
        <div style="background: var(--surface, #ffffff); padding: 40px; border-radius: 12px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
            <i class="fas fa-trophy" style="font-size: 60px; color: #ffc107; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-primary, #333); margin-bottom: 10px;">Поздравляем!</h3>
            <p style="color: var(--text-secondary, #666); font-size: 18px;">
                Вы ответили правильно на все вопросы!
            </p>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
</div>

<style>
.result-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 16px;
    transition: all 0.3s;
}

.result-button.primary {
    background: #28a745;
    color: white;
}

.result-button.primary:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.result-button.secondary {
    background: #6c757d;
    color: white;
}

.result-button.secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

[data-theme="dark"] div[style*="background: var(--surface, #ffffff)"] {
    background: var(--surface-dark, #2d3748) !important;
}

[data-theme="dark"] div[style*="background: var(--surface-light, #f8f9fa)"] {
    background: var(--surface-darker, #1a202c) !important;
}

[data-theme="dark"] div[style*="background: var(--info-bg, #e7f3ff)"] {
    background: rgba(0, 102, 204, 0.2) !important;
}

[data-theme="dark"] h3,
[data-theme="dark"] h5 {
    color: var(--text-primary, #e4e6eb) !important;
}

[data-theme="dark"] .result-button {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Результат теста - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
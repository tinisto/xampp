<?php
// Simple single question test with original design
$test = $_GET['test'] ?? 'math-test';
$questionIndex = (int)($_GET['q'] ?? 0);

// Load questions
$testPath = $_SERVER['DOCUMENT_ROOT'] . "/pages/tests/{$test}/questions.php";
if (!file_exists($testPath)) {
    die('Test not found');
}

$questions = include $testPath;
if (!isset($questions[$questionIndex])) {
    die('Question not found');
}

$question = $questions[$questionIndex];
$totalQuestions = count($questions);

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
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
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
        
        /* Choice Styling - Original Hover Effects */
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
        
        /* Original Hover Effects */
        .choice:hover {
            background: var(--background, #ffffff);
            border-color: var(--primary-color, #28a745);
            transform: translateX(10px);
            box-shadow: var(--shadow-md, 0 4px 6px rgba(0,0,0,0.1));
        }
        
        .choice input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.3);
            accent-color: #4facfe;
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
        
        /* Answer feedback colors */
        .choice.correct {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.3), rgba(34, 139, 34, 0.2)) !important;
            border-color: #28a745 !important;
            transform: translateX(10px) !important;
        }
        
        .choice.correct span {
            color: #28a745 !important;
            font-weight: 700 !important;
        }
        
        .choice.incorrect {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.3), rgba(178, 34, 34, 0.2)) !important;
            border-color: #dc3545 !important;
            transform: translateX(10px) !important;
        }
        
        .choice.incorrect span {
            color: #dc3545 !important;
            font-weight: 700 !important;
        }
        
        /* Next Button Container */
        .next-btn-container {
            margin-top: 20px;
            text-align: center;
        }
        
        /* Next Button */
        .next-btn {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            border: 2px solid white;
            color: white;
            padding: 18px 45px;
            border-radius: 35px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .next-btn:hover:not(:disabled) {
            background: white;
            color: #667eea;
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }
        
        .next-btn:disabled {
            opacity: 0;
            cursor: not-allowed;
            transform: none;
            pointer-events: none;
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
        
        /* Feedback Section */
        .feedback-section {
            margin-top: 15px;
            padding: 0;
            text-align: center;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
        
        .feedback-section.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .feedback-correct {
            background: transparent;
            border: none;
            color: #28a745;
        }
        
        .feedback-incorrect {
            background: transparent;
            border: none;
            color: #dc3545;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .feedback-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .feedback-explanation {
            font-size: 16px;
            line-height: 1.4;
            margin-bottom: 15px;
            color: white;
        }
        
        .feedback-correct-answer {
            font-size: 16px;
            font-weight: 600;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        /* Feedback Next Button */
        .feedback-next-btn {
            background: var(--primary-color, #28a745);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .feedback-next-btn:hover {
            background: var(--primary-hover, #218838);
            transform: scale(1.05);
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
            .choice.selected,
            .choice.correct,
            .choice.incorrect {
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
    
    <!-- Toggle Mode Button - Sun/Moon -->
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
                        <input type="radio" name="answer" value="<?= htmlspecialchars($choice) ?>">
                        <span><?= htmlspecialchars($choice) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <!-- Feedback Section -->
            <div id="feedbackSection" class="feedback-section">
            </div>
            
            <div class="next-btn-container">
                <button type="submit" class="next-btn" id="nextBtn" disabled>
                    <?php if ($questionIndex + 1 < $totalQuestions): ?>
                        <i class="fas fa-arrow-right"></i> Следующий вопрос
                    <?php else: ?>
                        <i class="fas fa-check-circle"></i> Завершить тест
                    <?php endif; ?>
                </button>
            </div>
        </form>
    </div>
    
    
    <script>
        const choices = document.querySelectorAll('.choice');
        const nextBtn = document.getElementById('nextBtn');
        
        // Clear session storage if starting from the beginning
        <?php if ($questionIndex === 0): ?>
        sessionStorage.removeItem('testAnswers');
        <?php endif; ?>
        
        // Load previous answer if exists (but not if we just cleared it)
        const answers = JSON.parse(sessionStorage.getItem('testAnswers') || '[]');
        const currentAnswer = answers[<?= $questionIndex ?>];
        if (currentAnswer && <?= $questionIndex ?> > 0) {
            choices.forEach(choice => {
                const radio = choice.querySelector('input[type="radio"]');
                if (radio.value === currentAnswer) {
                    choice.classList.add('selected');
                    radio.checked = true;
                    nextBtn.disabled = false;
                    nextBtn.style.opacity = '1';
                }
            });
        }
        
        // Teacher Mode - Check answers immediately
        choices.forEach((choice, index) => {
            choice.addEventListener('click', function() {
                // Prevent multiple clicks
                if (this.classList.contains('answered')) return;
                
                // Mark all choices as answered
                choices.forEach(c => {
                    c.classList.add('answered');
                    c.style.pointerEvents = 'none';
                });
                
                // Remove selected class from all choices
                choices.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked choice
                this.classList.add('selected');
                
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                const userAnswer = radio.value;
                const correctAnswer = <?= json_encode($question['correct_answer']) ?>;
                const explanation = <?= json_encode($question['explanation']) ?>;
                
                // Add visual feedback - green for correct, red for incorrect
                // Convert to string for comparison since radio values are always strings
                const isCorrect = userAnswer === String(correctAnswer);
                if (isCorrect) {
                    this.classList.add('correct');
                } else {
                    this.classList.add('incorrect');
                    // Also highlight the correct answer in green
                    choices.forEach(choice => {
                        const choiceRadio = choice.querySelector('input[type="radio"]');
                        if (choiceRadio.value === String(correctAnswer)) {
                            choice.classList.add('correct');
                        }
                    });
                }
                
                // Teacher feedback
                showFeedback(isCorrect, correctAnswer, explanation, userAnswer);
                
                // Add selection animation
                this.style.transform = 'translateX(15px) scale(1.02)';
                setTimeout(() => {
                    this.style.transform = 'translateX(10px)';
                }, 150);
            });
        });
        
        function showFeedback(isCorrect, correctAnswer, explanation, userAnswer) {
            const feedbackSection = document.getElementById('feedbackSection');
            
            if (isCorrect) {
                // Correct Answer - Show success and auto-advance
                feedbackSection.className = 'feedback-section feedback-correct show';
                feedbackSection.innerHTML = `
                    <div class="feedback-title">
                        <i class="fas fa-check-circle"></i> Правильно!
                    </div>
                    Переходим к следующему вопросу
                `;
                
                // Store answer and auto-advance after 1 second
                const answers = JSON.parse(sessionStorage.getItem('testAnswers') || '[]');
                answers[<?= $questionIndex ?>] = userAnswer;
                sessionStorage.setItem('testAnswers', JSON.stringify(answers));
                
                setTimeout(() => {
                    <?php if ($questionIndex + 1 < $totalQuestions): ?>
                        window.location.href = '?test=<?= $test ?>&q=<?= $questionIndex + 1 ?>';
                    <?php else: ?>
                        finishTest();
                    <?php endif; ?>
                }, 1000);
                
            } else {
                // Incorrect Answer - Show explanation first, then Next button
                feedbackSection.className = 'feedback-section feedback-incorrect show';
                feedbackSection.innerHTML = `
                    <div class="feedback-title">
                        <i class="fas fa-times-circle"></i> Неправильно
                    </div>
                    <div class="feedback-correct-answer">
                        <i class="fas fa-lightbulb"></i> Правильный ответ: ${correctAnswer}
                    </div>
                    ${explanation}
                    <div class="feedback-next-btn-wrapper" style="margin-top: 20px;">
                        <button type="submit" class="feedback-next-btn" onclick="document.getElementById('questionForm').requestSubmit()">
                            <i class="fas fa-arrow-right"></i> Следующий вопрос
                        </button>
                    </div>
                `;
                
                // Hide the main next button since we have one in the feedback now
                nextBtn.style.display = 'none';
            }
        }
        
        // Keyboard navigation (original functionality)
        document.addEventListener('keydown', function(e) {
            // Numbers 1-4 for answers
            if (e.key >= '1' && e.key <= '4') {
                const choiceIndex = parseInt(e.key) - 1;
                if (choices[choiceIndex]) {
                    choices[choiceIndex].click();
                }
            }
            
            // Enter or Space to submit
            if ((e.key === 'Enter' || e.key === ' ') && !nextBtn.disabled) {
                e.preventDefault();
                nextBtn.click();
            }
            
            // Escape to go back or close
            if (e.key === 'Escape') {
                if (<?= $questionIndex ?> > 0) {
                    window.location.href = '?test=<?= $test ?>&q=<?= $questionIndex - 1 ?>';
                } else {
                    window.location.href = '/tests';
                }
            }
        });
        
        // Function to finish test and submit results
        function finishTest() {
            const answers = JSON.parse(sessionStorage.getItem('testAnswers') || '[]');
            
            // Submit all answers to improved test handler
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '/pages/tests/test-improved.php?test=<?= $test ?>';
            
            // Convert session storage answers to form format
            const finalAnswers = {};
            for (let i = 0; i < <?= count($questions) ?>; i++) {
                if (answers[i]) {
                    finalAnswers[i] = answers[i];
                }
            }
            
            // Create hidden inputs for each answer
            Object.keys(finalAnswers).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `answers[${key}]`;
                input.value = finalAnswers[key];
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
        
        // Form submission (for wrong answers only)
        document.getElementById('questionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) return;
            
            // Store answer in session storage
            const answers = JSON.parse(sessionStorage.getItem('testAnswers') || '[]');
            answers[<?= $questionIndex ?>] = selectedAnswer.value;
            sessionStorage.setItem('testAnswers', JSON.stringify(answers));
            
            <?php if ($questionIndex + 1 < $totalQuestions): ?>
                // Go to next question
                window.location.href = '?test=<?= $test ?>&q=<?= $questionIndex + 1 ?>';
            <?php else: ?>
                finishTest();
            <?php endif; ?>
        });
        
        // Toggle theme function
        function toggleTheme() {
            console.log('Theme toggle button clicked');
            
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = body.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                // Switch to light theme
                body.setAttribute('data-theme', 'light');
                themeIcon.className = 'fas fa-sun';
                console.log('Switched to light theme');
            } else {
                // Switch to dark theme
                body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-moon';
                console.log('Switched to dark theme');
            }
            
            // Save theme preference
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
            
            console.log('Loaded theme:', savedTheme);
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
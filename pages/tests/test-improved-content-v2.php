<?php
// Extract test configuration  
$test = $_GET['test'] ?? 'iq-test';
$testConfig = $testsConfig[$test] ?? $testsConfig['iq-test'];
$questions = $testConfig['questions'] ?? [];
$questionIndex = $_SESSION['question_index'] ?? 0;
$currentQuestion = $questions[$questionIndex] ?? null;
$totalQuestions = count($questions);
$progress = (($questionIndex + 1) / $totalQuestions) * 100;

// Loading placeholder for test pages
function renderTestPlaceholder() {
    ?>
    <style>
        .test-placeholder {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
        
        .test-placeholder-header {
            background: #e9ecef;
            padding: 30px;
            text-align: center;
        }
        
        .test-placeholder-icon {
            width: 48px;
            height: 48px;
            background: #dee2e6;
            border-radius: 50%;
            margin: 0 auto 15px;
        }
        
        .test-placeholder-title {
            width: 200px;
            height: 28px;
            background: #dee2e6;
            border-radius: 4px;
            margin: 0 auto 10px;
        }
        
        .test-placeholder-progress {
            width: 100%;
            height: 8px;
            background: #dee2e6;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .test-placeholder-body {
            padding: 30px;
        }
        
        .test-placeholder-question {
            width: 80%;
            height: 24px;
            background: #e9ecef;
            border-radius: 4px;
            margin: 0 auto 30px;
        }
        
        .test-placeholder-option {
            width: 100%;
            height: 60px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        
        .test-placeholder-button {
            width: 150px;
            height: 40px;
            background: #e9ecef;
            border-radius: 25px;
            margin: 20px auto 0;
        }
        
        @media (max-width: 768px) {
            .test-placeholder {
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
    
    <div class="test-placeholder">
        <div class="test-placeholder-header">
            <div class="test-placeholder-icon"></div>
            <div class="test-placeholder-title"></div>
            <div class="test-placeholder-progress"></div>
        </div>
        <div class="test-placeholder-body">
            <div class="test-placeholder-question"></div>
            <div class="test-placeholder-option"></div>
            <div class="test-placeholder-option"></div>
            <div class="test-placeholder-option"></div>
            <div class="test-placeholder-option"></div>
            <div class="test-placeholder-button"></div>
        </div>
    </div>
    <?php
}
?>

<div class="main-content" id="test-content" style="display: none;">
    <div class="container">
        <?php if ($currentQuestion): ?>
            <div class="timer" id="timer">
                <i class="fas fa-clock me-2"></i>
                <span id="time-display">00:00</span>
            </div>
            
            <div class="test-container">
                <div class="test-header">
                    <i class="fas fa-<?= $testConfig['icon'] ?> test-icon"></i>
                    <h1 class="test-title"><?= htmlspecialchars($testConfig['title']) ?></h1>
                    <div class="question-number">
                        Вопрос <?= $questionIndex + 1 ?> из <?= $totalQuestions ?>
                    </div>
                    <div class="test-progress">
                        <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
                
                <div class="test-body">
                    <h2 class="question-text"><?= htmlspecialchars($currentQuestion['question']) ?></h2>
                    
                    <form method="POST" id="test-form">
                        <input type="hidden" name="time_spent" id="time_spent" value="0">
                        
                        <?php foreach ($currentQuestion['choices'] as $index => $choice): ?>
                            <label class="answer-option" for="choice-<?= $index ?>">
                                <input type="radio" name="answer" value="<?= htmlspecialchars($choice) ?>" id="choice-<?= $index ?>">
                                <p class="answer-text"><?= htmlspecialchars($choice) ?></p>
                            </label>
                        <?php endforeach; ?>
                        
                        <div class="test-controls">
                            <button type="button" class="btn-leave" onclick="leaveTest()">
                                <i class="fas fa-times me-2"></i>
                                Покинуть тест
                            </button>
                            <button type="submit" class="btn-next" id="next-btn">
                                <?= $questionIndex + 1 < $totalQuestions ? 'Следующий вопрос' : 'Завершить тест' ?>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="test-container">
                <div class="test-body text-center">
                    <h2>Тест завершен!</h2>
                    <p>Все вопросы пройдены.</p>
                    <a href="/test/<?= $test ?>?reset=true" class="btn-next">Пройти снова</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Loading Placeholder -->
<div id="test-loading" class="main-content">
    <div class="container">
        <?php renderTestPlaceholder(); ?>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    /* Hide header and footer when test is active */
    body.test-active header,
    body.test-active footer {
        display: none !important;
    }
    
    .main-content {
        flex: 1;
        display: flex;
        align-items: flex-start;
        padding: 20px 0;
        justify-content: center;
    }
    .test-container {
        background: var(--card-bg, white);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 800px;
        margin: 0 auto;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    .test-header {
        background: <?= $testConfig['color'] ?>;
        color: white;
        padding: 30px;
        text-align: center;
    }
    .test-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }
    .test-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .test-progress {
        background: rgba(255,255,255,0.2);
        height: 8px;
        border-radius: 4px;
        margin-top: 20px;
        overflow: hidden;
    }
    .progress-bar {
        background: white;
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    .test-body {
        padding: 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .question-number {
        text-align: center;
        margin-bottom: 30px;
        color: white;
        font-size: 16px;
    }
    .question-text {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 30px;
        color: var(--text-primary, #333);
        text-align: center;
        line-height: 1.4;
    }
    .answer-option {
        background: var(--bg-secondary, #f8f9fa);
        border: 2px solid var(--border-color, #e9ecef);
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
        color: var(--text-primary, #333);
    }
    .test-controls {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: auto;
        padding-top: 20px;
        flex-shrink: 0;
    }
    .btn-next {
        background: <?= $testConfig['color'] ?>;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 25px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        opacity: 0.5;
        pointer-events: none;
    }
    
    .btn-leave {
        background: transparent;
        color: #dc3545;
        border: 2px solid #dc3545;
        padding: 10px 24px;
        border-radius: 25px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-leave:hover {
        background: #dc3545;
        color: white;
        transform: scale(1.05);
    }
    
    .btn-next.enabled {
        opacity: 1;
        pointer-events: auto;
    }
    .btn-next:hover.enabled {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    .timer {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--card-bg, white);
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        font-weight: 600;
        color: var(--text-primary, #333);
        z-index: 100;
    }
    .timer.warning {
        background: #ffeaa7;
        color: #d63031;
    }
    
    /* Dark mode adjustments */
    [data-bs-theme="dark"] body {
        background: linear-gradient(135deg, #4c5a96 0%, #534263 100%);
    }
    
    /* Fix dark mode double card issue */
    [data-bs-theme="dark"] .test-container {
        box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important;
    }
    
    [data-bs-theme="dark"] .answer-option:hover {
        background: <?= $testConfig['color'] ?>30;
    }
    [data-bs-theme="dark"] .answer-option.selected {
        background: <?= $testConfig['color'] ?>40;
    }
    
    @media (max-width: 768px) {
        .main-content {
            padding: 0;
        }
        .test-container {
            max-height: calc(100vh - 120px);
            margin: 0;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        .test-header {
            padding: 15px;
        }
        .test-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .test-title {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .test-body {
            padding: 15px;
            overflow-y: auto;
            max-height: calc(100vh - 300px);
        }
        .question-text {
            font-size: 18px;
            margin-bottom: 15px;
        }
        .answer-option {
            padding: 10px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .answer-text {
            font-size: 14px;
        }
        .test-controls {
            padding-top: 10px;
            position: sticky;
            bottom: 0;
            background: var(--card-bg, white);
            margin: 0 -15px -15px;
            padding: 15px;
            border-top: 1px solid var(--border-color, #eee);
        }
        .btn-next, .btn-leave {
            padding: 8px 16px;
            font-size: 14px;
        }
        .timer {
            position: static;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
            padding: 10px 15px;
        }
        .question-number {
            font-size: 14px;
            margin-bottom: 15px;
        }
    }
</style>

<script>
    let questionStartTime = Date.now();
    let timerInterval;
    
    // Hide loading and show content after page loads
    window.addEventListener('DOMContentLoaded', function() {
        // Add test-active class to body to hide header/footer
        document.body.classList.add('test-active');
        
        // Show content after a short delay
        setTimeout(function() {
            document.getElementById('test-loading').style.display = 'none';
            document.getElementById('test-content').style.display = 'block';
        }, 500);
    });
    
    // Leave test function
    function leaveTest() {
        if (confirm('Вы уверены, что хотите покинуть тест? Ваш прогресс будет потерян.')) {
            // Remove test-active class to show header/footer again
            document.body.classList.remove('test-active');
            // Redirect to tests main page
            window.location.href = '/tests';
        }
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
    
    // Clean up when leaving page
    window.addEventListener('unload', function() {
        document.body.classList.remove('test-active');
    });
</script>
<?php
// Extract test configuration  
$test = $_GET['test'] ?? 'iq-test';
$testConfig = $testsConfig[$test] ?? $testsConfig['iq-test'];
$questions = $testConfig['questions'] ?? [];
$questionIndex = $_SESSION['question_index'] ?? 0;
$currentQuestion = $questions[$questionIndex] ?? null;
$totalQuestions = count($questions);
$progress = (($questionIndex + 1) / $totalQuestions) * 100;
?>

<div class="main-content">
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

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        padding: 20px;
        margin-bottom: 15px;
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
        font-size: 18px;
        font-weight: 500;
        margin: 0;
        color: var(--text-primary, #333);
    }
    .test-controls {
        text-align: center;
        margin-top: auto;
        padding-top: 20px;
        flex-shrink: 0;
    }
    .btn-next {
        background: <?= $testConfig['color'] ?>;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        opacity: 0.5;
        pointer-events: none;
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
            padding: 12px;
            margin-bottom: 10px;
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
        .btn-next {
            padding: 10px 20px;
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
</script>
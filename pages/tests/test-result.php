<?php
// Test result page
session_start();

if (!isset($_SESSION['test_results'])) {
    header("Location: /tests");
    exit();
}

$results = $_SESSION['test_results'];
$test = $_GET['test'] ?? $results['test_type'];

// Include IQ rating function if it exists
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getIQRating.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getIQRating.php';
}

// Get rating based on score
function getTestRating($score) {
    if ($score >= 90) return ['rating' => 'Отлично', 'color' => '#28a745'];
    if ($score >= 80) return ['rating' => 'Хорошо', 'color' => '#17a2b8'];
    if ($score >= 70) return ['rating' => 'Удовлетворительно', 'color' => '#ffc107'];
    if ($score >= 60) return ['rating' => 'Ниже среднего', 'color' => '#fd7e14'];
    return ['rating' => 'Неудовлетворительно', 'color' => '#dc3545'];
}

$rating = getTestRating($results['score']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат: <?= htmlspecialchars($results['test_title']) ?> - 11-классники</title>
    <link href="/css/unified-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .result-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .result-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 30px;
        }
        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            font-weight: bold;
            color: white;
        }
        .rating-text {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .score-details {
            color: #666;
            margin-bottom: 30px;
        }
        .incorrect-questions {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .question-review {
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        .question-review:last-child {
            margin-bottom: 0;
        }
        .review-question {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .review-answer {
            margin-bottom: 8px;
        }
        .user-answer {
            color: #dc3545;
        }
        .correct-answer {
            color: #28a745;
        }
        .explanation {
            color: #666;
            font-style: italic;
            margin-top: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #28a745;
            color: white;
        }
        .btn-primary:hover {
            background: #218838;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <?php 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/page-section-header.php';
    renderPageSectionHeader([
        'title' => 'Результат теста',
        'showSearch' => false
    ]);
    ?>
    
    <main style="padding: 40px 0;">
        <div class="result-container">
            <!-- Main Result -->
            <div class="result-card">
                <div class="score-circle" style="background: <?= $rating['color'] ?>;">
                    <?= $results['score'] ?>%
                </div>
                <div class="rating-text" style="color: <?= $rating['color'] ?>;">
                    <?= $rating['rating'] ?>
                </div>
                <div class="score-details">
                    Правильных ответов: <?= $results['correct_answers'] ?> из <?= $results['total_questions'] ?>
                </div>
                <h2 style="color: #333; margin-bottom: 20px;">
                    <?= htmlspecialchars($results['test_title']) ?>
                </h2>
            </div>

            <!-- Incorrect Questions Review -->
            <?php if (!empty($results['incorrect_questions'])): ?>
                <div class="incorrect-questions">
                    <h3 style="color: #333; margin-bottom: 25px;">
                        <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                        Разбор ошибок (<?= count($results['incorrect_questions']) ?>)
                    </h3>
                    
                    <?php foreach ($results['incorrect_questions'] as $question): ?>
                        <div class="question-review">
                            <div class="review-question">
                                <?= htmlspecialchars($question['question']) ?>
                            </div>
                            <div class="review-answer">
                                <strong>Ваш ответ:</strong> 
                                <span class="user-answer"><?= htmlspecialchars($question['user_answer']) ?></span>
                            </div>
                            <div class="review-answer">
                                <strong>Правильный ответ:</strong> 
                                <span class="correct-answer"><?= htmlspecialchars($question['correct_answer']) ?></span>
                            </div>
                            <?php if (!empty($question['explanation'])): ?>
                                <div class="explanation">
                                    <?= htmlspecialchars($question['explanation']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="/test/<?= htmlspecialchars($test) ?>" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Пройти еще раз
                </a>
                <a href="/tests" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Другие тесты
                </a>
            </div>
        </div>
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer-unified.php'; ?>
</body>
</html>

<?php
// Clear the session data after displaying results
unset($_SESSION['test_results']);
?>
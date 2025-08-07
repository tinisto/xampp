<?php
// Single test page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get test URL from parameter
$testUrl = $_GET['url_test'] ?? '';
if (empty($testUrl)) {
    header("Location: /tests");
    exit();
}

// Get test data
$query = "SELECT t.*, tc.title_category, tc.url_category,
                 (SELECT COUNT(*) FROM test_results WHERE test_id = t.id_test) as attempts_count,
                 (SELECT AVG(score) FROM test_results WHERE test_id = t.id_test) as avg_score
          FROM tests t
          LEFT JOIN test_categories tc ON t.category_id = tc.id_category
          WHERE t.url_test = ? AND t.status = 'active'";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $testUrl);
$stmt->execute();
$result = $stmt->get_result();
$test = $result->fetch_assoc();

if (!$test) {
    header("Location: /404");
    exit();
}

// Get test questions
$questionsQuery = "SELECT * FROM test_questions WHERE test_id = ? ORDER BY question_order";
$questionsStmt = $connection->prepare($questionsQuery);
$questionsStmt->bind_param("i", $test['id_test']);
$questionsStmt->execute();
$questionsResult = $questionsStmt->get_result();
$questions = [];
while ($row = $questionsResult->fetch_assoc()) {
    $questions[] = $row;
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($test['title_test'], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
$breadcrumbItems = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'Тесты', 'url' => '/tests']
];
if ($test['title_category']) {
    $breadcrumbItems[] = ['text' => $test['title_category'], 'url' => '/tests/category/' . $test['url_category']];
}
$breadcrumbItems[] = ['text' => $test['title_test']];
renderBreadcrumb($breadcrumbItems);
$greyContent2 = ob_get_clean();

// Section 3: Test metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
        <div>
            <i class="fas fa-question-circle" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= count($questions) ?> вопросов</span>
        </div>
        <div>
            <i class="fas fa-clock" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $test['time_limit'] ? $test['time_limit'] . ' минут' : 'Без ограничения времени' ?></span>
        </div>
        <div>
            <i class="fas fa-chart-line" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;">Пройден <?= number_format($test['attempts_count'] ?? 0) ?> раз</span>
        </div>
        <?php if ($test['avg_score']): ?>
            <div>
                <i class="fas fa-star" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;">Средний балл: <?= number_format($test['avg_score'], 1) ?>%</span>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Test description and start button
ob_start();
?>
<div style="padding: 20px;">
    <?php if ($test['description_test']): ?>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="color: #333; margin: 0 0 15px 0;">Описание теста</h3>
            <p style="color: #666; line-height: 1.8; margin: 0;">
                <?= nl2br(htmlspecialchars($test['description_test'])) ?>
            </p>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin: 30px 0;">
        <button onclick="startTest()" style="background: #28a745; color: white; border: none; padding: 15px 40px; font-size: 18px; border-radius: 5px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-play" style="margin-right: 10px;"></i>
            Начать тест
        </button>
    </div>
    
    <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; color: #856404;">
        <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
        <strong>Внимание:</strong> После начала теста вы не сможете вернуться к предыдущим вопросам. Убедитесь, что у вас достаточно времени для прохождения теста.
    </div>
</div>

<script>
function startTest() {
    if (confirm('Вы готовы начать тест?')) {
        document.getElementById('test-intro').style.display = 'none';
        document.getElementById('test-questions').style.display = 'block';
        if (<?= $test['time_limit'] ? 'true' : 'false' ?>) {
            startTimer(<?= $test['time_limit'] ?>);
        }
    }
}

function startTimer(minutes) {
    let timeLeft = minutes * 60;
    const timerElement = document.getElementById('timer');
    
    const interval = setInterval(() => {
        const mins = Math.floor(timeLeft / 60);
        const secs = timeLeft % 60;
        timerElement.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 60) {
            timerElement.style.color = '#dc3545';
        }
        
        if (timeLeft <= 0) {
            clearInterval(interval);
            submitTest();
        }
        timeLeft--;
    }, 1000);
}
</script>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Test questions (hidden initially)
ob_start();
?>
<div id="test-intro" style="padding: 0 20px;">
    <!-- Content from section 4 will be shown here initially -->
</div>

<div id="test-questions" style="display: none; padding: 0 20px 30px 20px;">
    <?php if ($test['time_limit']): ?>
        <div style="position: sticky; top: 0; background: white; padding: 15px; border-bottom: 2px solid #eee; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Прохождение теста</h3>
            <div style="font-size: 24px; font-weight: bold; color: #28a745;">
                <i class="fas fa-clock"></i>
                <span id="timer"><?= $test['time_limit'] ?>:00</span>
            </div>
        </div>
    <?php endif; ?>
    
    <form id="testForm" onsubmit="submitTest(event)">
        <?php foreach ($questions as $index => $question): ?>
            <div style="background: white; padding: 20px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="color: #333; margin: 0 0 15px 0;">
                    Вопрос <?= $index + 1 ?> из <?= count($questions) ?>
                </h4>
                <p style="font-size: 18px; color: #333; margin: 0 0 20px 0;">
                    <?= htmlspecialchars($question['question_text']) ?>
                </p>
                
                <?php
                // Get answer options
                $answers = json_decode($question['answer_options'], true) ?: [];
                foreach ($answers as $ansIndex => $answer):
                ?>
                    <label style="display: block; padding: 10px; margin-bottom: 10px; cursor: pointer; border: 1px solid #ddd; border-radius: 5px;">
                        <input type="radio" name="question_<?= $question['id_question'] ?>" value="<?= $ansIndex ?>" required style="margin-right: 10px;">
                        <?= htmlspecialchars($answer) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        
        <div style="text-align: center; margin: 30px 0;">
            <button type="submit" style="background: #28a745; color: white; border: none; padding: 15px 40px; font-size: 18px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                <i class="fas fa-check" style="margin-right: 10px;"></i>
                Завершить тест
            </button>
        </div>
    </form>
</div>

<div id="test-results" style="display: none; padding: 20px;">
    <!-- Results will be shown here after submission -->
</div>

<script>
function submitTest(event) {
    if (event) event.preventDefault();
    
    // Calculate score
    const form = document.getElementById('testForm');
    const formData = new FormData(form);
    
    // Show results
    document.getElementById('test-questions').style.display = 'none';
    document.getElementById('test-results').innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-check-circle" style="font-size: 60px; color: #28a745; margin-bottom: 20px;"></i>
            <h2 style="color: #333; margin-bottom: 20px;">Тест завершен!</h2>
            <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
                Ваши результаты обрабатываются...
            </p>
            <a href="/tests" style="background: #28a745; color: white; text-decoration: none; padding: 12px 30px; border-radius: 5px; display: inline-block;">
                Вернуться к тестам
            </a>
        </div>
    `;
    document.getElementById('test-results').style.display = 'block';
}
</script>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related tests
ob_start();
// Get related tests from same category
if ($test['category_id']) {
    $relatedQuery = "SELECT id_test, title_test, url_test, description_test
                     FROM tests 
                     WHERE category_id = ? AND id_test != ? AND status = 'active'
                     ORDER BY RAND() 
                     LIMIT 4";
    $stmt = $connection->prepare($relatedQuery);
    $stmt->bind_param("ii", $test['category_id'], $test['id_test']);
    $stmt->execute();
    $relatedResult = $stmt->get_result();
    $relatedTests = [];
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedTests[] = [
            'id_news' => $row['id_test'],
            'title_news' => $row['title_test'],
            'url_news' => $row['url_test'],
            'image_news' => '/images/default-test.jpg',
            'created_at' => date('Y-m-d'),
            'category_title' => 'Тест',
            'category_url' => '#'
        ];
    }

    if (count($relatedTests) > 0) {
        echo '<div style="padding: 20px;">';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle('Похожие тесты', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
        
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
        renderCardsGrid($relatedTests, 'test', [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => true
        ]);
        echo '</div>';
    }
}
$greyContent6 = ob_get_clean();

// Section 7: Comments (prepared but not implemented per user request)
ob_start();
?>
<div style="padding: 30px 20px; color: white;">
    <h3 style="margin: 0 0 20px 0;">Обсуждение теста</h3>
    <!-- Comments will be added later per user request -->
</div>
<?php
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $test['title_test'];
$metaD = $test['description_test'] ? substr($test['description_test'], 0, 160) : 'Тест: ' . $test['title_test'];
$metaK = $test['title_test'] . ', тест, проверка знаний, ' . $test['title_category'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
/**
 * Single Test Page - Real Template Version
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get test URL from request
$testUrl = $_GET['url_test'] ?? '';
if (empty($testUrl)) {
    // Extract from REQUEST_URI
    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/\/test\/([^\/]+)/', $uri, $matches)) {
        $testUrl = $matches[1];
    }
}

// Try to find the test
$testData = null;
$testExists = false;

if (!empty($testUrl)) {
    // Demo tests data - matches what we show in listing
    $demoTests = [
        'ege-math-test' => [
            'id_test' => 1,
            'title_test' => 'Тест по математике ЕГЭ',
            'url_test' => 'ege-math-test',
            'difficulty' => 'Сложный',
            'duration' => 30,
            'questions_count' => 20,
            'category_title' => 'Математика',
            'category_url' => 'math',
            'description' => 'Комплексный тест для подготовки к ЕГЭ по математике профильного уровня.',
            'questions' => [
                ['question' => 'Найдите значение выражения: 2x + 3 при x = 5', 'options' => ['10', '13', '15', '16'], 'correct' => 1],
                ['question' => 'Решите уравнение: x² - 4 = 0', 'options' => ['x = ±2', 'x = 2', 'x = 4', 'нет решений'], 'correct' => 0],
                ['question' => 'Найдите производную функции f(x) = x³', 'options' => ['3x²', 'x²', '3x', 'x³'], 'correct' => 0]
            ]
        ],
        'russian-language-test' => [
            'id_test' => 2,
            'title_test' => 'Тест по русскому языку',
            'url_test' => 'russian-language-test',
            'difficulty' => 'Средний',
            'duration' => 45,
            'questions_count' => 25,
            'category_title' => 'Русский язык',
            'category_url' => 'russian',
            'description' => 'Тест на знание правил русского языка, орфографии и пунктуации.',
            'questions' => [
                ['question' => 'В каком слове пишется НН?', 'options' => ['деревя_ый', 'стекля_ый', 'серебря_ый', 'глиня_ый'], 'correct' => 0],
                ['question' => 'Укажите правильное написание', 'options' => ['по-моему', 'по моему', 'помоему', 'по-моиму'], 'correct' => 0],
                ['question' => 'Найдите ошибку в предложении', 'options' => ['Нет ошибок', 'Согласно приказа', 'Благодаря помощи', 'Вопреки ожиданиям'], 'correct' => 1]
            ]
        ],
        'physics-test' => [
            'id_test' => 3,
            'title_test' => 'Тест по физике',
            'url_test' => 'physics-test',
            'difficulty' => 'Сложный',
            'duration' => 40,
            'questions_count' => 15,
            'category_title' => 'Физика',
            'category_url' => 'physics',
            'description' => 'Тест по основам физики: механика, термодинамика, электричество.',
            'questions' => [
                ['question' => 'Единица измерения силы в СИ', 'options' => ['Джоуль', 'Ньютон', 'Ватт', 'Паскаль'], 'correct' => 1],
                ['question' => 'Формула кинетической энергии', 'options' => ['mgh', 'mv²/2', 'Fs', 'Pt'], 'correct' => 1],
                ['question' => 'Скорость света в вакууме', 'options' => ['3×10⁸ м/с', '3×10⁶ м/с', '3×10¹⁰ м/с', '3×10⁴ м/с'], 'correct' => 0]
            ]
        ],
        'biology-test' => [
            'id_test' => 4,
            'title_test' => 'Тест по биологии',
            'url_test' => 'biology-test',
            'difficulty' => 'Легкий',
            'duration' => 35,
            'questions_count' => 18,
            'category_title' => 'Биология',
            'category_url' => 'biology',
            'description' => 'Тест по общей биологии: клетка, генетика, эволюция.',
            'questions' => [
                ['question' => 'Основная структурная единица живого', 'options' => ['Ткань', 'Орган', 'Клетка', 'Система органов'], 'correct' => 2],
                ['question' => 'Процесс фотосинтеза происходит в', 'options' => ['Митохондриях', 'Хлоропластах', 'Ядре', 'Цитоплазме'], 'correct' => 1],
                ['question' => 'Автор теории эволюции', 'options' => ['Мендель', 'Дарвин', 'Павлов', 'Сеченов'], 'correct' => 1]
            ]
        ],
        'chemistry-test' => [
            'id_test' => 5,
            'title_test' => 'Тест по химии',
            'url_test' => 'chemistry-test',
            'difficulty' => 'Средний',
            'duration' => 40,
            'questions_count' => 20,
            'category_title' => 'Химия',
            'category_url' => 'chemistry',
            'description' => 'Тест по основам химии: атомы, молекулы, реакции.',
            'questions' => [
                ['question' => 'Химический символ золота', 'options' => ['Go', 'Gd', 'Au', 'Ag'], 'correct' => 2],
                ['question' => 'Валентность кислорода', 'options' => ['I', 'II', 'III', 'IV'], 'correct' => 1],
                ['question' => 'Самый легкий газ', 'options' => ['Гелий', 'Водород', 'Азот', 'Кислород'], 'correct' => 1]
            ]
        ],
        'history-test' => [
            'id_test' => 6,
            'title_test' => 'Тест по истории',
            'url_test' => 'history-test',
            'difficulty' => 'Средний',
            'duration' => 50,
            'questions_count' => 30,
            'category_title' => 'История',
            'category_url' => 'history',
            'description' => 'Тест по истории России: от древности до современности.',
            'questions' => [
                ['question' => 'Год крещения Руси', 'options' => ['980', '988', '990', '1000'], 'correct' => 1],
                ['question' => 'Первый царь всея Руси', 'options' => ['Иван III', 'Иван IV', 'Василий III', 'Дмитрий Донской'], 'correct' => 1],
                ['question' => 'Куликовская битва состоялась в', 'options' => ['1380', '1480', '1240', '1242'], 'correct' => 0]
            ]
        ]
    ];
    
    // Check if this test exists in our demo data
    if (isset($demoTests[$testUrl])) {
        $testData = $demoTests[$testUrl];
        $testExists = true;
    }
}

// If test not found, redirect to tests listing
if (!$testData) {
    header('Location: /tests');
    exit;
}

// Section 1: Title + Breadcrumbs
ob_start();
// Breadcrumbs
echo '<nav style="padding: 15px 0; margin-bottom: 20px;">';
echo '<a href="/" style="color: #28a745; text-decoration: none;">Главная</a>';
echo ' → <a href="/tests" style="color: #28a745; text-decoration: none;">Тесты</a>';
if (!empty($testData['category_title'])) {
    echo ' → <a href="/tests/' . htmlspecialchars($testData['category_url']) . '" style="color: #28a745; text-decoration: none;">' . htmlspecialchars($testData['category_title']) . '</a>';
}
echo ' → <span style="color: #666;">' . htmlspecialchars($testData['title_test']) . '</span>';
echo '</nav>';

// Title
echo '<h1 style="font-size: 32px; color: #333; margin: 20px 0; line-height: 1.3;">' . htmlspecialchars($testData['title_test']) . '</h1>';
$greyContent1 = ob_get_clean();

// Section 2: Empty
$greyContent2 = '';

// Section 3: Test meta info
ob_start();
echo '<div style="display: flex; gap: 30px; align-items: center; padding: 20px 0; border-bottom: 1px solid #eee; margin-bottom: 20px; flex-wrap: wrap;">';
echo '<div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-clock" style="color: #28a745;"></i> <strong>' . $testData['duration'] . ' минут</strong></div>';
echo '<div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-question-circle" style="color: #28a745;"></i> <strong>' . $testData['questions_count'] . ' вопросов</strong></div>';
echo '<div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-chart-bar" style="color: #28a745;"></i> <strong>' . $testData['difficulty'] . '</strong></div>';
if (!empty($testData['category_title'])) {
    echo '<a href="/tests/' . htmlspecialchars($testData['category_url']) . '" style="background: #28a745; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">' . htmlspecialchars($testData['category_title']) . '</a>';
}
echo '</div>';
$greyContent3 = ob_get_clean();

// Section 4: Empty
$greyContent4 = '';

// Section 5: Test content
ob_start();
?>
<div style="max-width: 800px; margin: 0 auto;">
    <!-- Demo notice -->
    <div style="background: #e3f2fd; border: 1px solid #1976d2; border-radius: 8px; padding: 20px; margin-bottom: 30px; text-align: center;">
        <h4 style="color: #1976d2; margin-bottom: 15px;">Демо-режим</h4>
        <p style="margin: 0; color: #1976d2;">Это демонстрационная версия теста. Для полноценного функционала обратитесь к администратору.</p>
    </div>
    
    <!-- Test description -->
    <?php if (!empty($testData['description'])): ?>
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 25px; margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; color: #333;">Описание теста</h3>
        <p style="margin: 0; line-height: 1.6; color: #666;"><?= htmlspecialchars($testData['description']) ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Sample questions -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 30px;">
        <h3 style="margin-bottom: 25px; color: #333;">Примеры вопросов</h3>
        
        <?php if (!empty($testData['questions'])): ?>
        <?php foreach (array_slice($testData['questions'], 0, 3) as $index => $question): ?>
        <div style="margin-bottom: 25px; padding-bottom: 25px; <?= $index < 2 ? 'border-bottom: 1px solid #eee;' : '' ?>">
            <h4 style="margin-bottom: 15px; color: #333;">Вопрос <?= $index + 1 ?>:</h4>
            <p style="margin-bottom: 15px; font-size: 16px;"><?= htmlspecialchars($question['question']) ?></p>
            
            <div style="display: grid; gap: 10px;">
                <?php foreach ($question['options'] as $optIndex => $option): ?>
                <label style="display: flex; align-items: center; padding: 10px; background: #f8f9fa; border-radius: 4px; cursor: pointer;">
                    <input type="radio" name="demo_q<?= $index ?>" value="<?= $optIndex ?>" style="margin-right: 10px;">
                    <?= htmlspecialchars($option) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Start test button -->
        <div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee;">
            <button onclick="alert('Демо-версия. Для полного функционала нужна реализация системы тестирования.')" 
                    style="padding: 15px 40px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer;">
                Начать тест
            </button>
            <p style="margin: 15px 0 0 0; color: #666; font-size: 14px;">
                Нажмите кнопку, чтобы начать прохождение теста
            </p>
        </div>
    </div>
    
    <!-- Back to tests -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="/tests" style="color: #28a745; text-decoration: none; font-size: 16px;">
            ← Вернуться к списку тестов
        </a>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Section 7: Comments (empty for now)
$blueContent = '';

// Page title
$pageTitle = htmlspecialchars($testData['title_test']) . ' - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
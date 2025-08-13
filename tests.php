<?php
/**
 * Tests page - online tests and quizzes for students
 * Shows available tests and quizzes
 */

// Include database connection
require_once __DIR__ . '/database/db_connections.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Section 1: Title
ob_start();
include_once __DIR__ . '/common-components/real_title.php';
renderRealTitle('Тесты', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Онлайн тесты для подготовки к экзаменам и профориентации'
]);
$headerContent = ob_get_clean();

// Section 2: Test Categories Navigation
ob_start();
include_once __DIR__ . '/common-components/category-navigation.php';
$testNavItems = [
    ['title' => 'Все тесты', 'url' => '/tests'],
    ['title' => 'ЕГЭ', 'url' => '/tests?category=ege'],
    ['title' => 'ОГЭ', 'url' => '/tests?category=oge'],
    ['title' => 'Профориентация', 'url' => '/tests?category=career'],
    ['title' => 'Предметные', 'url' => '/tests?category=subject']
];

$currentCategory = $_GET['category'] ?? '';
$currentNavPath = '/tests' . ($currentCategory ? "?category=$currentCategory" : '');

renderCategoryNavigation($testNavItems, $currentNavPath);
$navigationContent = ob_get_clean();

// Section 3: Metadata
$metadataContent = '';

// Section 4: Empty
$filtersContent = '';

// Section 5: Tests Grid/List
ob_start();

// Sample test data (in a real application, this would come from database)
$availableTests = [
    [
        'id' => 1,
        'title' => 'ЕГЭ по математике (базовый уровень)',
        'description' => 'Тренировочный тест по математике базового уровня ЕГЭ',
        'category' => 'ege',
        'duration' => 180,
        'questions' => 20,
        'difficulty' => 'Базовый',
        'icon' => 'fas fa-calculator',
        'url' => '/test/1'
    ],
    [
        'id' => 2, 
        'title' => 'ЕГЭ по русскому языку',
        'description' => 'Подготовительный тест по русскому языку для ЕГЭ',
        'category' => 'ege',
        'duration' => 210,
        'questions' => 27,
        'difficulty' => 'Средний',
        'icon' => 'fas fa-language',
        'url' => '/test/2'
    ],
    [
        'id' => 3,
        'title' => 'Тест профориентации',
        'description' => 'Определите подходящую сферу деятельности и профессию',
        'category' => 'career',
        'duration' => 30,
        'questions' => 50,
        'difficulty' => 'Легкий',
        'icon' => 'fas fa-compass',
        'url' => '/test/3'
    ],
    [
        'id' => 4,
        'title' => 'ОГЭ по обществознанию',
        'description' => 'Тренировочный тест по обществознанию для 9 класса',
        'category' => 'oge', 
        'duration' => 180,
        'questions' => 24,
        'difficulty' => 'Средний',
        'icon' => 'fas fa-users',
        'url' => '/test/4'
    ],
    [
        'id' => 5,
        'title' => 'Физика 10-11 класс',
        'description' => 'Проверочный тест по физике для старших классов',
        'category' => 'subject',
        'duration' => 90,
        'questions' => 15,
        'difficulty' => 'Сложный',
        'icon' => 'fas fa-atom',
        'url' => '/test/5'
    ],
    [
        'id' => 6,
        'title' => 'Английский язык B1-B2',
        'description' => 'Тест на определение уровня английского языка',
        'category' => 'subject',
        'duration' => 60,
        'questions' => 30,
        'difficulty' => 'Средний',
        'icon' => 'fas fa-globe',
        'url' => '/test/6'
    ]
];

// Filter tests by category if specified
$filteredTests = $availableTests;
if ($currentCategory) {
    $filteredTests = array_filter($availableTests, function($test) use ($currentCategory) {
        return $test['category'] === $currentCategory;
    });
}

// Convert filtered tests to the format expected by renderCardsGrid
$testsForGrid = [];
foreach ($filteredTests as $test) {
    $testsForGrid[] = [
        'id' => $test['id'],
        'title' => $test['title'],
        'description' => $test['description'],
        'url' => $test['url'],
        'icon' => $test['icon'],
        'duration' => $test['duration'] . ' мин',
        'questions' => $test['questions'] . ' вопросов',
        'difficulty' => $test['difficulty']
    ];
}

if (count($testsForGrid) > 0) {
    include_once __DIR__ . '/common-components/cards-grid.php';
    renderCardsGrid($testsForGrid, 'test', [
        'showDescription' => true,
        'showViews' => false,
        'showDate' => false,
        'customFields' => ['duration', 'questions', 'difficulty'],
        'buttonText' => 'Начать тест',
        'buttonColor' => '#28a745'
    ]);
} else {
    ?>
    <div style="text-align: center; padding: 60px; color: #666;">
        <i class="fas fa-clipboard-list fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
        <h3 style="margin-bottom: 10px;">Тесты в разработке</h3>
        <p>В данной категории пока нет доступных тестов.</p>
        <p><a href="/tests" style="color: #28a745; text-decoration: none;">Посмотреть все тесты</a></p>
    </div>
    <?php
}

$mainContent = ob_get_clean();

// Section 6: Information about testing
ob_start();
?>
<div style="background: #f8f9fa; padding: 30px 20px; margin: 20px 0; border-radius: 12px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h3 style="color: #333; margin-bottom: 20px; text-align: center;">
            <i class="fas fa-info-circle" style="color: #28a745; margin-right: 10px;"></i>
            Как проходить тесты
        </h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center;">
            <div>
                <i class="fas fa-play-circle" style="font-size: 24px; color: #28a745; margin-bottom: 10px;"></i>
                <p style="margin: 0; font-weight: 500;">Выберите тест</p>
                <small style="color: #666;">Найдите подходящий тест по предмету или цели</small>
            </div>
            <div>
                <i class="fas fa-clock" style="font-size: 24px; color: #28a745; margin-bottom: 10px;"></i>
                <p style="margin: 0; font-weight: 500;">Следите за временем</p>
                <small style="color: #666;">У каждого теста есть ограничение по времени</small>
            </div>
            <div>
                <i class="fas fa-chart-line" style="font-size: 24px; color: #28a745; margin-bottom: 10px;"></i>
                <p style="margin: 0; font-weight: 500;">Получите результат</p>
                <small style="color: #666;">Узнайте свой балл и рекомендации</small>
            </div>
        </div>
    </div>
</div>
<?php
$paginationContent = ob_get_clean();

// Section 7: No comments for tests page
$commentsContent = '';

// Set page metadata
$pageTitle = 'Тесты - 11классники';
$metaD = 'Онлайн тесты для подготовки к ЕГЭ, ОГЭ и профориентации. Проверьте свои знания и подготовьтесь к экзаменам.';
$metaK = 'онлайн тесты, ЕГЭ, ОГЭ, профориентация, подготовка к экзаменам, проверка знаний';

// Comments configuration (compact format)
$commentsContent = [
    'type' => 'tests',
    'id' => 'page',
    'options' => [
        'showTitle' => false, // Don't show "Обсуждение" since it's clear from context
        'showStats' => true,
        'collapsed' => true
    ]
];

// Include unified template
include __DIR__ . '/template.php';
?>
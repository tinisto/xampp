<?php
/**
 * Tests Main Page - Real Template Version
 * Fixed to show tests or fallback content
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Онлайн тесты', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Проверьте свои знания'
]);
$greyContent1 = ob_get_clean();

// Section 2: Category Navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
$testNavItems = [
    ['title' => 'Все тесты', 'url' => '/tests'],
    ['title' => 'Математика', 'url' => '/tests/math'],
    ['title' => 'Русский язык', 'url' => '/tests/russian'],
    ['title' => 'Физика', 'url' => '/tests/physics'],
    ['title' => 'Химия', 'url' => '/tests/chemistry'],
    ['title' => 'Биология', 'url' => '/tests/biology']
];
renderCategoryNavigation($testNavItems, $_SERVER['REQUEST_URI']);
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Filters and Search
ob_start();
echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
renderFiltersDropdown([
    'sortOptions' => [
        'difficulty_easy' => 'Легкие тесты',
        'difficulty_medium' => 'Средние тесты', 
        'difficulty_hard' => 'Сложные тесты',
        'popular' => 'По популярности'
    ]
]);

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск тестов...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Tests Grid
ob_start();

$testsItems = [];

// Try multiple table names and query variations
$possibleTables = ['tests', 'test', 'quiz', 'quizzes'];
$foundTable = null;

foreach ($possibleTables as $tableName) {
    try {
        // Check if table exists
        $checkQuery = "SHOW TABLES LIKE '$tableName'";
        $checkResult = mysqli_query($connection, $checkQuery);
        
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $foundTable = $tableName;
            break;
        }
    } catch (Exception $e) {
        // Continue to next table
        continue;
    }
}

if ($foundTable) {
    try {
        // Get column names first to adapt the query
        $columnsQuery = "SHOW COLUMNS FROM $foundTable";
        $columnsResult = mysqli_query($connection, $columnsQuery);
        $columns = [];
        while ($col = mysqli_fetch_assoc($columnsResult)) {
            $columns[] = $col['Field'];
        }
        
        // Build query based on available columns
        $selectFields = [];
        $titleField = 'title';
        $urlField = 'url';
        $idField = 'id';
        
        // Map common field variations
        foreach ($columns as $col) {
            if (stripos($col, 'title') !== false && stripos($col, 'test') !== false) {
                $titleField = $col;
            }
            if (stripos($col, 'url') !== false && stripos($col, 'test') !== false) {
                $urlField = $col;
            }
            if (stripos($col, 'id') !== false && stripos($col, 'test') !== false) {
                $idField = $col;
            }
        }
        
        // Build basic query
        $query = "SELECT * FROM $foundTable LIMIT 12";
        
        // Add WHERE clause if status column exists
        if (in_array('status', $columns)) {
            $query = "SELECT * FROM $foundTable WHERE status = 'active' OR status = 'published' LIMIT 12";
        }
        
        $result = mysqli_query($connection, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Normalize the data structure
                $normalizedRow = [
                    'id_test' => $row[$idField] ?? $row['id'] ?? 1,
                    'title_test' => $row[$titleField] ?? $row['title'] ?? $row['name'] ?? 'Тест',
                    'url_test' => $row[$urlField] ?? $row['url'] ?? 'test-' . ($row['id'] ?? 1),
                    'image_test' => $row['image'] ?? $row['image_test'] ?? '/images/default-test.jpg',
                    'difficulty' => $row['difficulty'] ?? 'Средний',
                    'duration' => $row['duration'] ?? $row['time_limit'] ?? 30,
                    'questions_count' => $row['questions_count'] ?? $row['question_count'] ?? $row['total_questions'] ?? 10,
                    'category_title' => $row['category'] ?? $row['subject'] ?? 'Общие',
                    'category_url' => strtolower($row['category'] ?? $row['subject'] ?? 'general')
                ];
                $testsItems[] = $normalizedRow;
            }
        }
    } catch (Exception $e) {
        // Query failed, will show fallback
    }
}

// If no tests found, show sample tests
if (empty($testsItems)) {
    $testsItems = [
        [
            'id_test' => 1,
            'title_test' => 'Тест по математике ЕГЭ',
            'url_test' => 'ege-math-test',
            'image_test' => '/images/default-test.jpg',
            'difficulty' => 'Сложный',
            'duration' => 30,
            'questions_count' => 20,
            'category_title' => 'Математика',
            'category_url' => 'math'
        ],
        [
            'id_test' => 2,
            'title_test' => 'Тест по русскому языку',
            'url_test' => 'russian-language-test',
            'image_test' => '/images/default-test.jpg',
            'difficulty' => 'Средний',
            'duration' => 45,
            'questions_count' => 25,
            'category_title' => 'Русский язык',
            'category_url' => 'russian'
        ],
        [
            'id_test' => 3,
            'title_test' => 'Тест по физике',
            'url_test' => 'physics-test',
            'image_test' => '/images/default-test.jpg',
            'difficulty' => 'Сложный',
            'duration' => 40,
            'questions_count' => 15,
            'category_title' => 'Физика',
            'category_url' => 'physics'
        ],
        [
            'id_test' => 4,
            'title_test' => 'Тест по биологии',
            'url_test' => 'biology-test',
            'image_test' => '/images/default-test.jpg',
            'difficulty' => 'Легкий',
            'duration' => 35,
            'questions_count' => 18,
            'category_title' => 'Биология',
            'category_url' => 'biology'
        ],
        [
            'id_test' => 5,
            'title_test' => 'Тест по химии',
            'url_test' => 'chemistry-test',
            'image_test' => '/images/default-test.jpg',
            'difficulty' => 'Средний',
            'duration' => 40,
            'questions_count' => 20,
            'category_title' => 'Химия',
            'category_url' => 'chemistry'
        ],
        [
            'id_test' => 6,
            'title_test' => 'Тест по истории',
            'url_test' => 'history-test',
            'image_test' => '/images/default-test.jpg',
            'difficulty' => 'Средний',
            'duration' => 50,
            'questions_count' => 30,
            'category_title' => 'История',
            'category_url' => 'history'
        ]
    ];
    
    // Add notice about sample tests
    echo '<div style="background: #e3f2fd; border: 1px solid #1976d2; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: center;">';
    echo '<p style="margin: 0; color: #1976d2;"><strong>Демо-режим:</strong> Показаны образцы тестов. Для добавления реальных тестов обратитесь к администратору.</p>';
    echo '</div>';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
renderCardsGrid($testsItems, 'test', [
    'columns' => 3,
    'gap' => 20,
    'showBadge' => true
]);

$greyContent5 = ob_get_clean();

// Section 6: Pagination (if needed)
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Set page title
$pageTitle = 'Онлайн тесты - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
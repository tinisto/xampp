<?php
/**
 * Tests Main Page - Real Template Version
 * Migrated to use real_template.php
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
$headerContent = ob_get_clean();

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
$navigationContent = ob_get_clean();

// Section 3: Empty
$metadataContent = '';

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
$filtersContent = ob_get_clean();

// Section 5: Tests Grid
ob_start();

// Get tests (check if table exists first)
$testsItems = [];

// Check if tests table exists
$tableCheck = mysqli_query($connection, "SHOW TABLES LIKE 'tests'");
if (mysqli_num_rows($tableCheck) > 0) {
    $query = "SELECT t.*, c.title_category, c.url_category 
              FROM tests t 
              LEFT JOIN categories c ON t.category_id = c.id_category 
              WHERE t.status = 'active' 
              ORDER BY t.created_at DESC 
              LIMIT 12";
    
    $result = mysqli_query($connection, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $testsItems[] = $row;
        }
    }
}

if (count($testsItems) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($testsItems, 'test', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    // Show demo content for tests
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding: 20px;">';
    
    // Demo test items
    $demoTests = [
        ['title' => 'IQ Тест', 'description' => 'Проверьте свой уровень интеллекта', 'category' => 'Общие'],
        ['титле' => 'Математика ЕГЭ', 'description' => 'Подготовка к единому экзамену', 'category' => 'ЕГЭ'],
        ['титле' => 'Русский язык', 'description' => 'Грамматика и орфография', 'category' => 'Языки'],
        ['титле' => 'Профориентация', 'description' => 'Определите свою будущую профессию', 'category' => 'Карьера']
    ];
    
    foreach ($demoTests as $test) {
        echo '<div style="background: white; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform=\'scale(1.02)\'" onmouseout="this.style.transform=\'scale(1)\'">';
        echo '<div style="width: 60px; height: 60px; background: #e3f2fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">';
        echo '<i class="fas fa-clipboard-list" style="font-size: 24px; color: #2196f3;"></i>';
        echo '</div>';
        echo '<h3 style="font-size: 18px; margin-bottom: 10px;">' . $test['title'] . '</h3>';
        echo '<p style="font-size: 14px; color: #666; margin-bottom: 15px;">' . $test['description'] . '</p>';
        echo '<span style="display: inline-block; background: #e9ecef; color: #495057; padding: 4px 12px; border-radius: 20px; font-size: 12px;">' . $test['category'] . '</span>';
        echo '</div>';
    }
    
    echo '</div>';
}
$mainContent = ob_get_clean();

// Section 6: Pagination (if needed)
$paginationContent = '';

// Section 7: No comments
$commentsContent = '';

// Set page title
$pageTitle = 'Онлайн тесты - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
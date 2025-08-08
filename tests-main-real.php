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

// Get tests
$query = "SELECT t.*, c.title_category, c.url_category 
          FROM tests t 
          LEFT JOIN categories c ON t.category_id = c.id_category 
          WHERE t.status = 'active' 
          ORDER BY t.created_at DESC 
          LIMIT 12";

$result = mysqli_query($connection, $query);
$testsItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $testsItems[] = $row;
}

if (count($testsItems) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($testsItems, 'test', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-clipboard-list fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>Тесты не найдены</p>
          </div>';
}
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
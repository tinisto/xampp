<?php
// Tests listing page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Онлайн тесты', [
    'fontSize' => '36px',
    'margin' => '30px 0',
    'subtitle' => 'Проверьте свои знания по различным предметам'
]);
$greyContent1 = ob_get_clean();

// Section 2: Category Navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
$testCategories = [
    ['title' => 'Все тесты', 'url' => '/tests'],
    ['title' => 'ЕГЭ', 'url' => '/tests?category=ege'],
    ['title' => 'ОГЭ', 'url' => '/tests?category=oge'],
    ['title' => 'Олимпиады', 'url' => '/tests?category=olympiad'],
    ['title' => 'Профориентация', 'url' => '/tests?category=career']
];
renderCategoryNavigation($testCategories, $_SERVER['REQUEST_URI']);
$greyContent2 = ob_get_clean();

// Section 3: Statistics
ob_start();
?>
<div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; padding: 20px;">
    <?php
    // Get statistics
    $totalTests = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM tests WHERE status = 'active'"))['c'];
    $totalQuestions = mysqli_fetch_assoc(mysqli_query($connection, "SELECT SUM(questions_count) as c FROM tests WHERE status = 'active'"))['c'] ?: 0;
    $totalAttempts = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM test_attempts"))['c'];
    ?>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;"><?= number_format($totalTests) ?></div>
        <div style="font-size: 16px; color: #666;">Тестов</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;"><?= number_format($totalQuestions) ?></div>
        <div style="font-size: 16px; color: #666;">Вопросов</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;"><?= number_format($totalAttempts) ?></div>
        <div style="font-size: 16px; color: #666;">Попыток</div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Filters and Search
ob_start();
echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
renderFiltersDropdown([
    'sortOptions' => [
        'popular' => 'По популярности',
        'newest' => 'Новые',
        'difficulty_easy' => 'Сначала легкие',
        'difficulty_hard' => 'Сначала сложные'
    ]
]);

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск теста...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Tests Grid
ob_start();

// Get category filter
$categoryFilter = $_GET['category'] ?? '';
$whereClause = "WHERE status = 'active'";
if ($categoryFilter) {
    $whereClause .= " AND category_type = '" . mysqli_real_escape_string($connection, $categoryFilter) . "'";
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM tests $whereClause";
$countResult = mysqli_query($connection, $countQuery);
$totalTests = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalTests / $perPage);

// Get tests
$query = "SELECT t.*, c.title_category, c.url_category 
          FROM tests t
          LEFT JOIN categories c ON t.category_id = c.id_category
          $whereClause
          ORDER BY t.created_at DESC 
          LIMIT $perPage OFFSET $offset";

$result = mysqli_query($connection, $query);
$tests = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Map difficulty to Russian
    $difficultyMap = [
        'easy' => 'Легкий',
        'medium' => 'Средний',
        'hard' => 'Сложный'
    ];
    
    $tests[] = [
        'id_test' => $row['id_test'],
        'title_test' => $row['title_test'],
        'url_test' => $row['url_test'],
        'image_test' => $row['image_test'] ?: '/images/default-test.jpg',
        'difficulty' => $difficultyMap[$row['difficulty']] ?? $row['difficulty'],
        'duration' => $row['duration'],
        'questions_count' => $row['questions_count'],
        'category_title' => $row['title_category'] ?: 'Общие',
        'category_url' => $row['url_category'] ?: '#'
    ];
}

if (count($tests) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($tests, 'test', [
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

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    $baseUrl = '/tests' . ($categoryFilter ? "?category=$categoryFilter" : '');
    renderPaginationModern($page, $totalPages, $baseUrl);
}
$greyContent6 = ob_get_clean();

// Section 7: No comments for listing
$blueContent = '';

// Set page title
$pageTitle = 'Онлайн тесты';
$metaD = 'Онлайн тесты по различным предметам: ЕГЭ, ОГЭ, олимпиады, профориентация';
$metaK = 'тесты, онлайн тесты, ЕГЭ, ОГЭ, олимпиады, профориентация, экзамены';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
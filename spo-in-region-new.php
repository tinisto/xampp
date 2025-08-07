<?php
// SPO in region page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get region from URL
$regionUrlName = $_GET['region_name_en'] ?? '';
if (empty($regionUrlName)) {
    header("Location: /spo-all-regions");
    exit();
}

// Get region data
$regionQuery = "SELECT * FROM regions WHERE url_region = ?";
$stmt = $connection->prepare($regionQuery);
$stmt->bind_param("s", $regionUrlName);
$stmt->execute();
$regionResult = $stmt->get_result();
$region = $regionResult->fetch_assoc();

if (!$region) {
    header("Location: /404");
    exit();
}

// Get current page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM spo WHERE region_id = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $region['id_region']);
$stmt->execute();
$countResult = $stmt->get_result();
$totalSPO = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalSPO / $perPage);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('СПО: ' . $region['title_region'], [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => $totalSPO . ' ' . ($totalSPO == 1 ? 'учреждение' : ($totalSPO < 5 ? 'учреждения' : 'учреждений')) . ' СПО'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
renderBreadcrumb([
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'СПО по регионам', 'url' => '/spo-all-regions'],
    ['text' => $region['title_region']]
]);
$greyContent2 = ob_get_clean();

// Section 3: Empty for listing
$greyContent3 = '';

// Section 4: Filters and Search
ob_start();
echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
renderFiltersDropdown([
    'sortOptions' => [
        'name_asc' => 'По названию (А-Я)',
        'name_desc' => 'По названию (Я-А)',
        'city' => 'По городу'
    ]
]);

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск СПО...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: SPO list
ob_start();

// Get SPO institutions
$spoQuery = "SELECT * FROM spo 
             WHERE region_id = ? 
             ORDER BY title ASC 
             LIMIT ? OFFSET ?";
$stmt = $connection->prepare($spoQuery);
$stmt->bind_param("iii", $region['id_region'], $perPage, $offset);
$stmt->execute();
$spoResult = $stmt->get_result();

$spoList = [];
while ($row = $spoResult->fetch_assoc()) {
    $spoList[] = [
        'id_news' => $row['id'],
        'title_news' => $row['title'],
        'url_news' => $row['url'],
        'image_news' => '/images/default-spo.jpg',
        'created_at' => date('Y-m-d'),
        'category_title' => $row['city'] ?: 'Город не указан',
        'category_url' => '#'
    ];
}

if (count($spoList) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($spoList, 'spo', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-university fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этом регионе пока нет учреждений СПО</p>
          </div>';
}
$greyContent5 = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/spo-in-region/' . $region['url_region']);
}
$greyContent6 = ob_get_clean();

// Section 7: No comments for listing
$blueContent = '';

// Set page title
$pageTitle = 'СПО: ' . $region['title_region'];
$metaD = 'Средние профессиональные образовательные учреждения (СПО) в регионе ' . $region['title_region'];
$metaK = 'СПО, колледжи, техникумы, ' . $region['title_region'] . ', среднее профессиональное образование';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
// VPO in region page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get region from URL - check both possible parameter names
$regionUrlName = $_GET['region_url'] ?? $_GET['region_name_en'] ?? '';
if (empty($regionUrlName)) {
    header("Location: /vpo-all-regions");
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
$countQuery = "SELECT COUNT(*) as total FROM vpo WHERE region_id = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $region['id_region']);
$stmt->execute();
$countResult = $stmt->get_result();
$totalVPO = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalVPO / $perPage);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('ВПО: ' . $region['title_region'], [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => $totalVPO . ' ' . ($totalVPO == 1 ? 'ВУЗ' : ($totalVPO < 5 ? 'ВУЗа' : 'ВУЗов'))
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
renderBreadcrumb([
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'ВПО по регионам', 'url' => '/vpo-all-regions'],
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
    'placeholder' => 'Поиск ВУЗа...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: VPO list
ob_start();

// Get VPO institutions
$vpoQuery = "SELECT * FROM vpo 
             WHERE region_id = ? 
             ORDER BY title ASC 
             LIMIT ? OFFSET ?";
$stmt = $connection->prepare($vpoQuery);
$stmt->bind_param("iii", $region['id_region'], $perPage, $offset);
$stmt->execute();
$vpoResult = $stmt->get_result();

$vpoList = [];
while ($row = $vpoResult->fetch_assoc()) {
    $vpoList[] = [
        'id_news' => $row['id'],
        'title_news' => $row['title'],
        'url_news' => $row['url'],
        'image_news' => '/images/default-vpo.jpg',
        'created_at' => date('Y-m-d'),
        'category_title' => $row['city'] ?: 'Город не указан',
        'category_url' => '#'
    ];
}

if (count($vpoList) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($vpoList, 'vpo', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-graduation-cap fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этом регионе пока нет ВУЗов</p>
          </div>';
}
$greyContent5 = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/vpo-in-region/' . $region['url_region']);
}
$greyContent6 = ob_get_clean();

// Section 7: No comments for listing
$blueContent = '';

// Set page title
$pageTitle = 'ВПО: ' . $region['title_region'];
$metaD = 'Высшие учебные заведения (ВПО) в регионе ' . $region['title_region'];
$metaK = 'ВПО, университеты, институты, ' . $region['title_region'] . ', высшее образование';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
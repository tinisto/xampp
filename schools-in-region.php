<?php
// Schools in region page - using unified template
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get region slug from URL
$regionSlug = $_GET['region'] ?? '';
if (empty($regionSlug)) {
    // Show all regions listing if no specific region is requested
    header("Location: /schools-all-regions");
    exit();
}

// Get region data
$regionQuery = "SELECT * FROM regions WHERE region_name_en = ?";
$stmt = $connection->prepare($regionQuery);
$stmt->bind_param("s", $regionSlug);
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
$countQuery = "SELECT COUNT(*) as total FROM schools WHERE region_id = ?";
$stmt = $connection->prepare($countQuery);
$stmt->bind_param("i", $region['region_id']);
$stmt->execute();
$countResult = $stmt->get_result();
$totalSchools = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalSchools / $perPage);

// Header content
ob_start();
?>
<div style="text-align: center; padding: 40px 20px; margin-bottom: 30px;">
    <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; color: #333;">Школы: <?= htmlspecialchars($region['region_name']) ?></h1>
    <p style="font-size: 1.2rem; color: #666;"><?= $totalSchools ?> <?= ($totalSchools == 1 ? 'школа' : ($totalSchools < 5 ? 'школы' : 'школ')) ?></p>
</div>
<?php
$headerContent = ob_get_clean();

// Navigation content (breadcrumb)
ob_start();
?>
<div style="padding: 15px 20px;">
    <nav style="font-size: 14px; color: #666;">
        <a href="/" style="color: #28a745; text-decoration: none;">Главная</a>
        <span style="margin: 0 10px;">/</span>
        <a href="/schools-all-regions" style="color: #28a745; text-decoration: none;">Школы по регионам</a>
        <span style="margin: 0 10px;">/</span>
        <span><?= htmlspecialchars($region['region_name']) ?></span>
    </nav>
</div>
<?php
$navigationContent = ob_get_clean();

// Metadata content (empty for this page)
$metadataContent = '';

// Filters content
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

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search.php';
renderUnifiedSearch([
    'placeholder' => 'Поиск школ...',
    'buttonText' => 'Найти',
    'style' => 'compact'
]);

echo '</div>';
$filtersContent = ob_get_clean();

// Main content (Schools list)
ob_start();

// Get Schools institutions
$schoolsQuery = "SELECT * FROM schools 
             WHERE region_id = ? 
             ORDER BY name ASC 
             LIMIT ? OFFSET ?";
$stmt = $connection->prepare($schoolsQuery);
$stmt->bind_param("iii", $region['region_id'], $perPage, $offset);
$stmt->execute();
$schoolsResult = $stmt->get_result();

$schoolsList = [];
while ($row = $schoolsResult->fetch_assoc()) {
    $schoolsList[] = [
        'id_news' => $row['id'],
        'title_news' => $row['name'],
        'url_news' => $row['url_slug'],
        'image_news' => '/images/default-school.jpg',
        'created_at' => date('Y-m-d'),
        'category_title' => 'Школа',
        'category_url' => 'schools'
    ];
}

if (count($schoolsList) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($schoolsList, 'school', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-school fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>В этом регионе пока нет школ</p>
          </div>';
}
$mainContent = ob_get_clean();

// Pagination content
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/schools-in/' . $region['region_name_en']);
}
$paginationContent = ob_get_clean();

// Set page title
$pageTitle = 'Школы: ' . $region['region_name'];

// Include the unified template
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>
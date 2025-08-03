<?php
// Initialize region_id and type from additionalData
$region_id = isset($additionalData['region_id']) ? (int) $additionalData['region_id'] : null;
$type = isset($additionalData['type']) ? $additionalData['type'] : 'spo';

// Include necessary files
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";
include 'outputEducationalInstitutions.php';
include 'function-query.php';
include 'outputTowns.php';

// Constants
$institutionsPerPage = 20;
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$pageOffset = max(0, ($currentPage - 1) * $institutionsPerPage);

// Fetch institutions data
$institutions_result = getInstitutions($connection, $region_id, $type, $pageOffset, $institutionsPerPage);

// Fetch region data
$query_regions = "SELECT * FROM regions WHERE id_region = ?";
$stmt_regions = $connection->prepare($query_regions);
$stmt_regions->bind_param("i", $region_id);
$stmt_regions->execute();
$result_regions = $stmt_regions->get_result();
$myrow_region = $result_regions->fetch_assoc();
$stmt_regions->close();

if ($myrow_region) {
    $region_name_en = $myrow_region['region_name_en'];
    
    // Fetch total institutions count
    $totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $type WHERE id_region = ?";
    $stmt_total = $connection->prepare($totalInstitutions_sql);
    $stmt_total->bind_param("i", $region_id);
    $stmt_total->execute();
    $totalInstitutions_result = $stmt_total->get_result();
    $totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
    $stmt_total->close();
    
    // Include header links
    // $id_region = $region_id;
    // include 'header-links.php';  // Removed - contains unwanted email feature
}

// Type info for styling
$typeInfo = [
    'schools' => ['color' => '#17a2b8', 'icon' => 'school'],
    'vpo' => ['color' => '#28a745', 'icon' => 'university'],
    'spo' => ['color' => '#ffc107', 'icon' => 'graduation-cap']
];
$currentType = $typeInfo[$type] ?? $typeInfo['schools'];
?>

<style>
    .page-header {
        background: linear-gradient(135deg, <?= $currentType['color'] ?> 0%, <?= $currentType['color'] ?>dd 100%);
        color: white;
        padding: 40px 0;
        margin: -20px -15px 30px -15px;
    }
    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .breadcrumb-custom {
        background: rgba(255,255,255,0.2);
        padding: 8px 16px;
        border-radius: 25px;
        display: inline-block;
        margin-bottom: 15px;
    }
    .breadcrumb-custom a {
        color: white;
        text-decoration: none;
        opacity: 0.8;
    }
    .breadcrumb-custom a:hover {
        opacity: 1;
    }
    .institution-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid <?= $currentType['color'] ?>;
    }
    .institution-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .institution-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .institution-link {
        color: inherit;
        text-decoration: none;
    }
    .institution-link:hover .institution-name {
        color: <?= $currentType['color'] ?>;
    }
    .institution-meta {
        font-size: 14px;
        color: #666;
    }
    .institution-meta i {
        color: <?= $currentType['color'] ?>;
        margin-right: 5px;
    }
    .filter-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .filter-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
    .town-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .town-item {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .town-item:last-child {
        border-bottom: none;
    }
    .town-link {
        color: #666;
        text-decoration: none;
        font-size: 14px;
    }
    .town-link:hover {
        color: <?= $currentType['color'] ?>;
    }
    .stats-bar {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #666;
    }
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 24px;
        }
        .col-md-3 {
            margin-top: 20px;
        }
    }
</style>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb-custom">
            <a href="/">Главная</a> / 
            <a href="/<?= $type ?>-all-regions">Все регионы</a> / 
            <?= htmlspecialchars($myrow_region['region_name']) ?>
        </div>
        <h1>
            <i class="fas fa-<?= $currentType['icon'] ?> me-2"></i>
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <div>Найдено учреждений: <?= number_format($totalInstitutions) ?></div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Main content -->
        <div class="col-md-9">
            <div class="stats-bar">
                Показано <?= min($institutionsPerPage, $totalInstitutions - $pageOffset) ?> из <?= $totalInstitutions ?> учреждений
            </div>
            
            <?php if ($institutions_result && mysqli_num_rows($institutions_result) > 0): ?>
                <?php while ($institution = mysqli_fetch_assoc($institutions_result)): ?>
                    <?php
                    $id_field = "id_$type";
                    $name_field = $type === 'schools' ? 'school_name' : ($type === 'vpo' ? 'vpo_name' : 'spo_name');
                    $url = $type === 'schools' ? 'school' : $type;
                    ?>
                    <div class="institution-card">
                        <a href="/<?= $url ?>/<?= $institution[$id_field] ?>" class="institution-link">
                            <div class="institution-name"><?= htmlspecialchars($institution[$name_field]) ?></div>
                            <?php if (!empty($institution['address'])): ?>
                            <div class="institution-meta">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($institution['address']) ?>
                            </div>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    В данном регионе учреждения не найдены
                </div>
            <?php endif; ?>
            
            <?php
            // Pagination
            if ($totalInstitutions > $institutionsPerPage) {
                $totalPages = ceil($totalInstitutions / $institutionsPerPage);
                generatePagination($currentPage, $totalPages, $region_id);
            }
            ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="filter-box">
                <h3 class="filter-title">
                    <i class="fas fa-filter me-2"></i>Города региона
                </h3>
                <?php
                $query_towns = "
                    SELECT DISTINCT t.*
                    FROM towns t
                    JOIN $type s ON t.id_town = s.id_town
                    WHERE t.id_region = ?
                    ORDER BY t.town_name
                ";
                $stmt_towns = $connection->prepare($query_towns);
                $stmt_towns->bind_param("i", $region_id);
                $stmt_towns->execute();
                $result_towns = $stmt_towns->get_result();
                $stmt_towns->close();
                ?>
                
                <div class="town-list">
                    <?php while ($town = $result_towns->fetch_assoc()): ?>
                        <div class="town-item">
                            <a href="/<?= $type ?>-in-town/<?= $town['town_name_en'] ?>" class="town-link">
                                <?= htmlspecialchars($town['town_name']) ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Initialize region_id and type
$region_id = isset($additionalData['region_id']) ? (int) $additionalData['region_id'] : null;
$type = isset($additionalData['type']) ? $additionalData['type'] : 'spo';
$myrow_region = isset($additionalData['myrow_region']) ? $additionalData['myrow_region'] : null;

// Include necessary files
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";
include 'function-query.php';

// Constants
$institutionsPerPage = 20;
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$pageOffset = max(0, ($currentPage - 1) * $institutionsPerPage);

// Fetch institutions
$institutions_result = getInstitutions($connection, $region_id, $type, $pageOffset, $institutionsPerPage);

// Get total count
$totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $type WHERE id_region = ?";
$stmt_total = $connection->prepare($totalInstitutions_sql);
$stmt_total->bind_param("i", $region_id);
$stmt_total->execute();
$totalInstitutions_result = $stmt_total->get_result();
$totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
$stmt_total->close();

// Define institution type details
$typeDetails = [
    'schools' => ['name' => 'Школы', 'field' => 'school_name', 'url' => 'school'],
    'vpo' => ['name' => 'ВУЗы', 'field' => 'vpo_name', 'url' => 'vpo'],
    'spo' => ['name' => 'ССУЗы', 'field' => 'spo_name', 'url' => 'spo']
];
$currentType = $typeDetails[$type] ?? $typeDetails['schools'];
?>

<style>
    .page-header {
        background: #f8f9fa;
        padding: 20px 0;
        margin-bottom: 30px;
        border-bottom: 1px solid #dee2e6;
    }
    .breadcrumb {
        margin-bottom: 10px;
        background: none;
        padding: 0;
    }
    .breadcrumb a {
        color: #28a745;
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    .page-stats {
        font-size: 16px;
        color: #666;
    }
    .content-area {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .institution-item {
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }
    .institution-item:last-child {
        border-bottom: none;
    }
    .institution-name {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .institution-name a {
        color: #333;
        text-decoration: none;
    }
    .institution-name a:hover {
        color: #28a745;
    }
    .institution-info {
        font-size: 14px;
        color: #666;
    }
    .sidebar {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }
    .sidebar h4 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #333;
    }
    .town-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .town-list li {
        padding: 8px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .town-list li:last-child {
        border-bottom: none;
    }
    .town-list a {
        color: #666;
        text-decoration: none;
        font-size: 14px;
    }
    .town-list a:hover {
        color: #28a745;
    }
    .no-data {
        text-align: center;
        padding: 40px;
        color: #666;
    }
</style>

<div class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="/">Главная</a> / 
            <a href="/<?= $type ?>-all-regions"><?= $currentType['name'] ?> России</a> / 
            <span><?= htmlspecialchars($myrow_region['region_name']) ?></span>
        </nav>
        <h1 class="page-title"><?= $currentType['name'] ?> <?= htmlspecialchars($myrow_region['region_name_rod']) ?></h1>
        <div class="page-stats">Найдено учреждений: <?= number_format($totalInstitutions) ?></div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="content-area">
                <?php if ($institutions_result && mysqli_num_rows($institutions_result) > 0): ?>
                    <?php while ($institution = mysqli_fetch_assoc($institutions_result)): ?>
                        <?php
                        $id_field = "id_$type";
                        $name_field = $currentType['field'];
                        ?>
                        <div class="institution-item">
                            <div class="institution-name">
                                <a href="/<?= $currentType['url'] ?>/<?= $institution[$id_field] ?>">
                                    <?= htmlspecialchars($institution[$name_field]) ?>
                                </a>
                            </div>
                            <?php if (!empty($institution['address'])): ?>
                                <div class="institution-info">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($institution['address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php
                    // Pagination
                    if ($totalInstitutions > $institutionsPerPage) {
                        $totalPages = ceil($totalInstitutions / $institutionsPerPage);
                        echo '<div class="mt-4">';
                        generatePagination($currentPage, $totalPages, $region_id);
                        echo '</div>';
                    }
                    ?>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-info-circle fa-3x mb-3" style="color: #ddd;"></i>
                        <p>В данном регионе учреждения не найдены</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="sidebar">
                <h4>Города региона</h4>
                <?php
                $query_towns = "
                    SELECT DISTINCT t.town_name, t.town_name_en, t.id_town
                    FROM towns t
                    JOIN $type s ON t.id_town = s.id_town
                    WHERE t.id_region = ?
                    ORDER BY t.town_name
                ";
                $stmt_towns = $connection->prepare($query_towns);
                $stmt_towns->bind_param("i", $region_id);
                $stmt_towns->execute();
                $result_towns = $stmt_towns->get_result();
                ?>
                
                <?php if ($result_towns->num_rows > 0): ?>
                    <ul class="town-list">
                        <?php while ($town = $result_towns->fetch_assoc()): ?>
                            <li>
                                <a href="/<?= $type ?>-in-town/<?= $town['town_name_en'] ?>">
                                    <?= htmlspecialchars($town['town_name']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Нет городов с учреждениями</p>
                <?php endif; ?>
                
                <?php $stmt_towns->close(); ?>
            </div>
        </div>
    </div>
</div>
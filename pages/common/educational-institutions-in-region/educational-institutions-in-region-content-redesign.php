<?php
// Ensure we have database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

// Initialize region_id and type from additionalData
$region_id = isset($additionalData['region_id']) ? (int) $additionalData['region_id'] : null;
$type = isset($additionalData['type']) ? $additionalData['type'] : 'spo';

// Include necessary files
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/pagination.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";
include 'function-query.php';

// Constants
$institutionsPerPage = 24;
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

if (!$myrow_region) {
    header("Location: /error");
    exit();
}

$region_name_en = $myrow_region['region_name_en'];

// Fetch total institutions count
$totalInstitutions_sql = "SELECT COUNT(*) AS total FROM $type WHERE id_region = ?";
$stmt_total = $connection->prepare($totalInstitutions_sql);
$stmt_total->bind_param("i", $region_id);
$stmt_total->execute();
$totalInstitutions_result = $stmt_total->get_result();
$totalInstitutions = $totalInstitutions_result->fetch_assoc()['total'];
$stmt_total->close();

// Get type info
$typeInfo = [
    'schools' => ['title' => 'Школы', 'icon' => 'school', 'color' => '#17a2b8'],
    'vpo' => ['title' => 'ВУЗы', 'icon' => 'university', 'color' => '#28a745'],
    'spo' => ['title' => 'ССУЗы', 'icon' => 'graduation-cap', 'color' => '#ffc107']
];
$currentTypeInfo = $typeInfo[$type] ?? $typeInfo['schools'];
?>

<style>
    .region-hero {
        background: linear-gradient(135deg, <?= $currentTypeInfo['color'] ?> 0%, <?= $currentTypeInfo['color'] ?>dd 100%);
        color: white;
        padding: 50px 0;
        margin-bottom: 40px;
    }
    .region-hero h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .region-subtitle {
        font-size: 18px;
        opacity: 0.9;
        margin-bottom: 20px;
    }
    .breadcrumb-modern {
        background: rgba(255,255,255,0.2);
        padding: 10px 20px;
        border-radius: 50px;
        display: inline-block;
    }
    .breadcrumb-modern a {
        color: white;
        text-decoration: none;
        opacity: 0.8;
        transition: opacity 0.3s;
    }
    .breadcrumb-modern a:hover {
        opacity: 1;
    }
    .breadcrumb-modern .separator {
        margin: 0 10px;
        opacity: 0.6;
    }
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    .filter-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
    .town-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .town-badge {
        background: #f0f0f0;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .town-badge:hover {
        background: #e0e0e0;
    }
    .town-badge.active {
        background: <?= $currentTypeInfo['color'] ?>20;
        border-color: <?= $currentTypeInfo['color'] ?>;
        color: <?= $currentTypeInfo['color'] ?>;
    }
    .institutions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    .institution-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
    }
    .institution-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .institution-header {
        background: <?= $currentTypeInfo['color'] ?>10;
        padding: 20px;
        border-bottom: 1px solid <?= $currentTypeInfo['color'] ?>20;
    }
    .institution-type-icon {
        width: 40px;
        height: 40px;
        background: <?= $currentTypeInfo['color'] ?>;
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    .institution-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        line-height: 1.4;
    }
    .institution-link {
        color: inherit;
        text-decoration: none;
    }
    .institution-link:hover .institution-name {
        color: <?= $currentTypeInfo['color'] ?>;
    }
    .institution-location {
        font-size: 14px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .institution-details {
        padding: 20px;
    }
    .detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 14px;
        color: #666;
    }
    .detail-item i {
        color: <?= $currentTypeInfo['color'] ?>;
        width: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }
    .stats-bar {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .stats-info {
        font-size: 16px;
        color: #666;
    }
    .stats-number {
        font-weight: 700;
        color: <?= $currentTypeInfo['color'] ?>;
        font-size: 20px;
    }
    @media (max-width: 768px) {
        .region-hero h1 {
            font-size: 24px;
        }
        .institutions-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .filter-section {
            padding: 15px;
        }
    }
</style>

<div class="region-hero">
    <div class="container">
        <div class="breadcrumb-modern">
            <a href="/">Главная</a>
            <span class="separator">/</span>
            <a href="/<?= $type ?>-all-regions"><?= $currentTypeInfo['title'] ?> России</a>
            <span class="separator">/</span>
            <span><?= htmlspecialchars($myrow_region['region_name']) ?></span>
        </div>
        <h1><?= $currentTypeInfo['title'] ?> <?= htmlspecialchars($myrow_region['region_name_rod']) ?></h1>
        <p class="region-subtitle">
            <i class="fas fa-<?= $currentTypeInfo['icon'] ?> me-2"></i>
            Найдено учреждений: <?= number_format($totalInstitutions) ?>
        </p>
    </div>
</div>

<div class="container">
    <?php
    // Fetch towns with institutions
    $query_towns = "
        SELECT DISTINCT t.*, COUNT(DISTINCT i.id_$type) as institution_count
        FROM towns t
        JOIN $type i ON t.id_town = i.id_town
        WHERE t.id_region = ?
        GROUP BY t.id_town
        ORDER BY institution_count DESC, t.town_name ASC
    ";
    $stmt_towns = $connection->prepare($query_towns);
    $stmt_towns->bind_param("i", $region_id);
    $stmt_towns->execute();
    $result_towns = $stmt_towns->get_result();
    $towns = [];
    while ($town = $result_towns->fetch_assoc()) {
        $towns[] = $town;
    }
    $stmt_towns->close();
    ?>
    
    <?php if (count($towns) > 1): ?>
    <div class="filter-section">
        <h3 class="filter-title">Фильтр по городам</h3>
        <div class="town-filter">
            <span class="town-badge active" data-town="all">
                Все города (<?= $totalInstitutions ?>)
            </span>
            <?php foreach ($towns as $town): ?>
                <span class="town-badge" data-town="<?= $town['id_town'] ?>">
                    <?= htmlspecialchars($town['town_name']) ?> (<?= $town['institution_count'] ?>)
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="stats-bar">
        <div class="stats-info">
            Показано <?= min($institutionsPerPage, $totalInstitutions - $pageOffset) ?> из 
            <span class="stats-number"><?= $totalInstitutions ?></span> учреждений
        </div>
        <div class="stats-info">
            Страница <span class="stats-number"><?= $currentPage ?></span> из 
            <span class="stats-number"><?= ceil($totalInstitutions / $institutionsPerPage) ?></span>
        </div>
    </div>
    
    <?php if ($institutions_result && mysqli_num_rows($institutions_result) > 0): ?>
        <div class="institutions-grid">
            <?php while ($institution = mysqli_fetch_assoc($institutions_result)): ?>
                <?php
                // Get the appropriate ID and name fields based on type
                $id_field = "id_$type";
                $name_field = $type === 'schools' ? 'school_name' : ($type === 'vpo' ? 'vpo_name' : 'spo_name');
                $url_field = $type === 'schools' ? 'school' : $type;
                
                // Get town info
                $town_query = "SELECT town_name FROM towns WHERE id_town = ?";
                $stmt_town = $connection->prepare($town_query);
                $stmt_town->bind_param("i", $institution['id_town']);
                $stmt_town->execute();
                $town_result = $stmt_town->get_result();
                $town = $town_result->fetch_assoc();
                $stmt_town->close();
                ?>
                
                <div class="institution-card" data-town="<?= $institution['id_town'] ?>">
                    <a href="/<?= $url_field ?>/<?= $institution[$id_field] ?>" class="institution-link">
                        <div class="institution-header">
                            <div class="institution-type-icon">
                                <i class="fas fa-<?= $currentTypeInfo['icon'] ?>"></i>
                            </div>
                            <h3 class="institution-name"><?= htmlspecialchars($institution[$name_field]) ?></h3>
                            <div class="institution-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($town['town_name'] ?? 'Не указан') ?>
                            </div>
                        </div>
                        <div class="institution-details">
                            <?php if (!empty($institution['address'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-location-dot"></i>
                                <span><?= htmlspecialchars($institution['address']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($institution['phone'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars($institution['phone']) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($institution['website'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-globe"></i>
                                <span>Есть сайт</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        
        <?php
        // Pagination
        if ($totalInstitutions > $institutionsPerPage) {
            $totalPages = ceil($totalInstitutions / $institutionsPerPage);
            ?>
            <style>
                .pagination-modern {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 10px;
                    margin: 40px 0;
                }
                .pagination-modern a,
                .pagination-modern span {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 40px;
                    height: 40px;
                    padding: 0 15px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }
                .pagination-modern a {
                    background: white;
                    color: #666;
                    border: 1px solid #ddd;
                }
                .pagination-modern a:hover {
                    background: <?= $currentTypeInfo['color'] ?>;
                    color: white;
                    border-color: <?= $currentTypeInfo['color'] ?>;
                }
                .pagination-modern .current {
                    background: <?= $currentTypeInfo['color'] ?>;
                    color: white;
                    border: 1px solid <?= $currentTypeInfo['color'] ?>;
                }
                .pagination-modern .dots {
                    color: #999;
                }
            </style>
            
            <div class="pagination-modern">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php
                $range = 2;
                $start = max(1, $currentPage - $range);
                $end = min($totalPages, $currentPage + $range);
                
                if ($start > 1) {
                    echo '<a href="?page=1">1</a>';
                    if ($start > 2) echo '<span class="dots">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $currentPage) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="?page=' . $i . '">' . $i . '</a>';
                    }
                }
                
                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) echo '<span class="dots">...</span>';
                    echo '<a href="?page=' . $totalPages . '">' . $totalPages . '</a>';
                }
                ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php
        }
        ?>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-<?= $currentTypeInfo['icon'] ?>"></i>
            <h3>Учреждения не найдены</h3>
            <p>В этом регионе пока нет зарегистрированных учреждений данного типа</p>
        </div>
    <?php endif; ?>
</div>

<script>
// Town filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const townBadges = document.querySelectorAll('.town-badge');
    const institutionCards = document.querySelectorAll('.institution-card');
    
    townBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            // Update active state
            townBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const selectedTown = this.getAttribute('data-town');
            
            // Filter institutions
            institutionCards.forEach(card => {
                if (selectedTown === 'all' || card.getAttribute('data-town') === selectedTown) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
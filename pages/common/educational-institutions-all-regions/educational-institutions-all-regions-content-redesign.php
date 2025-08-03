<?php
// Ensure we have database connection
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

// Get the variables from additionalData
$table = $additionalData['table'] ?? 'schools';
$countField = $additionalData['countField'] ?? 'school_count';
$linkPrefix = $additionalData['linkPrefix'] ?? 'schools-in-region';
$pageTitle = $additionalData['pageTitle'] ?? 'Образовательные учреждения';

$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$result = $connection->query($sql);

if (!$result) {
    header("Location: /error");
    exit();
}
?>

<style>
    .regions-hero {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 50px;
    }
    .regions-hero h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .regions-hero p {
        font-size: 18px;
        opacity: 0.9;
    }
    .search-container {
        max-width: 600px;
        margin: 30px auto 0;
    }
    .search-input {
        width: 100%;
        padding: 15px 20px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .search-input:focus {
        outline: none;
        box-shadow: 0 5px 30px rgba(0,0,0,0.15);
    }
    .regions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }
    .region-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .region-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #28a745;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }
    .region-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .region-card:hover::before {
        transform: scaleY(1);
    }
    .region-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .region-link {
        color: inherit;
        text-decoration: none;
        display: block;
    }
    .region-link:hover {
        color: #28a745;
    }
    .institution-count {
        background: #e7f5ec;
        color: #28a745;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .institution-icon {
        font-size: 16px;
    }
    .stats-summary {
        background: #f8f9fa;
        padding: 40px 0;
        margin: 50px 0;
        border-radius: 12px;
    }
    .stat-box {
        text-align: center;
    }
    .stat-number {
        font-size: 48px;
        font-weight: 700;
        color: #28a745;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 16px;
        color: #666;
    }
    @media (max-width: 768px) {
        .regions-hero h1 {
            font-size: 28px;
        }
        .regions-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .stat-number {
            font-size: 36px;
        }
    }
</style>

<div class="regions-hero">
    <div class="container text-center">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p>Выберите регион для просмотра образовательных учреждений</p>
        <div class="search-container">
            <input type="text" class="search-input" id="regionSearch" placeholder="Поиск по названию региона...">
        </div>
    </div>
</div>

<div class="container">
    <?php
    // First, get total counts
    $totalRegions = 0;
    $totalInstitutions = 0;
    $regionsData = [];
    
    while ($row = $result->fetch_assoc()) {
        $count_sql = "SELECT COUNT(*) AS institution_count FROM $table WHERE id_region = {$row['id_region']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $institution_count = $count_result->fetch_assoc()['institution_count'];
            if ($institution_count > 0) {
                $totalRegions++;
                $totalInstitutions += $institution_count;
                $regionsData[] = [
                    'id' => $row['id_region'],
                    'name' => $row['region_name'],
                    'name_en' => $row['region_name_en'],
                    'count' => $institution_count
                ];
            }
        }
    }
    ?>
    
    <!-- Statistics Summary -->
    <div class="stats-summary">
        <div class="row">
            <div class="col-md-6">
                <div class="stat-box">
                    <div class="stat-number"><?= number_format($totalRegions) ?></div>
                    <div class="stat-label">Регионов</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-box">
                    <div class="stat-number"><?= number_format($totalInstitutions) ?></div>
                    <div class="stat-label">
                        <?php
                        $label = '';
                        switch($table) {
                            case 'schools': $label = 'Школ'; break;
                            case 'vpo': $label = 'ВУЗов'; break;
                            case 'spo': $label = 'ССУЗов'; break;
                        }
                        echo $label;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Regions Grid -->
    <div class="regions-grid" id="regionsGrid">
        <?php foreach ($regionsData as $region): ?>
            <div class="region-card" data-region-name="<?= mb_strtolower($region['name']) ?>">
                <a href="/<?= $linkPrefix ?>/<?= $region['name_en'] ?>" class="region-link">
                    <div class="region-name">
                        <span><?= htmlspecialchars($region['name']) ?></span>
                        <span class="institution-count">
                            <i class="fas fa-<?= $table === 'schools' ? 'school' : ($table === 'vpo' ? 'university' : 'graduation-cap') ?> institution-icon"></i>
                            <?= number_format($region['count']) ?>
                        </span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Search functionality
document.getElementById('regionSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.region-card');
    
    cards.forEach(card => {
        const regionName = card.getAttribute('data-region-name');
        if (regionName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php
$connection->close();
?>
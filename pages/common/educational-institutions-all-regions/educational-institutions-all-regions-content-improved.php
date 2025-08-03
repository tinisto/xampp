<?php
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
        margin: -20px -15px 50px -15px;
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
    .region-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #28a745;
    }
    .region-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .region-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .region-link {
        color: #333;
        text-decoration: none;
    }
    .region-link:hover {
        color: #28a745;
    }
    .institution-count {
        background: #e7f5ec;
        color: #28a745;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        margin-left: 10px;
    }
    .stats-summary {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 40px;
        text-align: center;
    }
    .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: #28a745;
    }
    @media (max-width: 768px) {
        .regions-hero h1 {
            font-size: 28px;
        }
        .regions-hero {
            padding: 40px 0;
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
    // Calculate statistics
    $totalRegions = 0;
    $totalInstitutions = 0;
    $regionsData = [];
    
    while ($row = $result->fetch_assoc()) {
        $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = {$row['id_region']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $institution_count = $count_result->fetch_assoc()['count'];
            if ($institution_count > 0) {
                $totalRegions++;
                $totalInstitutions += $institution_count;
                $regionsData[] = [
                    'name' => $row['region_name'],
                    'name_en' => $row['region_name_en'],
                    'count' => $institution_count
                ];
            }
        }
    }
    ?>
    
    <div class="stats-summary">
        <div class="row">
            <div class="col-6">
                <div class="stat-number"><?= number_format($totalRegions) ?></div>
                <div>Регионов</div>
            </div>
            <div class="col-6">
                <div class="stat-number"><?= number_format($totalInstitutions) ?></div>
                <div><?= $type === 'schools' ? 'Школ' : ($type === 'vpo' ? 'ВУЗов' : 'ССУЗов') ?></div>
            </div>
        </div>
    </div>
    
    <div class="row" id="regionsContainer">
        <?php foreach ($regionsData as $region): ?>
            <div class="col-lg-4 col-md-6" data-region-name="<?= mb_strtolower($region['name']) ?>">
                <div class="region-card">
                    <a href="/<?= $linkPrefix ?>/<?= $region['name_en'] ?>" class="region-link">
                        <div class="region-name">
                            <?= htmlspecialchars($region['name']) ?>
                            <span class="institution-count"><?= number_format($region['count']) ?></span>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.getElementById('regionSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const regions = document.querySelectorAll('[data-region-name]');
    
    regions.forEach(region => {
        const regionName = region.getAttribute('data-region-name');
        region.style.display = regionName.includes(searchTerm) ? 'block' : 'none';
    });
});
</script>

<?php
$connection->close();
?>
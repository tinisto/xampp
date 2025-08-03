<?php
// Simple clean design
$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$result = $connection->query($sql);

if (!$result) {
    header("Location: /error");
    exit();
}
?>

<style>
    .page-title {
        text-align: center;
        margin: 30px 0;
        color: #333;
    }
    .page-title h1 {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .page-subtitle {
        font-size: 16px;
        color: #666;
    }
    .search-box {
        max-width: 500px;
        margin: 0 auto 40px;
    }
    .search-box input {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
    }
    .search-box input:focus {
        outline: none;
        border-color: #28a745;
    }
    .regions-container {
        margin-bottom: 40px;
    }
    .region-item {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .region-item:hover {
        border-color: #28a745;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .region-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
        color: #333;
    }
    .region-name {
        font-size: 16px;
        font-weight: 500;
    }
    .region-count {
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    .stats-box {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 40px;
    }
    .stats-box h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #333;
    }
    .stats-box .number {
        font-size: 36px;
        font-weight: 700;
        color: #28a745;
    }
</style>

<div class="container">
    <div class="page-title">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="page-subtitle">Выберите регион для просмотра образовательных учреждений</p>
    </div>
    
    <div class="search-box">
        <input type="text" id="regionSearch" placeholder="Поиск региона..." class="form-control">
    </div>
    
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
                    'id' => $row['id_region'],
                    'name' => $row['region_name'],
                    'name_en' => $row['region_name_en'],
                    'count' => $institution_count
                ];
            }
        }
    }
    ?>
    
    <div class="stats-box">
        <h3>Статистика</h3>
        <div class="row">
            <div class="col-6">
                <div class="number"><?= number_format($totalRegions) ?></div>
                <div>Регионов</div>
            </div>
            <div class="col-6">
                <div class="number"><?= number_format($totalInstitutions) ?></div>
                <div><?= $type === 'schools' ? 'Школ' : ($type === 'vpo' ? 'ВУЗов' : 'ССУЗов') ?></div>
            </div>
        </div>
    </div>
    
    <div class="regions-container">
        <div class="row">
            <?php foreach ($regionsData as $region): ?>
                <div class="col-md-6 col-lg-4 mb-3" data-region="<?= mb_strtolower($region['name']) ?>">
                    <div class="region-item">
                        <a href="/<?= $linkPrefix ?>/<?= $region['name_en'] ?>" class="region-link">
                            <span class="region-name"><?= htmlspecialchars($region['name']) ?></span>
                            <span class="region-count"><?= number_format($region['count']) ?></span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('regionSearch').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('[data-region]').forEach(item => {
        const region = item.getAttribute('data-region');
        item.style.display = region.includes(search) ? '' : 'none';
    });
});
</script>
<?php
// Get variables from template engine
$type = $additionalData['type'] ?? 'schools';
$pageTitle = $additionalData['pageTitle'] ?? 'Учебные заведения';
$table = $additionalData['table'] ?? 'schools';
$linkPrefix = $additionalData['linkPrefix'] ?? 'schools-in-region';

// Set count field based on type
$countField = 'count';

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
        text-align: center;
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
    .regions-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .region-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .region-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.12);
    }
    .region-name {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }
    .region-name a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .region-name a:hover {
        color: #28a745;
    }
    .institution-count {
        background: #28a745;
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 16px;
        min-width: 50px;
        text-align: center;
    }
    .regions-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 50px;
    }
    @media (max-width: 1200px) {
        .regions-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    .stats-section {
        background: #f8f9fa;
        padding: 40px 0;
        margin: 50px 0;
        text-align: center;
    }
    .total-count {
        font-size: 48px;
        font-weight: 700;
        color: #28a745;
        margin-bottom: 10px;
    }
    .total-label {
        font-size: 18px;
        color: #666;
    }
    @media (max-width: 768px) {
        .regions-hero h1 {
            font-size: 28px;
        }
        .regions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .region-card {
            padding: 20px;
        }
    }
    @media (max-width: 576px) {
        .regions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="regions-hero">
    <div class="container">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p>Выберите регион для просмотра учебных заведений</p>
    </div>
</div>

<div class="regions-container">
    <div class="regions-grid">
        <?php 
        $totalInstitutions = 0;
        $regionsWithInstitutions = 0;
        $regionCards = [];
        
        while ($row = $result->fetch_assoc()): 
            // Query to count the number of institutions in the region
            $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = {$row['id_region']}";
            $count_result = $connection->query($count_sql);

            if (!$count_result) {
                continue;
            }

            $institution_count = $count_result->fetch_assoc()['count'];
            
            if ($institution_count > 0):
                $totalInstitutions += $institution_count;
                $regionsWithInstitutions++;
                $regionCards[] = [
                    'region' => $row,
                    'count' => $institution_count
                ];
            endif;
        endwhile;
        
        // Sort regions by institution count (descending)
        usort($regionCards, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Display sorted regions
        foreach ($regionCards as $card): ?>
            <div class="region-card">
                <h3 class="region-name">
                    <a href="/<?= $linkPrefix ?>/<?= $card['region']['region_name_en'] ?>">
                        <?= htmlspecialchars($card['region']['region_name']) ?>
                    </a>
                </h3>
                <span class="institution-count"><?= $card['count'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if ($regionsWithInstitutions > 0): ?>
    <div class="stats-section">
        <div class="total-count"><?= number_format($totalInstitutions) ?></div>
        <div class="total-label">
            <?php
            $typeLabel = '';
            switch ($type) {
                case 'schools':
                    $typeLabel = 'школ';
                    break;
                case 'spo':
                    $typeLabel = 'ССУЗов';
                    break;
                case 'vpo':
                    $typeLabel = 'ВУЗов';
                    break;
            }
            ?>
            Всего <?= $typeLabel ?> в <?= $regionsWithInstitutions ?> регионах
        </div>
    </div>
    <?php endif; ?>
</div>
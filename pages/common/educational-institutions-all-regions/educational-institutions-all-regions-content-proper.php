<?php
// Variables are available from template engine
$table = $table ?? 'schools';
$linkPrefix = $linkPrefix ?? '/schools-in-region';
$pageTitle = $pageTitle ?? 'Учебные заведения по регионам';

// Query regions
$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$result = $connection->query($sql);

if (!$result) {
    echo '<div class="container"><div class="alert alert-danger">Ошибка загрузки данных</div></div>';
    return;
}
?>

<div class="container py-4">
    <h1 class="section-title text-center mb-4"><?= $pageTitle ?></h1>
    
    <div class="row g-3">
        <?php 
        $displayed_count = 0;
        while ($row = $result->fetch_assoc()): 
            // Count institutions in this region
            $count_sql = "SELECT COUNT(*) AS count FROM $table WHERE id_region = {$row['id_region']}";
            $count_result = $connection->query($count_sql);
            
            if (!$count_result) {
                continue;
            }
            
            $count_row = $count_result->fetch_assoc();
            $institution_count = $count_row['count'];
            
            if ($institution_count > 0):
                $displayed_count++;
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 region-card">
                    <div class="card-body d-flex justify-content-between align-items:center">
                        <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>" 
                           class="text-decoration-none stretched-link">
                            <h6 class="mb-0"><?= $row['region_name'] ?></h6>
                        </a>
                        <span class="badge bg-primary rounded-pill"><?= $institution_count ?></span>
                    </div>
                </div>
            </div>
        <?php 
            endif;
        endwhile; 
        ?>
    </div>
    
    <?php if ($displayed_count == 0): ?>
        <div class="text-center mt-5">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                В данный момент нет доступных учебных заведений.
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.region-card {
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.region-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.region-card .stretched-link {
    color: var(--text-primary);
}

.region-card .badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}
</style>
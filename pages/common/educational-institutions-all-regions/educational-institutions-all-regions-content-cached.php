<?php
// Include cache functions
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/cache.php';

// Get regions with caching (cache for 2 hours)
$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$regions = cached_query($connection, $sql, 7200);

if (!$regions) {
    header("Location: /error");
    exit();
}

// Get all institution counts in one query with caching
$region_col = ($table == 'schools') ? 'id_region' : 'region_id';
$count_sql = "SELECT $region_col as region_id, COUNT(*) as count FROM $table GROUP BY $region_col";
$counts_data = cached_query($connection, $count_sql, 3600);

// Convert to associative array for easy lookup
$institution_counts = [];
foreach ($counts_data as $count_row) {
    $institution_counts[$count_row['region_id']] = $count_row['count'];
}
?>

<h5 class="text-center fw-bold mb-3"><?= htmlspecialchars($pageTitle) ?></h5>

<div class="container">
    <div class="row">
        <?php foreach ($regions as $row): ?>
            <?php
            $institution_count = $institution_counts[$row['id_region']] ?? 0;
            
            // Only display regions with institutions
            if ($institution_count > 0):
            ?>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/<?= htmlspecialchars($linkPrefix) ?>-in-region/<?= htmlspecialchars($row['region_name_en']) ?>" 
                       class="text-decoration-none d-block p-3 border rounded hover-shadow">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><?= htmlspecialchars($row['region_name']) ?></span>
                            <span class="badge bg-primary"><?= number_format($institution_count) ?></span>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
</style>
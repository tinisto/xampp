<?php
// Get type from parent file or default
$type = $type ?? $_GET['type'] ?? 'schools';

// Set variables based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $linkPrefix = '/spo-in-region';
        $pageTitle = $pageTitle ?? 'СПО по регионам';
        break;
    case 'vpo':
        $table = 'vpo';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = $pageTitle ?? 'ВПО по регионам';
        break;
    default:
        $table = 'schools';
        $linkPrefix = '/schools-in-region';
        $pageTitle = $pageTitle ?? 'Школы по регионам';
        break;
}

$sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$result = $connection->query($sql);

if (!$result) {
    header("Location: /error");
    exit();
}
?>

<h5 class="text-center fw-bold mb-3"><?= $pageTitle ?></h5>

<div class="container">
    <div class="row">
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
            <div class="col-md-4">
                <div class="region" data-region-id="<?= $row['id_region'] ?>">
                    <h6 class="text-lg font-bold">
                        <a href="<?= $linkPrefix ?>/<?= $row['region_name_en'] ?>"
                            class="text-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                            <?= $row['region_name'] ?>
                        </a>
                        <span class="badge bg-secondary rounded-pill ms-2"><?= $institution_count ?></span>
                    </h6>
                </div>
            </div>
        <?php 
            endif;
        endwhile; 
        ?>
    </div>
    
    <?php if ($displayed_count == 0): ?>
        <div class="text-center mt-4">
            <p>В данный момент нет доступных учебных заведений.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$connection->close();
?>
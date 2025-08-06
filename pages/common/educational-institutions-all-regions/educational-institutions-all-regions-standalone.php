<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $linkPrefix = '/spo-in-region';
        $pageTitle = 'СПО по регионам';
        break;
    case 'vpo':
        $table = 'vpo';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = 'ВПО по регионам';
        break;
    default: // schools
        $table = 'schools';
        $linkPrefix = '/schools-in-region';
        $pageTitle = 'Школы по регионам';
        break;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/header.php'; ?>
    
    <main class="container py-4">
        <h5 class="text-center fw-bold mb-3"><?= $pageTitle ?></h5>
        
        <div class="row">
            <?php 
            $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
            $result = $connection->query($sql);

            if (!$result) {
                echo "<p>Error loading regions</p>";
                exit();
            }

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
                <div class="col-md-4 mb-3">
                    <div class="region p-3" data-region-id="<?= $row['id_region'] ?>">
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
    </main>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/common-components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$connection->close();
?>
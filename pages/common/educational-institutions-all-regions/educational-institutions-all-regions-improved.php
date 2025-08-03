<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get the type from URL parameter
$type = $_GET['type'] ?? 'schools';

// Define table and field names based on type
switch ($type) {
    case 'spo':
        $table = 'spo';
        $linkPrefix = '/spo-in-region';
        $pageTitle = 'СПО по регионам';
        $metaD = 'Средние профессиональные образовательные учреждения (СПО) по регионам России';
        $metaK = 'СПО, колледжи, техникумы, регионы, среднее профессиональное образование';
        break;
    case 'vpo':
        $table = 'vpo';
        $linkPrefix = '/vpo-in-region';
        $pageTitle = 'ВПО по регионам';
        $metaD = 'Высшие учебные заведения (ВПО) по регионам России';
        $metaK = 'ВПО, университеты, институты, регионы, высшее образование';
        break;
    default: // schools
        $table = 'schools';
        $linkPrefix = '/schools-in-region';
        $pageTitle = 'Школы по регионам';
        $metaD = 'Школы по регионам России';
        $metaK = 'школы, регионы, среднее образование';
        break;
}

// Generate content in a function to avoid variable scope issues
function generateRegionsContent($table, $linkPrefix, $pageTitle) {
    global $connection;
    
    ob_start();
    ?>
    <div class="container py-4">
        <h1 class="section-title text-center"><?= $pageTitle ?></h1>
        
        <div class="row g-3">
            <?php 
            $sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
            $result = $connection->query($sql);

            if (!$result) {
                echo '<div class="col-12"><div class="alert alert-danger">Ошибка загрузки данных</div></div>';
                return ob_get_clean();
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
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 region-card">
                        <div class="card-body d-flex justify-content-between align-items-center">
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
    <?php
    return ob_get_clean();
}

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

// Generate content
$content = generateRegionsContent($table, $linkPrefix, $pageTitle);

// Create a temporary file to hold the content
$tempFile = tempnam(sys_get_temp_dir(), 'content_');
file_put_contents($tempFile, $content);

// Render template with the temporary content file
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $tempFile, $templateConfig, $metaD, $metaK);

// Clean up temp file
unlink($tempFile);
?>
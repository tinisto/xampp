<?php
// Schools all regions page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Type is always 'schools' for this file
$type = 'schools';
$table = 'schools';
$countField = 'schools_count';
$linkPrefix = '/schools-in-region';
$pageTitle = 'Школы по регионам';
$metaD = 'Общеобразовательные учреждения (школы) по регионам России';
$metaK = 'школы, регионы, среднее образование, общеобразовательные учреждения';
$regionColumn = 'region_id';
$idColumn = 'id_school';

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($pageTitle, [
    'fontSize' => '36px',
    'margin' => '30px 0',
    'subtitle' => 'Выберите регион для просмотра школ'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty for this page
$greyContent2 = '';

// Section 3: Statistics
ob_start();
?>
<div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; padding: 20px;">
    <?php
    // Get total schools count
    $totalQuery = "SELECT COUNT(*) as total FROM $table";
    $totalResult = mysqli_query($connection, $totalQuery);
    $totalSchools = mysqli_fetch_assoc($totalResult)['total'];
    
    // Get regions count
    $regionsQuery = "SELECT COUNT(DISTINCT $regionColumn) as total FROM $table";
    $regionsResult = mysqli_query($connection, $regionsQuery);
    $totalRegions = mysqli_fetch_assoc($regionsResult)['total'];
    ?>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;"><?= number_format($totalSchools) ?></div>
        <div style="font-size: 16px; color: #666;">Всего школ</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;"><?= number_format($totalRegions) ?></div>
        <div style="font-size: 16px; color: #666;">Регионов</div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Search
ob_start();
echo '<div style="text-align: center; padding: 20px;">';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск региона...',
    'buttonText' => 'Найти',
    'width' => '400px'
]);
echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Main content - regions grid
ob_start();
// Get regions data with school counts
$query = "SELECT 
            r.id_region, 
            r.title_region, 
            r.url_region,
            COUNT(s.$idColumn) as $countField,
            (SELECT COUNT(*) FROM users WHERE region_id = r.id_region) as user_count
          FROM regions r
          LEFT JOIN $table s ON r.id_region = s.$regionColumn
          GROUP BY r.id_region
          HAVING $countField > 0
          ORDER BY r.title_region ASC";

$result = mysqli_query($connection, $query);
$regions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $regions[] = $row;
}

// Display regions in a grid
?>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">
    <?php foreach ($regions as $region): ?>
        <a href="<?= htmlspecialchars($linkPrefix . '/' . $region['url_region']) ?>" style="text-decoration: none;">
            <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 20px; transition: all 0.3s ease; cursor: pointer;"
                 onmouseover="this.style.background='#e9ecef'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'"
                 onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <h3 style="margin: 0 0 10px 0; color: #333; font-size: 18px;"><?= htmlspecialchars($region['title_region']) ?></h3>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="color: #28a745; font-weight: 600; font-size: 24px;"><?= number_format($region[$countField]) ?></span>
                        <span style="color: #666; font-size: 14px;">школ</span>
                    </div>
                    <i class="fas fa-arrow-right" style="color: #28a745;"></i>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6 & 7: Empty for regions listing
$greyContent6 = '';
$blueContent = '';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
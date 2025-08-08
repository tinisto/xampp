<?php
/**
 * Educational Institutions All Regions - Real Template Version
 * Migrated to use real_template.php
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get the type from URL parameter or router
$type = $_GET['type'] ?? $institutionType ?? 'schools';

// Define settings based on type
switch ($type) {
    case 'spo':
        $tableName = 'spo';
        $nameField = 'name_spo';
        $urlField = 'url_spo';
        $regionField = 'region_spo';
        $pageTitle = 'СПО всех регионов';
        $subtitle = 'Средние профессиональные образовательные учреждения';
        break;
    case 'vpo':
        $tableName = 'vpo';
        $nameField = 'name_vpo';
        $urlField = 'url_vpo';
        $regionField = 'region_vpo';
        $pageTitle = 'ВПО всех регионов';
        $subtitle = 'Высшие профессиональные образовательные учреждения';
        break;
    default: // schools
        $tableName = 'schools';
        $nameField = 'name_school';
        $urlField = 'url_school';
        $regionField = 'region_school';
        $pageTitle = 'Школы всех регионов';
        $subtitle = 'Общеобразовательные учреждения';
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($pageTitle, [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => $subtitle
]);
$greyContent1 = ob_get_clean();

// Section 2: Navigation (empty for this page)
$greyContent2 = '';

// Section 3: Empty
$greyContent3 = '';

// Section 4: Search
ob_start();
echo '<div style="text-align: center; padding: 20px;">';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск по регионам...',
    'buttonText' => 'Найти'
]);
echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Regions grid
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php
        // Get regions with counts
        $query = "SELECT $regionField as region, COUNT(*) as count 
                  FROM $tableName 
                  WHERE status = 'active' 
                  GROUP BY $regionField 
                  ORDER BY $regionField";
        
        $result = mysqli_query($connection, $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $regionUrl = strtolower(str_replace([' ', 'ё'], ['-', 'е'], $row['region']));
            ?>
            <a href="/<?= $type ?>-in-region/<?= htmlspecialchars($regionUrl) ?>" 
               style="text-decoration: none; color: inherit;">
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; 
                            text-align: center; transition: all 0.3s ease;
                            background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; color: #333; font-size: 18px;">
                        <?= htmlspecialchars($row['region']) ?>
                    </h3>
                    <div style="color: #28a745; font-weight: 600; font-size: 16px;">
                        <?= $row['count'] ?> <?= $type === 'schools' ? 'школ' : ($type === 'spo' ? 'СПО' : 'ВУЗов') ?>
                    </div>
                </div>
            </a>
            <?php
        }
        ?>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
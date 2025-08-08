<?php
/**
 * Educational Institutions All Regions - Fixed Region Display
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

$regionsData = [];
$foundRealData = false;
$showDemoNotice = false;

// Try to get real data from database
if ($connection && !$connection->connect_error) {
    try {
        // Check if table exists
        $checkQuery = "SHOW TABLES LIKE '$tableName'";
        $checkResult = mysqli_query($connection, $checkQuery);
        
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            // First, let's check if there's a regions table
            $regionsTable = null;
            $regionsTables = ['regions', 'region', 'oblast', 'subjects'];
            
            foreach ($regionsTables as $testTable) {
                $checkRegionsQuery = "SHOW TABLES LIKE '$testTable'";
                $checkRegionsResult = mysqli_query($connection, $checkRegionsQuery);
                if ($checkRegionsResult && mysqli_num_rows($checkRegionsResult) > 0) {
                    $regionsTable = $testTable;
                    break;
                }
            }
            
            // Check columns in main table
            $columnsQuery = "SHOW COLUMNS FROM $tableName";
            $columnsResult = mysqli_query($connection, $columnsQuery);
            $columns = [];
            while ($col = mysqli_fetch_assoc($columnsResult)) {
                $columns[] = $col['Field'];
            }
            
            // Find the right region field name
            $actualRegionField = null;
            foreach ($columns as $col) {
                if (stripos($col, 'region') !== false) {
                    $actualRegionField = $col;
                    break;
                }
            }
            
            if ($actualRegionField) {
                if ($regionsTable) {
                    // Try to join with regions table
                    $query = "SELECT r.name as region_name, r.id as region_id, COUNT(main.$actualRegionField) as count 
                              FROM $regionsTable r 
                              LEFT JOIN $tableName main ON main.$actualRegionField = r.id 
                              GROUP BY r.id, r.name 
                              HAVING count > 0
                              ORDER BY r.name 
                              LIMIT 50";
                } else {
                    // Direct query - check if region field contains text or numbers
                    $sampleQuery = "SELECT $actualRegionField FROM $tableName LIMIT 5";
                    $sampleResult = mysqli_query($connection, $sampleQuery);
                    $isNumeric = true;
                    
                    while ($sample = mysqli_fetch_assoc($sampleResult)) {
                        if (!is_numeric($sample[$actualRegionField]) && !empty($sample[$actualRegionField])) {
                            $isNumeric = false;
                            break;
                        }
                    }
                    
                    if (!$isNumeric) {
                        // Direct text regions
                        $query = "SELECT $actualRegionField as region_name, COUNT(*) as count 
                                  FROM $tableName 
                                  WHERE $actualRegionField IS NOT NULL AND $actualRegionField != ''
                                  GROUP BY $actualRegionField 
                                  ORDER BY $actualRegionField 
                                  LIMIT 50";
                    } else {
                        // Numeric regions - use sample data
                        $query = null;
                    }
                }
                
                if ($query) {
                    $result = mysqli_query($connection, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            if (!empty($row['region_name']) && $row['region_name'] != '1') {
                                $regionsData[] = [
                                    'region' => $row['region_name'],
                                    'count' => $row['count']
                                ];
                                $foundRealData = true;
                            }
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Will use fallback data
    }
}

// If no real data found or data is invalid, use sample data
if (empty($regionsData) || !$foundRealData) {
    $showDemoNotice = true;
    switch ($type) {
        case 'vpo':
            $regionsData = [
                ['region' => 'Москва', 'count' => 127],
                ['region' => 'Санкт-Петербург', 'count' => 89],
                ['region' => 'Московская область', 'count' => 45],
                ['region' => 'Новосибирская область', 'count' => 32],
                ['region' => 'Свердловская область', 'count' => 28],
                ['region' => 'Ростовская область', 'count' => 24],
                ['region' => 'Республика Татарстан', 'count' => 23],
                ['region' => 'Краснодарский край', 'count' => 21],
                ['region' => 'Челябинская область', 'count' => 19],
                ['region' => 'Нижегородская область', 'count' => 18],
                ['region' => 'Самарская область', 'count' => 17],
                ['region' => 'Воронежская область', 'count' => 15],
                ['region' => 'Красноярский край', 'count' => 14],
                ['region' => 'Пермский край', 'count' => 13],
                ['region' => 'Волгоградская область', 'count' => 12]
            ];
            break;
        case 'spo':
            $regionsData = [
                ['region' => 'Москва', 'count' => 89],
                ['region' => 'Санкт-Петербург', 'count' => 67],
                ['region' => 'Московская область', 'count' => 56],
                ['region' => 'Краснодарский край', 'count' => 43],
                ['region' => 'Ростовская область', 'count' => 38],
                ['region' => 'Свердловская область', 'count' => 35],
                ['region' => 'Республика Татарстан', 'count' => 34],
                ['region' => 'Новосибирская область', 'count' => 31],
                ['region' => 'Челябинская область', 'count' => 29],
                ['region' => 'Нижегородская область', 'count' => 27],
                ['region' => 'Самарская область', 'count' => 25],
                ['region' => 'Волгоградская область', 'count' => 22],
                ['region' => 'Красноярский край', 'count' => 21],
                ['region' => 'Пермский край', 'count' => 19],
                ['region' => 'Республика Башкортостан', 'count' => 18]
            ];
            break;
        default: // schools
            $regionsData = [
                ['region' => 'Москва', 'count' => 1243],
                ['region' => 'Санкт-Петербург', 'count' => 867],
                ['region' => 'Московская область', 'count' => 2156],
                ['region' => 'Краснодарский край', 'count' => 1432],
                ['region' => 'Ростовская область', 'count' => 1287],
                ['region' => 'Свердловская область', 'count' => 1098],
                ['region' => 'Республика Татарстан', 'count' => 987],
                ['region' => 'Республика Башкортостан', 'count' => 934],
                ['region' => 'Новосибирская область', 'count' => 876],
                ['region' => 'Челябинская область', 'count' => 823],
                ['region' => 'Нижегородская область', 'count' => 789],
                ['region' => 'Самарская область', 'count' => 745],
                ['region' => 'Красноярский край', 'count' => 712],
                ['region' => 'Пермский край', 'count' => 678],
                ['region' => 'Волгоградская область', 'count' => 645]
            ];
    }
}

?>
<div style="padding: 20px;">
    <?php if ($showDemoNotice): ?>
    <div style="background: #e3f2fd; border: 1px solid #1976d2; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: center;">
        <p style="margin: 0; color: #1976d2;"><strong>Демо-режим:</strong> Показаны образцы регионов. Для отображения реальных данных необходимо настроить базу данных.</p>
    </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php foreach ($regionsData as $row): ?>
            <?php 
            $regionUrl = strtolower(str_replace([' ', 'ё', 'область', 'край', 'республика'], ['-', 'е', '', '', ''], trim($row['region'])));
            $regionUrl = preg_replace('/[^a-z0-9-]/', '', $regionUrl);
            $regionUrl = trim($regionUrl, '-');
            ?>
            <a href="/<?= $type ?>-in-region/<?= htmlspecialchars($regionUrl) ?>" 
               style="text-decoration: none; color: inherit; transition: transform 0.2s;">
                <div style="border: 1px solid #ddd; border-radius: 12px; padding: 25px; 
                            text-align: center; transition: all 0.3s ease;
                            background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 15px 0; color: #333; font-size: 18px; font-weight: 600; line-height: 1.4;">
                        <?= htmlspecialchars($row['region']) ?>
                    </h3>
                    <div style="color: #28a745; font-weight: 700; font-size: 24px; margin-bottom: 5px;">
                        <?= $row['count'] ?>
                    </div>
                    <div style="color: #666; font-size: 14px;">
                        <?= $type === 'schools' ? 'школ' : ($type === 'spo' ? 'учреждений СПО' : 'ВУЗов') ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($regionsData)): ?>
    <div style="text-align: center; padding: 60px; color: #666;">
        <i class="fas fa-map-marker-alt fa-4x" style="opacity: 0.3; margin-bottom: 20px;"></i>
        <h3 style="color: #999; margin-bottom: 10px;">Данные по регионам не найдены</h3>
        <p>Обратитесь к администратору для настройки базы данных.</p>
    </div>
    <?php endif; ?>
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
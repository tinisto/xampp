#!/usr/bin/env python3
"""Create new versions of VPO/SPO/Tests pages using real_template.php"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# New page files that use real_template.php
new_pages = {
    'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-real.php': '''<?php
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
?>''',
    
    'pages/tests/tests-main-real.php': '''<?php
/**
 * Tests Main Page - Real Template Version
 * Migrated to use real_template.php
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Онлайн тесты', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'Проверьте свои знания'
]);
$greyContent1 = ob_get_clean();

// Section 2: Category Navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
$testNavItems = [
    ['title' => 'Все тесты', 'url' => '/tests'],
    ['title' => 'Математика', 'url' => '/tests/math'],
    ['title' => 'Русский язык', 'url' => '/tests/russian'],
    ['title' => 'Физика', 'url' => '/tests/physics'],
    ['title' => 'Химия', 'url' => '/tests/chemistry'],
    ['title' => 'Биология', 'url' => '/tests/biology']
];
renderCategoryNavigation($testNavItems, $_SERVER['REQUEST_URI']);
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Filters and Search
ob_start();
echo '<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px;">';

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/filters-dropdown.php';
renderFiltersDropdown([
    'sortOptions' => [
        'difficulty_easy' => 'Легкие тесты',
        'difficulty_medium' => 'Средние тесты', 
        'difficulty_hard' => 'Сложные тесты',
        'popular' => 'По популярности'
    ]
]);

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Поиск тестов...',
    'buttonText' => 'Найти'
]);

echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Tests Grid
ob_start();

// Get tests
$query = "SELECT t.*, c.title_category, c.url_category 
          FROM tests t 
          LEFT JOIN categories c ON t.category_id = c.id_category 
          WHERE t.status = 'active' 
          ORDER BY t.created_at DESC 
          LIMIT 12";

$result = mysqli_query($connection, $query);
$testsItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $testsItems[] = $row;
}

if (count($testsItems) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($testsItems, 'test', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 40px; color: #666;">
            <i class="fas fa-clipboard-list fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p>Тесты не найдены</p>
          </div>';
}
$greyContent5 = ob_get_clean();

// Section 6: Pagination (if needed)
$greyContent6 = '';

// Section 7: No comments
$blueContent = '';

// Set page title
$pageTitle = 'Онлайн тесты - 11-классники';

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
}

try:
    print("Creating new template-based pages...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    for filepath, content in new_pages.items():
        # Save locally
        filename = filepath.split('/')[-1]
        with open(filename, 'w') as f:
            f.write(content)
        
        # Upload
        try:
            with open(filename, 'rb') as f:
                ftp.storbinary(f'STOR {filepath}', f)
            print(f"✓ Created {filepath}")
        except Exception as e:
            print(f"✗ Failed to upload {filepath}: {e}")
    
    # Now update the router files to use these new pages
    updated_routers = {
        'vpo-all-regions-new.php': '''<?php
// VPO All Regions router - updated
error_reporting(0);

// Set type for the page
$_GET['type'] = 'vpo';
$institutionType = 'vpo';

// Include the NEW real template version
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-real.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>ВПО всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'ВПО всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>''',
        
        'spo-all-regions-new.php': '''<?php
// SPO All Regions router - updated  
error_reporting(0);

// Set type for the page
$_GET['type'] = 'spo';
$institutionType = 'spo';

// Include the NEW real template version
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-real.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>СПО всех регионов</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'СПО всех регионов - 11-классники';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>''',
        
        'tests-new.php': '''<?php
// Tests router - updated
error_reporting(0);

// Check for single test
$testUrl = $_GET['url_test'] ?? '';

if (!empty($testUrl)) {
    // Single test page - use existing single test handler
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/test.php';
    if (file_exists($pageFile)) {
        include $pageFile;
    }
} else {
    // Tests listing - use NEW real template version
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/tests-main-real.php';
    if (file_exists($pageFile)) {
        include $pageFile;
    } else {
        // Fallback
        $greyContent1 = '<div style="padding: 30px;"><h1>Тесты</h1></div>';
        $greyContent2 = '';
        $greyContent3 = '';
        $greyContent4 = '';
        $greyContent5 = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
        $greyContent6 = '';
        $blueContent = '';
        $pageTitle = 'Тесты - 11-классники';
        
        include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
    }
}
?>'''
    }
    
    # Upload updated router files
    for filename, content in updated_routers.items():
        with open(filename, 'w') as f:
            f.write(content)
        
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR {filename}', f)
        print(f"✓ Updated {filename}")
    
    ftp.quit()
    
    print("\n✅ New template-based pages created!")
    print("\nNow these pages should use the real template:")
    print("- https://11klassniki.ru/vpo-all-regions")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/tests")
    
except Exception as e:
    print(f"Error: {e}")
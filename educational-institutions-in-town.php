<?php
// Educational institutions in town page - migrated to use real_template.php

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameters
$type = $_GET['type'] ?? '';
$region_name_en = $_GET['region_name_en'] ?? '';
$url_slug_town = $_GET['url_slug_town'] ?? '';

// Validate institution type
if (!in_array($type, ['schools', 'spo', 'vpo'])) {
    header("Location: /404");
    exit();
}

// Fetch town data
$query_towns = "SELECT t.*, r.region_name 
                FROM towns t 
                JOIN regions r ON t.id_region = r.id_region 
                WHERE t.url_slug_town = ? AND r.region_name_en = ?";
$stmt = $connection->prepare($query_towns);
$stmt->bind_param("ss", $url_slug_town, $region_name_en);
$stmt->execute();
$result_towns = $stmt->get_result();
$town_data = $result_towns->fetch_assoc();
$stmt->close();

if (!$town_data) {
    header("Location: /404");
    exit();
}

$town_name = $town_data['name'];
$town_id = $town_data['id_town'];
$region_name = $town_data['region_name'];

// Set page title based on type
switch ($type) {
    case 'schools':
        $pageTitle = 'Школы — ' . $town_name;
        $type_label = 'Школы';
        $type_plural = 'школ';
        break;
    case 'spo':
        $pageTitle = 'Колледжи / Техникумы — ' . $town_name;
        $type_label = 'СПО';
        $type_plural = 'колледжей';
        break;
    case 'vpo':
        $pageTitle = 'Университеты / Институты — ' . $town_name;
        $type_label = 'ВПО';
        $type_plural = 'вузов';
        break;
}

// Fetch institutions
$table_name = $type;
$query_institutions = "SELECT * FROM $table_name WHERE id_town = ? ORDER BY name ASC";
$stmt = $connection->prepare($query_institutions);
$stmt->bind_param("i", $town_id);
$stmt->execute();
$result_institutions = $stmt->get_result();
$institutions = $result_institutions->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_count = count($institutions);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($pageTitle, [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => $region_name . ' • Найдено ' . $total_count . ' ' . $type_plural
]);
$greyContent1 = ob_get_clean();

// Section 2: Navigation links
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="/schools-all-regions" class="type-nav-item <?= $type === 'schools' ? 'active' : '' ?>">
            <i class="fas fa-school"></i> Школы
        </a>
        <a href="/spo-all-regions" class="type-nav-item <?= $type === 'spo' ? 'active' : '' ?>">
            <i class="fas fa-university"></i> СПО
        </a>
        <a href="/vpo-all-regions" class="type-nav-item <?= $type === 'vpo' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i> ВПО
        </a>
    </div>
</div>

<style>
.type-nav-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--surface, #f8f9fa);
    color: var(--text-primary, #333);
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s;
    font-weight: 500;
}

.type-nav-item:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
}

.type-nav-item.active {
    background: #28a745;
    color: white;
}

[data-theme="dark"] .type-nav-item {
    background: var(--surface-dark, #2d3748);
    color: var(--text-primary, #e4e6eb);
}
</style>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Breadcrumb
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
renderBreadcrumb([
    ['/', 'Главная'],
    ['/' . $type . '-all-regions', $type_label . ' России'],
    ['/' . $type . '/' . $region_name_en, $type_label . ' ' . $region_name],
    ['#', $town_name]
]);
$greyContent3 = ob_get_clean();

// Section 4: Search box
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline('Поиск по учебным заведениям...', 'Найти');
$greyContent4 = ob_get_clean();

// Section 5: Institutions grid
ob_start();
?>
<div style="padding: 20px;">
    <?php if (empty($institutions)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-search" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-primary, #333);">Учебные заведения не найдены</h3>
            <p style="color: var(--text-secondary, #666);">В городе <?= htmlspecialchars($town_name) ?> пока нет зарегистрированных учебных заведений этого типа.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ($institutions as $institution): ?>
                <?php
                // Determine URL based on type
                switch ($type) {
                    case 'schools':
                        $inst_url = '/school/' . $institution['url_slug'];
                        $icon = 'fa-school';
                        break;
                    case 'spo':
                        $inst_url = '/spo/' . $institution['url_slug'];
                        $icon = 'fa-university';
                        break;
                    case 'vpo':
                        $inst_url = '/vpo/' . $institution['url_slug'];
                        $icon = 'fa-graduation-cap';
                        break;
                }
                ?>
                <a href="<?= htmlspecialchars($inst_url) ?>" class="institution-card">
                    <div class="institution-icon">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="institution-info">
                        <h4><?= htmlspecialchars($institution['name']) ?></h4>
                        <?php if (!empty($institution['address'])): ?>
                            <p class="address">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($institution['address']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($institution['phone'])): ?>
                            <p class="phone">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($institution['phone']) ?>
                            </p>
                        <?php endif; ?>
                        <span class="view-more">Подробнее →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.institution-card {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: var(--surface, #ffffff);
    border-radius: 12px;
    text-decoration: none;
    color: var(--text-primary, #333);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.institution-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.institution-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    width: 60px;
    height: 60px;
    background: #28a745;
    color: white;
    border-radius: 12px;
    font-size: 24px;
}

.institution-info {
    flex: 1;
}

.institution-info h4 {
    margin: 0 0 10px 0;
    color: var(--text-primary, #333);
    font-size: 18px;
    line-height: 1.4;
}

.institution-info p {
    margin: 5px 0;
    color: var(--text-secondary, #666);
    font-size: 14px;
}

.institution-info p i {
    width: 16px;
    margin-right: 8px;
    color: #28a745;
}

.view-more {
    display: inline-block;
    margin-top: 10px;
    color: #28a745;
    font-weight: 500;
}

[data-theme="dark"] .institution-card {
    background: var(--surface-dark, #2d3748);
}

[data-theme="dark"] .institution-info h4 {
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] .institution-info p {
    color: var(--text-secondary, #b0b3b8);
}

@media (max-width: 768px) {
    .institution-card {
        flex-direction: column;
        text-align: center;
    }
    
    .institution-icon {
        margin: 0 auto;
    }
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Empty pagination (if needed in future)
$greyContent6 = '';

// Blue section: Empty
$blueContent = '';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
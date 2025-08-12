<?php
// Unified educational institution single page (Schools/VPO/SPO) - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Determine institution type from URL
$urlPath = $_SERVER['REQUEST_URI'];
$type = '';
if (strpos($urlPath, '/school/') !== false) {
    $type = 'school';
} elseif (strpos($urlPath, '/vpo/') !== false) {
    $type = 'vpo';
} elseif (strpos($urlPath, '/spo/') !== false) {
    $type = 'spo';
} else {
    header("Location: /404");
    exit();
}

// Get URL slug from parameter
$urlSlug = $_GET['url_slug'] ?? '';
if (empty($urlSlug)) {
    header("Location: /" . $type . "s-all-regions");
    exit();
}

// Configure based on type
switch ($type) {
    case 'vpo':
        $table = 'vpo';
        $titleField = 'title_vpo';
        $urlField = 'url_vpo';
        $descField = 'description_vpo';
        $viewField = 'view_vpo';
        $addressField = 'address_vpo';
        $cityField = 'city_vpo';
        $fullNameField = 'full_name_vpo';
        $typeField = 'type_vpo';
        $entityType = 'vpo';
        $pageTypeTitle = 'ВУЗ';
        $allRegionsTitle = 'ВУЗы по регионам';
        $allRegionsUrl = '/vpo-all-regions';
        $inRegionUrl = '/vpo-in-region/';
        break;
    case 'spo':
        $table = 'spo';
        $titleField = 'title_spo';
        $urlField = 'url_spo';
        $descField = 'description_spo';
        $viewField = 'view_spo';
        $addressField = 'address_spo';
        $cityField = 'city_spo';
        $fullNameField = 'full_name_spo';
        $typeField = 'type_spo';
        $entityType = 'spo';
        $pageTypeTitle = 'СПО';
        $allRegionsTitle = 'СПО по регионам';
        $allRegionsUrl = '/spo-all-regions';
        $inRegionUrl = '/spo-in-region/';
        break;
    default: // school
        $table = 'schools';
        $titleField = 'title_school';
        $urlField = 'url_school';
        $descField = 'description_school';
        $viewField = 'view_school';
        $addressField = 'address_school';
        $cityField = 'city_school';
        $fullNameField = 'full_name_school';
        $typeField = 'type_school';
        $entityType = 'school';
        $pageTypeTitle = 'Школа';
        $allRegionsTitle = 'Школы по регионам';
        $allRegionsUrl = '/schools-all-regions';
        $inRegionUrl = '/schools-in-region/';
        break;
}

// Get institution data
$query = "SELECT t.*, r.title_region, r.url_region, tn.title_town,
                 (SELECT COUNT(*) FROM comments WHERE entity_type = ? AND entity_id = t.id) as comment_count
          FROM $table t
          LEFT JOIN regions r ON t.region_id = r.id_region
          LEFT JOIN towns tn ON t.town_id = tn.id_town
          WHERE t.$urlField = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("ss", $entityType, $urlSlug);
$stmt->execute();
$result = $stmt->get_result();
$institution = $result->fetch_assoc();

if (!$institution) {
    header("Location: /404");
    exit();
}

// Update view count
$updateQuery = "UPDATE $table SET $viewField = $viewField + 1 WHERE id = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("i", $institution['id']);
$updateStmt->execute();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($institution[$titleField], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
$breadcrumbItems = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => $allRegionsTitle, 'url' => $allRegionsUrl]
];
if ($institution['title_region']) {
    $breadcrumbItems[] = ['text' => $institution['title_region'], 'url' => $inRegionUrl . $institution['url_region']];
}
$breadcrumbItems[] = ['text' => $institution[$titleField]];
renderBreadcrumb($breadcrumbItems);
$greyContent2 = ob_get_clean();

// Section 3: Metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
        <?php if ($institution[$cityField] || $institution['title_town']): ?>
            <div>
                <i class="fas fa-map-marker-alt" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($institution[$cityField] ?: $institution['title_town']) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($institution['title_region']): ?>
            <div>
                <i class="fas fa-globe" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($institution['title_region']) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <i class="fas fa-eye" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= number_format($institution[$viewField] ?? 0) ?> просмотров</span>
        </div>
        <div>
            <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $institution['comment_count'] ?> комментариев</span>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Tabs
ob_start();
?>
<div style="padding: 20px;">
    <div style="display: flex; gap: 20px; border-bottom: 2px solid #eee; margin-bottom: 20px;">
        <button onclick="showTab('info')" id="tab-info" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid #28a745; font-weight: 600; color: #28a745; cursor: pointer;">
            Общая информация
        </button>
        <button onclick="showTab('contacts')" id="tab-contacts" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #666; cursor: pointer;">
            Контакты
        </button>
        <?php if ($type === 'vpo' || $type === 'spo'): ?>
            <button onclick="showTab('admission')" id="tab-admission" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #666; cursor: pointer;">
                Поступление
            </button>
        <?php else: ?>
            <button onclick="showTab('administration')" id="tab-administration" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #666; cursor: pointer;">
                Администрация
            </button>
        <?php endif; ?>
    </div>
</div>
<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('[id^="tab-"]').forEach(b => {
        b.style.borderBottomColor = 'transparent';
        b.style.color = '#666';
    });
    
    document.getElementById('content-' + tab).style.display = 'block';
    document.getElementById('tab-' + tab).style.borderBottomColor = '#28a745';
    document.getElementById('tab-' + tab).style.color = '#28a745';
}
</script>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Main content - THIS IS WHERE THE DIFFERENCE IS
ob_start();
?>
<div style="padding: 0 20px 30px 20px;">
    <!-- Info Tab -->
    <div id="content-info" class="tab-content" style="display: block;">
        <?php if ($institution[$descField]): ?>
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Описание</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($institution[$descField])) ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <?php if ($institution[$typeField]): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Тип учреждения</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($institution[$typeField]) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($institution['founded_year']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Год основания</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($institution['founded_year']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($institution['students_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество <?= $type === 'school' ? 'учеников' : 'студентов' ?></h4>
                    <p style="color: #666; margin: 0;"><?= number_format($institution['students_count']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($institution['teachers_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество <?= $type === 'school' ? 'учителей' : 'преподавателей' ?></h4>
                    <p style="color: #666; margin: 0;"><?= number_format($institution['teachers_count']) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($institution[$fullNameField] && $institution[$fullNameField] !== $institution[$titleField]): ?>
            <div style="margin-top: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Полное наименование</h3>
                <p style="color: #666; line-height: 1.6;"><?= htmlspecialchars($institution[$fullNameField]) ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contacts Tab -->
    <div id="content-contacts" class="tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php if ($institution[$addressField]): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Адрес</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($institution[$addressField]) ?></p>
                    <?php if ($institution['zip_code']): ?>
                        <p style="color: #666; margin: 5px 0 0 0;">Индекс: <?= htmlspecialchars($institution['zip_code']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($institution['tel']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-phone" style="margin-right: 8px;"></i>Телефон</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($institution['tel']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($institution['email']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-envelope" style="margin-right: 8px;"></i>Email</h4>
                    <p style="color: #666; margin: 0;"><a href="mailto:<?= htmlspecialchars($institution['email']) ?>" style="color: #28a745;"><?= htmlspecialchars($institution['email']) ?></a></p>
                </div>
            <?php endif; ?>
            
            <?php if ($institution['site']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-globe" style="margin-right: 8px;"></i>Сайт</h4>
                    <p style="color: #666; margin: 0;"><a href="<?= htmlspecialchars($institution['site']) ?>" target="_blank" style="color: #28a745;"><?= htmlspecialchars($institution['site']) ?></a></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($institution['director_name']): ?>
            <div style="margin-top: 30px;">
                <h4 style="color: #333; margin: 0 0 15px 0;">Руководство</h4>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <p style="color: #333; margin: 0 0 10px 0; font-size: 18px; font-weight: 500;">
                        <?= htmlspecialchars($institution['director_name']) ?>
                    </p>
                    <p style="color: #666; margin: 0;"><?= $type === 'vpo' ? 'Ректор' : 'Директор' ?></p>
                    <?php if ($institution['director_phone']): ?>
                        <p style="color: #666; margin: 10px 0 0 0;">
                            <i class="fas fa-phone" style="width: 20px;"></i>
                            <?= htmlspecialchars($institution['director_phone']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($institution['director_email']): ?>
                        <p style="color: #666; margin: 5px 0 0 0;">
                            <i class="fas fa-envelope" style="width: 20px;"></i>
                            <a href="mailto:<?= htmlspecialchars($institution['director_email']) ?>" style="color: #28a745;">
                                <?= htmlspecialchars($institution['director_email']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Admission Tab (for VPO/SPO) or Administration Tab (for Schools) -->
    <?php if ($type === 'vpo' || $type === 'spo'): ?>
    <div id="content-admission" class="tab-content" style="display: none;">
        <?php if ($institution['admission_info']): ?>
            <div>
                <h3 style="color: #333; margin-bottom: 15px;">Информация о поступлении</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($institution['admission_info'])) ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <h4 style="color: #333; margin-bottom: 15px;">Приемная комиссия</h4>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <?php if ($institution['admission_phone']): ?>
                    <p style="color: #666; margin: 0 0 10px 0;">
                        <i class="fas fa-phone" style="width: 20px; color: #28a745;"></i>
                        <strong>Телефон:</strong> <?= htmlspecialchars($institution['admission_phone']) ?>
                    </p>
                <?php endif; ?>
                <?php if ($institution['admission_email']): ?>
                    <p style="color: #666; margin: 0 0 10px 0;">
                        <i class="fas fa-envelope" style="width: 20px; color: #28a745;"></i>
                        <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($institution['admission_email']) ?>" style="color: #28a745;"><?= htmlspecialchars($institution['admission_email']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if ($institution['admission_hours']): ?>
                    <p style="color: #666; margin: 0;">
                        <i class="fas fa-clock" style="width: 20px; color: #28a745;"></i>
                        <strong>Часы работы:</strong> <?= htmlspecialchars($institution['admission_hours']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div id="content-administration" class="tab-content" style="display: none;">
        <?php if ($institution['director_name']): ?>
            <div style="margin-bottom: 30px;">
                <h4 style="color: #333; margin: 0 0 15px 0;">Директор</h4>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <p style="color: #333; margin: 0 0 10px 0; font-size: 18px; font-weight: 500;">
                        <?= htmlspecialchars($institution['director_name']) ?>
                    </p>
                    <?php if ($institution['director_phone']): ?>
                        <p style="color: #666; margin: 5px 0;">
                            <i class="fas fa-phone" style="width: 20px;"></i>
                            <?= htmlspecialchars($institution['director_phone']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($institution['director_email']): ?>
                        <p style="color: #666; margin: 5px 0;">
                            <i class="fas fa-envelope" style="width: 20px;"></i>
                            <a href="mailto:<?= htmlspecialchars($institution['director_email']) ?>" style="color: #28a745;">
                                <?= htmlspecialchars($institution['director_email']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related institutions
ob_start();
if ($institution['region_id']) {
    $relatedQuery = "SELECT id, $titleField, $urlField, $cityField
                     FROM $table 
                     WHERE region_id = ? AND id != ? 
                     ORDER BY RAND() 
                     LIMIT 4";
    $stmt = $connection->prepare($relatedQuery);
    $stmt->bind_param("ii", $institution['region_id'], $institution['id']);
    $stmt->execute();
    $relatedResult = $stmt->get_result();
    $relatedItems = [];
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedItems[] = [
            'id_news' => $row['id'],
            'title_news' => $row[$titleField],
            'url_news' => $row[$urlField],
            'image_news' => '/images/default-' . $type . '.jpg',
            'created_at' => date('Y-m-d'),
            'category_title' => $row[$cityField] ?: 'Город не указан',
            'category_url' => '#'
        ];
    }

    if (count($relatedItems) > 0) {
        echo '<div style="padding: 20px;">';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle('Другие ' . ($type === 'school' ? 'школы' : ($type === 'vpo' ? 'ВУЗы' : 'СПО')) . ' региона', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
        
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
        renderCardsGrid($relatedItems, $type, [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => true
        ]);
        echo '</div>';
    }
}
$greyContent6 = ob_get_clean();

// Section 7: Comments (prepared but not implemented per user request)
ob_start();
?>
<div style="padding: 30px 20px; color: white;">
    <h3 style="margin: 0 0 20px 0;">Комментарии (<?= $institution['comment_count'] ?>)</h3>
    <!-- Comments will be added later per user request -->
</div>
<?php
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $institution[$titleField];
$metaD = $institution[$descField] ? substr($institution[$descField], 0, 160) : 'Информация о ' . $pageTypeTitle . ' ' . $institution[$titleField];
$metaK = $institution[$titleField] . ', ' . $pageTypeTitle . ', ' . $institution[$cityField] . ', ' . $institution['title_region'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
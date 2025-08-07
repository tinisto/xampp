<?php
// Single school page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get school URL from parameter
$schoolUrl = $_GET['url_slug'] ?? '';
if (empty($schoolUrl)) {
    header("Location: /schools-all-regions");
    exit();
}

// Get school data
$query = "SELECT s.*, r.title_region, r.url_region, t.title_town,
                 (SELECT COUNT(*) FROM comments WHERE entity_type = 'school' AND entity_id = s.id_school) as comment_count
          FROM schools s
          LEFT JOIN regions r ON s.region_id = r.id_region
          LEFT JOIN towns t ON s.town_id = t.id_town
          WHERE s.url_school = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $schoolUrl);
$stmt->execute();
$result = $stmt->get_result();
$school = $result->fetch_assoc();

if (!$school) {
    header("Location: /404");
    exit();
}

// Update view count
$updateQuery = "UPDATE schools SET view_school = view_school + 1 WHERE id_school = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("i", $school['id_school']);
$updateStmt->execute();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($school['title_school'], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
$breadcrumbItems = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'Школы по регионам', 'url' => '/schools-all-regions']
];
if ($school['title_region']) {
    $breadcrumbItems[] = ['text' => $school['title_region'], 'url' => '/schools-in-region/' . $school['url_region']];
}
$breadcrumbItems[] = ['text' => $school['title_school']];
renderBreadcrumb($breadcrumbItems);
$greyContent2 = ob_get_clean();

// Section 3: Metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
        <?php if ($school['city_school'] || $school['title_town']): ?>
            <div>
                <i class="fas fa-map-marker-alt" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($school['city_school'] ?: $school['title_town']) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($school['title_region']): ?>
            <div>
                <i class="fas fa-globe" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($school['title_region']) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <i class="fas fa-eye" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= number_format($school['view_school'] ?? 0) ?> просмотров</span>
        </div>
        <div>
            <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $school['comment_count'] ?> комментариев</span>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Contact info tabs
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
        <button onclick="showTab('administration')" id="tab-administration" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #666; cursor: pointer;">
            Администрация
        </button>
    </div>
</div>
<script>
function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('[id^="tab-"]').forEach(b => {
        b.style.borderBottomColor = 'transparent';
        b.style.color = '#666';
    });
    
    // Show selected tab
    document.getElementById('content-' + tab).style.display = 'block';
    document.getElementById('tab-' + tab).style.borderBottomColor = '#28a745';
    document.getElementById('tab-' + tab).style.color = '#28a745';
}
</script>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Main content
ob_start();
?>
<div style="padding: 0 20px 30px 20px;">
    <!-- Info Tab -->
    <div id="content-info" class="tab-content" style="display: block;">
        <?php if ($school['description_school']): ?>
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Описание</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($school['description_school'])) ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <?php if ($school['type_school']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Тип учреждения</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($school['type_school']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($school['founded_year']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Год основания</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($school['founded_year']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($school['students_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество учеников</h4>
                    <p style="color: #666; margin: 0;"><?= number_format($school['students_count']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($school['teachers_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество учителей</h4>
                    <p style="color: #666; margin: 0;"><?= number_format($school['teachers_count']) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($school['full_name_school'] && $school['full_name_school'] !== $school['title_school']): ?>
            <div style="margin-top: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Полное наименование</h3>
                <p style="color: #666; line-height: 1.6;"><?= htmlspecialchars($school['full_name_school']) ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contacts Tab -->
    <div id="content-contacts" class="tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php if ($school['address_school']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Адрес</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($school['address_school']) ?></p>
                    <?php if ($school['zip_code']): ?>
                        <p style="color: #666; margin: 5px 0 0 0;">Индекс: <?= htmlspecialchars($school['zip_code']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($school['tel']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-phone" style="margin-right: 8px;"></i>Телефон</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($school['tel']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($school['email']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-envelope" style="margin-right: 8px;"></i>Email</h4>
                    <p style="color: #666; margin: 0;"><a href="mailto:<?= htmlspecialchars($school['email']) ?>" style="color: #28a745;"><?= htmlspecialchars($school['email']) ?></a></p>
                </div>
            <?php endif; ?>
            
            <?php if ($school['site']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-globe" style="margin-right: 8px;"></i>Сайт</h4>
                    <p style="color: #666; margin: 0;"><a href="<?= htmlspecialchars($school['site']) ?>" target="_blank" style="color: #28a745;"><?= htmlspecialchars($school['site']) ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Administration Tab -->
    <div id="content-administration" class="tab-content" style="display: none;">
        <?php if ($school['director_name']): ?>
            <div style="margin-bottom: 30px;">
                <h4 style="color: #333; margin: 0 0 15px 0;">Директор</h4>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <p style="color: #333; margin: 0 0 10px 0; font-size: 18px; font-weight: 500;">
                        <?= htmlspecialchars($school['director_name']) ?>
                    </p>
                    <?php if ($school['director_phone']): ?>
                        <p style="color: #666; margin: 5px 0;">
                            <i class="fas fa-phone" style="width: 20px;"></i>
                            <?= htmlspecialchars($school['director_phone']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($school['director_email']): ?>
                        <p style="color: #666; margin: 5px 0;">
                            <i class="fas fa-envelope" style="width: 20px;"></i>
                            <a href="mailto:<?= htmlspecialchars($school['director_email']) ?>" style="color: #28a745;">
                                <?= htmlspecialchars($school['director_email']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related schools
ob_start();
// Get related schools from same region
if ($school['region_id']) {
    $relatedQuery = "SELECT id_school, title_school, url_school, city_school
                     FROM schools 
                     WHERE region_id = ? AND id_school != ? 
                     ORDER BY RAND() 
                     LIMIT 4";
    $stmt = $connection->prepare($relatedQuery);
    $stmt->bind_param("ii", $school['region_id'], $school['id_school']);
    $stmt->execute();
    $relatedResult = $stmt->get_result();
    $relatedSchools = [];
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedSchools[] = [
            'id_news' => $row['id_school'],
            'title_news' => $row['title_school'],
            'url_news' => $row['url_school'],
            'image_news' => '/images/default-school.jpg',
            'created_at' => date('Y-m-d'),
            'category_title' => $row['city_school'] ?: 'Город не указан',
            'category_url' => '#'
        ];
    }

    if (count($relatedSchools) > 0) {
        echo '<div style="padding: 20px;">';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle('Другие школы региона', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
        
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
        renderCardsGrid($relatedSchools, 'school', [
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
    <h3 style="margin: 0 0 20px 0;">Комментарии (<?= $school['comment_count'] ?>)</h3>
    <!-- Comments will be added later per user request -->
</div>
<?php
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $school['title_school'];
$metaD = $school['description_school'] ? substr($school['description_school'], 0, 160) : 'Информация о школе ' . $school['title_school'];
$metaK = $school['title_school'] . ', школа, ' . $school['city_school'] . ', ' . $school['title_region'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
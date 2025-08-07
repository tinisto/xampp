<?php
// Single VPO page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get VPO URL from parameter
$vpoUrl = $_GET['url'] ?? '';
if (empty($vpoUrl)) {
    header("Location: /vpo-all-regions");
    exit();
}

// Get VPO data
$query = "SELECT v.*, r.title_region, r.url_region,
                 (SELECT COUNT(*) FROM comments WHERE entity_type = 'vpo' AND entity_id = v.id) as comment_count
          FROM vpo v
          LEFT JOIN regions r ON v.region_id = r.id_region
          WHERE v.url = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $vpoUrl);
$stmt->execute();
$result = $stmt->get_result();
$vpo = $result->fetch_assoc();

if (!$vpo) {
    header("Location: /404");
    exit();
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($vpo['title'], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
$breadcrumbItems = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'ВПО по регионам', 'url' => '/vpo-all-regions']
];
if ($vpo['title_region']) {
    $breadcrumbItems[] = ['text' => $vpo['title_region'], 'url' => '/vpo-in-region/' . $vpo['url_region']];
}
$breadcrumbItems[] = ['text' => $vpo['title']];
renderBreadcrumb($breadcrumbItems);
$greyContent2 = ob_get_clean();

// Section 3: Metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
        <?php if ($vpo['city']): ?>
            <div>
                <i class="fas fa-map-marker-alt" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($vpo['city']) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($vpo['title_region']): ?>
            <div>
                <i class="fas fa-globe" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($vpo['title_region']) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $vpo['comment_count'] ?> комментариев</span>
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
        <button onclick="showTab('admission')" id="tab-admission" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #666; cursor: pointer;">
            Поступление
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
        <?php if ($vpo['description']): ?>
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Описание</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($vpo['description'])) ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <?php if ($vpo['rector']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Ректор</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($vpo['rector']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($vpo['founded_year']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Год основания</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($vpo['founded_year']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($vpo['students_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество студентов</h4>
                    <p style="color: #666; margin: 0;"><?= number_format($vpo['students_count']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Contacts Tab -->
    <div id="content-contacts" class="tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php if ($vpo['address']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Адрес</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($vpo['address']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($vpo['phone']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-phone" style="margin-right: 8px;"></i>Телефон</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($vpo['phone']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($vpo['email']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-envelope" style="margin-right: 8px;"></i>Email</h4>
                    <p style="color: #666; margin: 0;"><a href="mailto:<?= htmlspecialchars($vpo['email']) ?>" style="color: #28a745;"><?= htmlspecialchars($vpo['email']) ?></a></p>
                </div>
            <?php endif; ?>
            
            <?php if ($vpo['website']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-globe" style="margin-right: 8px;"></i>Сайт</h4>
                    <p style="color: #666; margin: 0;"><a href="<?= htmlspecialchars($vpo['website']) ?>" target="_blank" style="color: #28a745;"><?= htmlspecialchars($vpo['website']) ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Admission Tab -->
    <div id="content-admission" class="tab-content" style="display: none;">
        <?php if ($vpo['admission_info']): ?>
            <div style="color: #666; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($vpo['admission_info'])) ?>
            </div>
        <?php else: ?>
            <p style="color: #666;">Информация о поступлении будет добавлена позже.</p>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related VPO
ob_start();
// Get related VPO from same region
if ($vpo['region_id']) {
    $relatedQuery = "SELECT id, title, url, city
                     FROM vpo 
                     WHERE region_id = ? AND id != ? 
                     ORDER BY RAND() 
                     LIMIT 4";
    $stmt = $connection->prepare($relatedQuery);
    $stmt->bind_param("ii", $vpo['region_id'], $vpo['id']);
    $stmt->execute();
    $relatedResult = $stmt->get_result();
    $relatedVPO = [];
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedVPO[] = [
            'id_news' => $row['id'],
            'title_news' => $row['title'],
            'url_news' => $row['url'],
            'image_news' => '/images/default-vpo.jpg',
            'created_at' => date('Y-m-d'),
            'category_title' => $row['city'] ?: 'Город не указан',
            'category_url' => '#'
        ];
    }

    if (count($relatedVPO) > 0) {
        echo '<div style="padding: 20px;">';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle('Другие ВУЗы региона', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
        
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
        renderCardsGrid($relatedVPO, 'vpo', [
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
    <h3 style="margin: 0 0 20px 0;">Комментарии (<?= $vpo['comment_count'] ?>)</h3>
    <!-- Comments will be added later per user request -->
</div>
<?php
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $vpo['title'];
$metaD = $vpo['description'] ? substr($vpo['description'], 0, 160) : 'Информация о ВУЗе ' . $vpo['title'];
$metaK = $vpo['title'] . ', ВУЗ, университет, ' . $vpo['city'] . ', ' . $vpo['title_region'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
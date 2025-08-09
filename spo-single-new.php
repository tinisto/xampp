<?php
// Single SPO page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get SPO URL from parameter
$spoUrl = $_GET['url_slug'] ?? '';
if (empty($spoUrl)) {
    header("Location: /spo-all-regions");
    exit();
}

// Get SPO data
$query = "SELECT s.*, r.title_region, r.url_region, t.title_town,
                 (SELECT COUNT(*) FROM comments WHERE entity_type = 'spo' AND entity_id = s.id) as comment_count
          FROM spo s
          LEFT JOIN regions r ON s.region_id = r.id_region
          LEFT JOIN towns t ON s.town_id = t.id_town
          WHERE s.url_spo = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $spoUrl);
$stmt->execute();
$result = $stmt->get_result();
$spo = $result->fetch_assoc();

if (!$spo) {
    header("Location: /404");
    exit();
}

// Update view count
$updateQuery = "UPDATE spo SET view_spo = view_spo + 1 WHERE id = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("i", $spo['id']);
$updateStmt->execute();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($spo['title_spo'], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
$breadcrumbItems = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'СПО по регионам', 'url' => '/spo-all-regions']
];
if ($spo['title_region']) {
    $breadcrumbItems[] = ['text' => $spo['title_region'], 'url' => '/spo-in-region/' . $spo['url_region']];
}
$breadcrumbItems[] = ['text' => $spo['title_spo']];
renderBreadcrumb($breadcrumbItems);
$greyContent2 = ob_get_clean();

// Section 3: Metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
        <?php if ($spo['city_spo'] || $spo['title_town']): ?>
            <div>
                <i class="fas fa-map-marker-alt" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($spo['city_spo'] ?: $spo['title_town']) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($spo['title_region']): ?>
            <div>
                <i class="fas fa-globe" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($spo['title_region']) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <i class="fas fa-eye" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= number_format($spo['view_spo'] ?? 0) ?> просмотров</span>
        </div>
        <div>
            <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $spo['comment_count'] ?> комментариев</span>
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
        <button onclick="showTab('programs')" id="tab-programs" style="padding: 10px 20px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #666; cursor: pointer;">
            Программы обучения
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
        <?php if ($spo['description_spo']): ?>
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Описание</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($spo['description_spo'])) ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <?php if ($spo['type_spo']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Тип учреждения</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($spo['type_spo']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($spo['founded_year']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Год основания</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($spo['founded_year']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($spo['students_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество студентов</h4>
                    <p style="color: #666; margin: 0;"><?= number_format($spo['students_count']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($spo['teachers_count']): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="color: #333; margin: 0 0 10px 0;">Количество преподавателей</h4>
                    <p style="color: #666; margin: 0;"><?= number_format($spo['teachers_count']) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($spo['full_name_spo'] && $spo['full_name_spo'] !== $spo['title_spo']): ?>
            <div style="margin-top: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Полное наименование</h3>
                <p style="color: #666; line-height: 1.6;"><?= htmlspecialchars($spo['full_name_spo']) ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Programs Tab -->
    <div id="content-programs" class="tab-content" style="display: none;">
        <?php if ($spo['programs']): ?>
            <div>
                <h3 style="color: #333; margin-bottom: 15px;">Программы обучения</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($spo['programs'])) ?>
                </div>
            </div>
        <?php else: ?>
            <p style="color: #666;">Информация о программах обучения будет добавлена позже.</p>
        <?php endif; ?>
        
        <?php if ($spo['specialties']): ?>
            <div style="margin-top: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Специальности</h3>
                <ul style="color: #666; line-height: 1.8;">
                    <?php foreach (explode("\n", $spo['specialties']) as $specialty): ?>
                        <?php if (trim($specialty)): ?>
                            <li><?= htmlspecialchars(trim($specialty)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Contacts Tab -->
    <div id="content-contacts" class="tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php if ($spo['address_spo']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Адрес</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($spo['address_spo']) ?></p>
                    <?php if ($spo['zip_code']): ?>
                        <p style="color: #666; margin: 5px 0 0 0;">Индекс: <?= htmlspecialchars($spo['zip_code']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($spo['tel']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-phone" style="margin-right: 8px;"></i>Телефон</h4>
                    <p style="color: #666; margin: 0;"><?= htmlspecialchars($spo['tel']) ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($spo['email']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-envelope" style="margin-right: 8px;"></i>Email</h4>
                    <p style="color: #666; margin: 0;"><a href="mailto:<?= htmlspecialchars($spo['email']) ?>" style="color: #28a745;"><?= htmlspecialchars($spo['email']) ?></a></p>
                </div>
            <?php endif; ?>
            
            <?php if ($spo['site']): ?>
                <div>
                    <h4 style="color: #333; margin: 0 0 10px 0;"><i class="fas fa-globe" style="margin-right: 8px;"></i>Сайт</h4>
                    <p style="color: #666; margin: 0;"><a href="<?= htmlspecialchars($spo['site']) ?>" target="_blank" style="color: #28a745;"><?= htmlspecialchars($spo['site']) ?></a></p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($spo['director_name']): ?>
            <div style="margin-top: 30px;">
                <h4 style="color: #333; margin: 0 0 15px 0;">Руководство</h4>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <p style="color: #333; margin: 0 0 10px 0; font-size: 18px; font-weight: 500;">
                        <?= htmlspecialchars($spo['director_name']) ?>
                    </p>
                    <p style="color: #666; margin: 0;">Директор</p>
                    <?php if ($spo['director_phone']): ?>
                        <p style="color: #666; margin: 10px 0 0 0;">
                            <i class="fas fa-phone" style="width: 20px;"></i>
                            <?= htmlspecialchars($spo['director_phone']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($spo['director_email']): ?>
                        <p style="color: #666; margin: 5px 0 0 0;">
                            <i class="fas fa-envelope" style="width: 20px;"></i>
                            <a href="mailto:<?= htmlspecialchars($spo['director_email']) ?>" style="color: #28a745;">
                                <?= htmlspecialchars($spo['director_email']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Admission Tab -->
    <div id="content-admission" class="tab-content" style="display: none;">
        <?php if ($spo['admission_info']): ?>
            <div>
                <h3 style="color: #333; margin-bottom: 15px;">Информация о поступлении</h3>
                <div style="color: #666; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($spo['admission_info'])) ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <h4 style="color: #333; margin-bottom: 15px;">Приемная комиссия</h4>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <?php if ($spo['admission_phone']): ?>
                    <p style="color: #666; margin: 0 0 10px 0;">
                        <i class="fas fa-phone" style="width: 20px; color: #28a745;"></i>
                        <strong>Телефон:</strong> <?= htmlspecialchars($spo['admission_phone']) ?>
                    </p>
                <?php endif; ?>
                <?php if ($spo['admission_email']): ?>
                    <p style="color: #666; margin: 0 0 10px 0;">
                        <i class="fas fa-envelope" style="width: 20px; color: #28a745;"></i>
                        <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($spo['admission_email']) ?>" style="color: #28a745;"><?= htmlspecialchars($spo['admission_email']) ?></a>
                    </p>
                <?php endif; ?>
                <?php if ($spo['admission_hours']): ?>
                    <p style="color: #666; margin: 0;">
                        <i class="fas fa-clock" style="width: 20px; color: #28a745;"></i>
                        <strong>Часы работы:</strong> <?= htmlspecialchars($spo['admission_hours']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related SPO
ob_start();
// Get related SPO from same region
if ($spo['region_id']) {
    $relatedQuery = "SELECT id, title_spo, url_spo, city_spo
                     FROM spo 
                     WHERE region_id = ? AND id != ? 
                     ORDER BY RAND() 
                     LIMIT 4";
    $stmt = $connection->prepare($relatedQuery);
    $stmt->bind_param("ii", $spo['region_id'], $spo['id']);
    $stmt->execute();
    $relatedResult = $stmt->get_result();
    $relatedSpo = [];
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedSpo[] = [
            'id_news' => $row['id'],
            'title_news' => $row['title_spo'],
            'url_news' => $row['url_spo'],
            'image_news' => '/images/default-spo.jpg',
            'created_at' => date('Y-m-d'),
            'category_title' => $row['city_spo'] ?: 'Город не указан',
            'category_url' => '#'
        ];
    }

    if (count($relatedSpo) > 0) {
        echo '<div style="padding: 20px;">';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle('Другие СПО региона', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
        
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
        renderCardsGrid($relatedSpo, 'spo', [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => true
        ]);
        echo '</div>';
    }
}
$greyContent6 = ob_get_clean();

// Section 7: Beautiful Threaded Comments
ob_start();
// Include the new threaded comments component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/threaded-comments.php';
renderThreadedComments('spo', $spo['id'], [
    'title' => 'Отзывы и комментарии',
    'loadLimit' => 10,
    'allowNewComments' => true,
    'allowReplies' => true,
    'maxDepth' => 5
]);
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $spo['title_spo'];
$metaD = $spo['description_spo'] ? substr($spo['description_spo'], 0, 160) : 'Информация о СПО ' . $spo['title_spo'];
$metaK = $spo['title_spo'] . ', СПО, колледж, техникум, ' . $spo['city_spo'] . ', ' . $spo['title_region'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
// Modern SPO (Colleges) listing page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get current page
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get region filter
$regionId = isset($_GET['region']) ? (int)$_GET['region'] : null;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query conditions
$where = ["1=1"];
$params = [];

if ($regionId) {
    $where[] = "s.region_id = ?";
    $params[] = $regionId;
}

if ($search) {
    $where[] = "(s.name LIKE ? OR s.full_name LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = implode(' AND ', $where);

// Get total count
$totalSPO = db_fetch_column("
    SELECT COUNT(*) 
    FROM spo s 
    WHERE $whereClause
", $params);

// Get SPO list
$spoList = db_fetch_all("
    SELECT s.*
    FROM spo s
    WHERE $whereClause
    ORDER BY s.name ASC
    LIMIT ? OFFSET ?
", array_merge($params, [$perPage, $offset]));

// Calculate pagination
$totalPages = ceil($totalSPO / $perPage);

// Get regions for filter
$regions = [
    ['id' => 77, 'name' => 'Москва'],
    ['id' => 78, 'name' => 'Санкт-Петербург'],
    ['id' => 50, 'name' => 'Московская область'],
    ['id' => 47, 'name' => 'Ленинградская область'],
    ['id' => 16, 'name' => 'Республика Татарстан'],
];

// Page title
$pageTitle = 'Колледжи и техникумы России';

// Section 1: Title
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            Колледжи и техникумы России
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Средние специальные учебные заведения для получения профессионального образования
        </p>
        <?php if ($totalSPO > 0): ?>
        <div style="margin-top: 30px; display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 36px; font-weight: 700; color: #222222;"><?= number_format($totalSPO) ?></div>
                <div style="font-size: 16px; color: #717171;">учебных заведений</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Filters
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <form method="get" action="/spo" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
            <!-- Region filter -->
            <select name="region" onchange="this.form.submit()" 
                    style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background: white; min-width: 200px;">
                <option value="">Все регионы</option>
                <?php foreach ($regions as $region): ?>
                <option value="<?= $region['id'] ?>" <?= $regionId == $region['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($region['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <!-- Search -->
            <div style="flex: 1; display: flex; gap: 10px; min-width: 300px;">
                <input type="text" 
                       name="search" 
                       placeholder="Поиск по названию или описанию..." 
                       value="<?= htmlspecialchars($search) ?>"
                       style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px;">
                <button type="submit" 
                        style="padding: 10px 20px; background: #00b09b; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="fas fa-search"></i> Найти
                </button>
            </div>
            
            <?php if ($regionId || $search): ?>
            <a href="/spo" 
               style="padding: 10px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 8px;">
                <i class="fas fa-times"></i> Сбросить
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Quick info
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div style="background: #e8f5e9; padding: 25px; border-radius: 12px;">
                <i class="fas fa-clock" style="font-size: 32px; color: #2e7d32; margin-bottom: 15px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #2e7d32; margin-bottom: 10px;">Срок обучения</h3>
                <p style="color: #666; margin: 0;">От 2 до 4 лет в зависимости от программы</p>
            </div>
            <div style="background: #fff3e0; padding: 25px; border-radius: 12px;">
                <i class="fas fa-book-open" style="font-size: 32px; color: #f57c00; margin-bottom: 15px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #f57c00; margin-bottom: 10px;">Специальности</h3>
                <p style="color: #666; margin: 0;">Более 250 направлений подготовки</p>
            </div>
            <div style="background: #e3f2fd; padding: 25px; border-radius: 12px;">
                <i class="fas fa-briefcase" style="font-size: 32px; color: #1976d2; margin-bottom: 15px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #1976d2; margin-bottom: 10px;">Трудоустройство</h3>
                <p style="color: #666; margin: 0;">Практическая подготовка и помощь в трудоустройстве</p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: SPO list
ob_start();
?>
<div style="padding: 0 20px 40px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php if (!empty($spoList)): ?>
        <style>
            @media (min-width: 1200px) {
                .spo-grid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }
            }
        </style>
        <div class="spo-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; max-width: 1400px; margin: 0 auto;">
            <?php foreach ($spoList as $spo): ?>
            <article style="background: #f8f9fa; border-radius: 12px; overflow: hidden; transition: all 0.3s; border: 1px solid #e9ecef; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="window.location.href='/spo/<?= htmlspecialchars($spo['url_slug']) ?>'">
                
                <div style="background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); height: 8px;"></div>
                
                <div style="padding: 15px;">
                    <div>
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; line-height: 1.3; color: #333;">
                                <?= htmlspecialchars($spo['name']) ?>
                            </h3>
                            
                            <?php if ($spo['full_name']): ?>
                            <p style="color: #666; font-size: 14px; margin: 10px 0; line-height: 1.5;">
                                <?= htmlspecialchars(mb_substr($spo['full_name'], 0, 100)) ?>...
                            </p>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
                                <?php if ($spo['site']): ?>
                                <a href="<?= htmlspecialchars($spo['site']) ?>" target="_blank"
                                   onclick="event.stopPropagation()"
                                   style="color: #00b09b; font-size: 14px; text-decoration: none;">
                                    <i class="fas fa-globe"></i> Сайт
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($spo['tel']): ?>
                                <span style="color: #666; font-size: 14px;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($spo['tel']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 30px 20px;">
            <i class="fas fa-school" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h3 style="color: #6c757d; margin-bottom: 10px;">Колледжи не найдены</h3>
            <p style="color: #adb5bd;">Попробуйте изменить параметры поиска или выбрать другой регион</p>
            <a href="/spo" style="display: inline-block; margin-top: 20px; color: #00b09b; text-decoration: none;">
                ← Показать все колледжи
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Pagination
ob_start();
if ($totalPages > 1):
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <nav style="display: flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;">
            <!-- Previous -->
            <?php if ($page > 1): ?>
            <a href="/spo?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                ← Предыдущая
            </a>
            <?php endif; ?>
            
            <!-- Page numbers -->
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            for ($i = $start; $i <= $end; $i++): ?>
            <a href="/spo?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
               style="padding: 10px 15px; border-radius: 8px; text-decoration: none;
                      <?= $i === $page ? 'background: #00b09b; color: white;' : 'background: white; border: 1px solid #dee2e6; color: #495057;' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="/spo?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                Следующая →
            </a>
            <?php endif; ?>
        </nav>
        
        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 14px;">
            Страница <?= $page ?> из <?= $totalPages ?> (всего <?= number_format($totalSPO) ?> колледжей)
        </div>
    </div>
</div>
<?php
endif;
$greyContent5 = ob_get_clean();

// Section 6: Empty
$greyContent6 = '';

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
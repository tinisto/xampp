<?php
// Modern Schools listing page
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
$totalSchools = db_fetch_column("
    SELECT COUNT(*) 
    FROM schools s 
    WHERE $whereClause
", $params);

// Get schools list
$schoolsList = db_fetch_all("
    SELECT s.*
    FROM schools s
    WHERE $whereClause
    ORDER BY s.name ASC
    LIMIT ? OFFSET ?
", array_merge($params, [$perPage, $offset]));

// Calculate pagination
$totalPages = ceil($totalSchools / $perPage);

// Get regions for filter
$regions = [
    ['id' => 77, 'name' => 'Москва'],
    ['id' => 78, 'name' => 'Санкт-Петербург'],
    ['id' => 50, 'name' => 'Московская область'],
    ['id' => 47, 'name' => 'Ленинградская область'],
    ['id' => 16, 'name' => 'Республика Татарстан'],
];

// Page title
$pageTitle = 'Школы России';

// Section 1: Title
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            Школы России
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Общеобразовательные учреждения: школы, гимназии и лицеи
        </p>
        <?php if ($totalSchools > 0): ?>
        <div style="margin-top: 30px; display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 36px; font-weight: 700; color: #222222;"><?= number_format($totalSchools) ?></div>
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
        <form method="get" action="/schools" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
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
                       placeholder="Поиск по названию или номеру школы..." 
                       value="<?= htmlspecialchars($search) ?>"
                       style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px;">
                <button type="submit" 
                        style="padding: 10px 20px; background: #f5576c; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="fas fa-search"></i> Найти
                </button>
            </div>
            
            <?php if ($regionId || $search): ?>
            <a href="/schools" 
               style="padding: 10px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 8px;">
                <i class="fas fa-times"></i> Сбросить
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: School types info
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 28px; font-weight: 700; margin-bottom: 30px;">Типы школ</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background: #fce4ec; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-school" style="font-size: 36px; color: #c2185b; margin-bottom: 15px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #c2185b; margin-bottom: 10px;">Общеобразовательные</h3>
                <p style="color: #666; margin: 0;">Стандартная программа обучения 1-11 классы</p>
            </div>
            <div style="background: #e8eaf6; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-graduation-cap" style="font-size: 36px; color: #3f51b5; margin-bottom: 15px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #3f51b5; margin-bottom: 10px;">Гимназии</h3>
                <p style="color: #666; margin: 0;">Углубленное изучение гуманитарных предметов</p>
            </div>
            <div style="background: #e0f2f1; padding: 25px; border-radius: 12px; text-align: center;">
                <i class="fas fa-atom" style="font-size: 36px; color: #00897b; margin-bottom: 15px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #00897b; margin-bottom: 10px;">Лицеи</h3>
                <p style="color: #666; margin: 0;">Углубленное изучение точных наук</p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Schools list
ob_start();
?>
<div style="padding: 0 20px 40px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php if (!empty($schoolsList)): ?>
        <style>
            @media (min-width: 1200px) {
                .schools-grid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }
            }
        </style>
        <div class="schools-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; max-width: 1400px; margin: 0 auto;">
            <?php foreach ($schoolsList as $school): ?>
            <article style="background: #f8f9fa; border-radius: 12px; overflow: hidden; transition: all 0.3s; border: 1px solid #e9ecef; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="window.location.href='/school/<?= htmlspecialchars($school['url_slug']) ?>'">
                
                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); height: 8px;"></div>
                
                <div style="padding: 15px;">
                    <div>
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; line-height: 1.3; color: #333;">
                                <?= htmlspecialchars($school['name']) ?>
                            </h3>
                            
                            <?php if ($school['full_name']): ?>
                            <p style="color: #666; font-size: 14px; margin: 10px 0; line-height: 1.5;">
                                <?= htmlspecialchars(mb_substr($school['full_name'], 0, 100)) ?>...
                            </p>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
                                <?php if ($school['site']): ?>
                                <a href="<?= htmlspecialchars($school['site']) ?>" target="_blank"
                                   onclick="event.stopPropagation()"
                                   style="color: #f5576c; font-size: 14px; text-decoration: none;">
                                    <i class="fas fa-globe"></i> Сайт
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($school['tel']): ?>
                                <span style="color: #666; font-size: 14px;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($school['tel']) ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($school['street']): ?>
                                <span style="color: #666; font-size: 14px;">
                                    <i class="fas fa-map-marker-alt"></i> Адрес
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
            <h3 style="color: #6c757d; margin-bottom: 10px;">Школы не найдены</h3>
            <p style="color: #adb5bd;">Попробуйте изменить параметры поиска или выбрать другой регион</p>
            <a href="/schools" style="display: inline-block; margin-top: 20px; color: #f5576c; text-decoration: none;">
                ← Показать все школы
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
            <a href="/schools?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                ← Предыдущая
            </a>
            <?php endif; ?>
            
            <!-- Page numbers -->
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            for ($i = $start; $i <= $end; $i++): ?>
            <a href="/schools?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
               style="padding: 10px 15px; border-radius: 8px; text-decoration: none;
                      <?= $i === $page ? 'background: #f5576c; color: white;' : 'background: white; border: 1px solid #dee2e6; color: #495057;' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="/schools?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                Следующая →
            </a>
            <?php endif; ?>
        </nav>
        
        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 14px;">
            Страница <?= $page ?> из <?= $totalPages ?> (всего <?= number_format($totalSchools) ?> школ)
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
include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>
<?php
// Modern VPO (Universities) listing page
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
    $where[] = "v.region_id = ?";
    $params[] = $regionId;
}

if ($search) {
    $where[] = "(v.name LIKE ? OR v.full_name LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = implode(' AND ', $where);

// Get total count
$totalVPO = db_fetch_column("
    SELECT COUNT(*) 
    FROM vpo v 
    WHERE $whereClause
", $params);

// Get VPO list
$vpoList = db_fetch_all("
    SELECT v.*
    FROM vpo v
    WHERE $whereClause
    ORDER BY v.name ASC
    LIMIT ? OFFSET ?
", array_merge($params, [$perPage, $offset]));

// Calculate pagination
$totalPages = ceil($totalVPO / $perPage);

// Get regions for filter (simplified for now)
$regions = [
    ['id' => 77, 'name' => 'Москва'],
    ['id' => 78, 'name' => 'Санкт-Петербург'],
    ['id' => 50, 'name' => 'Московская область'],
    ['id' => 47, 'name' => 'Ленинградская область'],
    ['id' => 16, 'name' => 'Республика Татарстан'],
];

// Page title
$pageTitle = 'ВУЗы России';

// Section 1: Title
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            ВУЗы России
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Высшие учебные заведения: университеты, институты и академии
        </p>
        <?php if ($totalVPO > 0): ?>
        <div style="margin-top: 30px; display: flex; justify-content: center; gap: 40px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 36px; font-weight: 700; color: #222222;"><?= number_format($totalVPO) ?></div>
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
        <form method="get" action="/vpo" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
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
                        style="padding: 10px 20px; background: #1e3c72; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="fas fa-search"></i> Найти
                </button>
            </div>
            
            <?php if ($regionId || $search): ?>
            <a href="/vpo" 
               style="padding: 10px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 8px;">
                <i class="fas fa-times"></i> Сбросить
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Quick stats
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div style="background: #e3f2fd; padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-university" style="font-size: 32px; color: #1976d2; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px; font-weight: 700; color: #1976d2; margin: 10px 0;">100+</h3>
                <p style="color: #666; margin: 0;">Университетов</p>
            </div>
            <div style="background: #f3e5f5; padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-graduation-cap" style="font-size: 32px; color: #7b1fa2; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px; font-weight: 700; color: #7b1fa2; margin: 10px 0;">500+</h3>
                <p style="color: #666; margin: 0;">Специальностей</p>
            </div>
            <div style="background: #e8f5e9; padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-users" style="font-size: 32px; color: #388e3c; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px; font-weight: 700; color: #388e3c; margin: 10px 0;">1M+</h3>
                <p style="color: #666; margin: 0;">Студентов</p>
            </div>
            <div style="background: #fff3e0; padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-medal" style="font-size: 32px; color: #f57c00; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px; font-weight: 700; color: #f57c00; margin: 10px 0;">85</h3>
                <p style="color: #666; margin: 0;">Регионов</p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: VPO list
ob_start();
?>
<div style="padding: 0 20px 40px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php if (!empty($vpoList)): ?>
        <style>
            @media (min-width: 1200px) {
                .vpo-grid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }
            }
        </style>
        <div class="vpo-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; max-width: 1400px; margin: 0 auto;">
            <?php foreach ($vpoList as $vpo): ?>
            <article style="background: #f8f9fa; border-radius: 12px; overflow: hidden; transition: all 0.3s; border: 1px solid #e9ecef; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="window.location.href='/vpo/<?= htmlspecialchars($vpo['url_slug']) ?>'">
                
                <div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); height: 8px;"></div>
                
                <div style="padding: 15px;">
                    <div>
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; line-height: 1.3; color: #333;">
                                <?= htmlspecialchars($vpo['name']) ?>
                            </h3>
                            
                            <?php if ($vpo['full_name']): ?>
                            <p style="color: #666; font-size: 14px; margin: 10px 0; line-height: 1.5;">
                                <?= htmlspecialchars(mb_substr($vpo['full_name'], 0, 100)) ?>...
                            </p>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
                                <?php if ($vpo['site']): ?>
                                <a href="<?= htmlspecialchars($vpo['site']) ?>" target="_blank"
                                   onclick="event.stopPropagation()"
                                   style="color: #1e3c72; font-size: 14px; text-decoration: none;">
                                    <i class="fas fa-globe"></i> Сайт
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($vpo['tel']): ?>
                                <span style="color: #666; font-size: 14px;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($vpo['tel']) ?>
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
            <i class="fas fa-university" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h3 style="color: #6c757d; margin-bottom: 10px;">ВУЗы не найдены</h3>
            <p style="color: #adb5bd;">Попробуйте изменить параметры поиска или выбрать другой регион</p>
            <a href="/vpo" style="display: inline-block; margin-top: 20px; color: #1e3c72; text-decoration: none;">
                ← Показать все ВУЗы
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
            <a href="/vpo?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                ← Предыдущая
            </a>
            <?php endif; ?>
            
            <!-- Page numbers -->
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            for ($i = $start; $i <= $end; $i++): ?>
            <a href="/vpo?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
               style="padding: 10px 15px; border-radius: 8px; text-decoration: none;
                      <?= $i === $page ? 'background: #1e3c72; color: white;' : 'background: white; border: 1px solid #dee2e6; color: #495057;' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="/vpo?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                Следующая →
            </a>
            <?php endif; ?>
        </nav>
        
        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 14px;">
            Страница <?= $page ?> из <?= $totalPages ?> (всего <?= number_format($totalVPO) ?> ВУЗов)
        </div>
    </div>
</div>
<?php
endif;
$greyContent5 = ob_get_clean();

// Section 6: Info block
ob_start();
?>
<div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 30px 20px;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 20px;">Поступление в ВУЗ</h2>
        <p style="font-size: 18px; line-height: 1.6; opacity: 0.9; margin-bottom: 30px;">
            Выбор университета — важный шаг в жизни каждого абитуриента. 
            На нашем портале вы найдете актуальную информацию о высших учебных заведениях России.
        </p>
        <a href="/posts?category=vpo" 
           style="display: inline-block; background: white; color: #1e3c72; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            Читать статьи о поступлении →
        </a>
    </div>
</div>
<?php
$greyContent6 = ob_get_clean();

// Include template
$blueContent = '';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
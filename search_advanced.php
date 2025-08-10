<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get search parameters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get categories for filter
$categories = db_fetch_all("SELECT * FROM categories ORDER BY name");

$results = [];
$totalResults = 0;

if ($q) {
    // Build search query based on type
    $searchResults = [];
    
    // Search in news
    if ($type === 'all' || $type === 'news') {
        $newsQuery = "
            SELECT 'news' as item_type, id_news as id, title_news as title, 
                   text_news as text, url_news as url, created_at, views,
                   category_id, NULL as region_id
            FROM news 
            WHERE is_published = 1 
            AND (title_news LIKE ? OR text_news LIKE ?)
        ";
        
        $params = ["%$q%", "%$q%"];
        
        if ($category > 0) {
            $newsQuery .= " AND category_id = ?";
            $params[] = $category;
        }
        
        if ($dateFrom) {
            $newsQuery .= " AND created_at >= ?";
            $params[] = $dateFrom . ' 00:00:00';
        }
        
        if ($dateTo) {
            $newsQuery .= " AND created_at <= ?";
            $params[] = $dateTo . ' 23:59:59';
        }
        
        $newsResults = db_fetch_all($newsQuery, $params);
        $searchResults = array_merge($searchResults, $newsResults);
    }
    
    // Search in posts
    if ($type === 'all' || $type === 'posts') {
        $postsQuery = "
            SELECT 'post' as item_type, id as id, title_post as title, 
                   text_post as text, url_slug as url, date_post as created_at, 
                   views, category as category_id, NULL as region_id
            FROM posts 
            WHERE is_published = 1 
            AND (title_post LIKE ? OR text_post LIKE ?)
        ";
        
        $params = ["%$q%", "%$q%"];
        
        if ($category > 0) {
            $postsQuery .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($dateFrom) {
            $postsQuery .= " AND date_post >= ?";
            $params[] = $dateFrom . ' 00:00:00';
        }
        
        if ($dateTo) {
            $postsQuery .= " AND date_post <= ?";
            $params[] = $dateTo . ' 23:59:59';
        }
        
        $postResults = db_fetch_all($postsQuery, $params);
        $searchResults = array_merge($searchResults, $postResults);
    }
    
    // Search in VPO
    if ($type === 'all' || $type === 'vpo') {
        $vpoQuery = "
            SELECT 'vpo' as item_type, id_university as id, name_vpo as title, 
                   description as text, url_slug as url, created_at, 0 as views,
                   NULL as category_id, region_id
            FROM vpo 
            WHERE name_vpo LIKE ? OR description LIKE ?
        ";
        
        $vpoResults = db_fetch_all($vpoQuery, ["%$q%", "%$q%"]);
        $searchResults = array_merge($searchResults, $vpoResults);
    }
    
    // Search in SPO
    if ($type === 'all' || $type === 'spo') {
        $spoQuery = "
            SELECT 'spo' as item_type, id_college as id, name_spo as title, 
                   description as text, url_slug as url, created_at, 0 as views,
                   NULL as category_id, region_id
            FROM spo 
            WHERE name_spo LIKE ? OR description LIKE ?
        ";
        
        $spoResults = db_fetch_all($spoQuery, ["%$q%", "%$q%"]);
        $searchResults = array_merge($searchResults, $spoResults);
    }
    
    // Search in schools
    if ($type === 'all' || $type === 'schools') {
        $schoolsQuery = "
            SELECT 'school' as item_type, id_school as id, name_school as title, 
                   description as text, url_slug as url, created_at, 0 as views,
                   NULL as category_id, region_id
            FROM schools 
            WHERE name_school LIKE ? OR description LIKE ?
        ";
        
        $schoolResults = db_fetch_all($schoolsQuery, ["%$q%", "%$q%"]);
        $searchResults = array_merge($searchResults, $schoolResults);
    }
    
    // Sort results
    if ($sortBy === 'date') {
        usort($searchResults, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
    } elseif ($sortBy === 'views') {
        usort($searchResults, function($a, $b) {
            return $b['views'] - $a['views'];
        });
    } elseif ($sortBy === 'relevance') {
        // Simple relevance: exact matches first, then partial
        usort($searchResults, function($a, $b) use ($q) {
            $aExact = stripos($a['title'], $q) !== false ? 1 : 0;
            $bExact = stripos($b['title'], $q) !== false ? 1 : 0;
            return $bExact - $aExact;
        });
    }
    
    // Paginate results
    $totalResults = count($searchResults);
    $results = array_slice($searchResults, $offset, $perPage);
}

// Page title
$pageTitle = $q ? 'Поиск: ' . htmlspecialchars($q) : 'Расширенный поиск';

// Section 1: Search form
ob_start();
?>
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 20px; color: white; text-align: center;">
    <h1 style="font-size: 36px; margin-bottom: 20px;">Расширенный поиск</h1>
    <p style="font-size: 18px; opacity: 0.9;">Найдите нужную информацию с помощью фильтров</p>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Advanced search form
ob_start();
?>
<div style="background: var(--bg-secondary); padding: 30px 20px; border-radius: 12px; margin-top: -40px; position: relative; z-index: 10;">
    <form method="GET" action="/search" style="max-width: 800px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <!-- Search query -->
            <div style="grid-column: 1 / -1;">
                <label for="q" style="display: block; margin-bottom: 8px; font-weight: 600;">Поисковый запрос</label>
                <input type="text" 
                       id="q"
                       name="q" 
                       value="<?= htmlspecialchars($q) ?>" 
                       placeholder="Введите ключевые слова..."
                       style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                              border-radius: 8px; font-size: 16px; background: var(--bg-primary);">
            </div>
            
            <!-- Type filter -->
            <div>
                <label for="type" style="display: block; margin-bottom: 8px; font-weight: 600;">Тип контента</label>
                <select name="type" id="type" 
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                               border-radius: 8px; background: var(--bg-primary);">
                    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Все типы</option>
                    <option value="news" <?= $type === 'news' ? 'selected' : '' ?>>Новости</option>
                    <option value="posts" <?= $type === 'posts' ? 'selected' : '' ?>>Статьи</option>
                    <option value="vpo" <?= $type === 'vpo' ? 'selected' : '' ?>>ВУЗы</option>
                    <option value="spo" <?= $type === 'spo' ? 'selected' : '' ?>>ССУЗы</option>
                    <option value="schools" <?= $type === 'schools' ? 'selected' : '' ?>>Школы</option>
                </select>
            </div>
            
            <!-- Category filter -->
            <div>
                <label for="category" style="display: block; margin-bottom: 8px; font-weight: 600;">Категория</label>
                <select name="category" id="category"
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                               border-radius: 8px; background: var(--bg-primary);">
                    <option value="0">Все категории</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Date from -->
            <div>
                <label for="date_from" style="display: block; margin-bottom: 8px; font-weight: 600;">Дата от</label>
                <input type="date" 
                       id="date_from"
                       name="date_from" 
                       value="<?= htmlspecialchars($dateFrom) ?>"
                       style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                              border-radius: 8px; background: var(--bg-primary);">
            </div>
            
            <!-- Date to -->
            <div>
                <label for="date_to" style="display: block; margin-bottom: 8px; font-weight: 600;">Дата до</label>
                <input type="date" 
                       id="date_to"
                       name="date_to" 
                       value="<?= htmlspecialchars($dateTo) ?>"
                       style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                              border-radius: 8px; background: var(--bg-primary);">
            </div>
            
            <!-- Sort by -->
            <div>
                <label for="sort" style="display: block; margin-bottom: 8px; font-weight: 600;">Сортировка</label>
                <select name="sort" id="sort"
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); 
                               border-radius: 8px; background: var(--bg-primary);">
                    <option value="relevance" <?= $sortBy === 'relevance' ? 'selected' : '' ?>>По релевантности</option>
                    <option value="date" <?= $sortBy === 'date' ? 'selected' : '' ?>>По дате</option>
                    <option value="views" <?= $sortBy === 'views' ? 'selected' : '' ?>>По просмотрам</option>
                </select>
            </div>
            
            <!-- Submit buttons -->
            <div style="grid-column: 1 / -1; display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                <button type="submit" 
                        style="padding: 12px 30px; background: #007bff; color: white; 
                               border: none; border-radius: 8px; font-size: 16px; 
                               font-weight: 600; cursor: pointer;">
                    <i class="fas fa-search"></i> Найти
                </button>
                <a href="/search" 
                   style="padding: 12px 30px; background: var(--bg-secondary); color: var(--text-primary); 
                          border: 1px solid var(--border-color); border-radius: 8px; 
                          font-size: 16px; text-decoration: none; display: inline-flex; 
                          align-items: center;">
                    <i class="fas fa-times"></i> Сбросить
                </a>
            </div>
        </div>
    </form>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Search results
ob_start();
if ($q):
?>
<div style="padding: 40px 20px;">
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 24px; margin-bottom: 10px;">Результаты поиска</h2>
        <p style="color: var(--text-secondary);">
            Найдено результатов: <strong><?= $totalResults ?></strong>
            <?php if ($totalResults > 0): ?>
            (показаны <?= $offset + 1 ?>-<?= min($offset + $perPage, $totalResults) ?>)
            <?php endif; ?>
        </p>
    </div>
    
    <?php if (empty($results)): ?>
    <div style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-search" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
        <p style="font-size: 18px; color: var(--text-secondary);">По вашему запросу ничего не найдено</p>
        <p style="color: var(--text-secondary);">Попробуйте изменить параметры поиска</p>
    </div>
    <?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php foreach ($results as $result): ?>
        <div style="background: var(--bg-primary); border: 1px solid var(--border-color); 
                    border-radius: 12px; padding: 20px; transition: all 0.3s;">
            <div style="display: flex; align-items: start; gap: 15px; margin-bottom: 10px;">
                <?php
                $icon = 'fa-file';
                $color = '#6c757d';
                $link = '#';
                
                switch($result['item_type']) {
                    case 'news':
                        $icon = 'fa-newspaper';
                        $color = '#007bff';
                        $link = '/news/' . $result['url'];
                        break;
                    case 'post':
                        $icon = 'fa-book';
                        $color = '#28a745';
                        $link = '/post/' . $result['url'];
                        break;
                    case 'vpo':
                        $icon = 'fa-university';
                        $color = '#dc3545';
                        $link = '/vpo/' . $result['url'];
                        break;
                    case 'spo':
                        $icon = 'fa-school';
                        $color = '#ffc107';
                        $link = '/spo/' . $result['url'];
                        break;
                    case 'school':
                        $icon = 'fa-graduation-cap';
                        $color = '#17a2b8';
                        $link = '/school/' . $result['url'];
                        break;
                }
                ?>
                <i class="fas <?= $icon ?>" style="color: <?= $color ?>; font-size: 20px; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 10px 0; font-size: 20px;">
                        <a href="<?= $link ?>" style="color: var(--link-color); text-decoration: none;">
                            <?= htmlspecialchars($result['title']) ?>
                        </a>
                    </h3>
                    <p style="color: var(--text-secondary); margin: 0 0 10px 0; line-height: 1.6;">
                        <?= htmlspecialchars(mb_substr(strip_tags($result['text']), 0, 200)) ?>...
                    </p>
                    <div style="display: flex; gap: 20px; font-size: 14px; color: var(--text-secondary);">
                        <span><i class="fas fa-calendar"></i> <?= date('d.m.Y', strtotime($result['created_at'])) ?></span>
                        <?php if ($result['views'] > 0): ?>
                        <span><i class="fas fa-eye"></i> <?= $result['views'] ?></span>
                        <?php endif; ?>
                        <?php if ($result['category_id'] && isset($categories[$result['category_id'] - 1])): ?>
                        <span><i class="fas fa-tag"></i> <?= htmlspecialchars($categories[$result['category_id'] - 1]['name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if ($totalResults > $perPage): ?>
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
        <?php
        $totalPages = ceil($totalResults / $perPage);
        $queryParams = $_GET;
        
        for ($i = 1; $i <= $totalPages; $i++):
            $queryParams['page'] = $i;
            $pageUrl = '/search?' . http_build_query($queryParams);
        ?>
            <?php if ($i == $page): ?>
            <span style="padding: 8px 12px; background: #007bff; color: white; border-radius: 4px;">
                <?= $i ?>
            </span>
            <?php else: ?>
            <a href="<?= $pageUrl ?>" 
               style="padding: 8px 12px; background: var(--bg-secondary); color: var(--text-primary); 
                      border-radius: 4px; text-decoration: none;">
                <?= $i ?>
            </a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>
<?php
endif;
$greyContent3 = ob_get_clean();

// Include template
include 'real_template_local.php';
?>
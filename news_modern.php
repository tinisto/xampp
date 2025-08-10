<?php
// Modern news listing page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get current page
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 16;
$offset = ($page - 1) * $perPage;

// Get category filter
$categorySlug = isset($_GET['category']) ? $_GET['category'] : null;
$categoryId = null;
$category = null;

if ($categorySlug) {
    $category = db_fetch_one("SELECT * FROM categories WHERE url_slug = ?", [$categorySlug]);
    if ($category) {
        $categoryId = $category['id_category'];
    }
}

// Build query conditions
$where = ["n.approved = 1"];
$params = [];

if ($categoryId) {
    $where[] = "n.category_id = ?";
    $params[] = $categoryId;
}

$whereClause = implode(' AND ', $where);

// Get total count
$totalNews = db_fetch_column("
    SELECT COUNT(*) 
    FROM news n 
    WHERE $whereClause
", $params);

// Sort order
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
$orderBy = match($sort) {
    'old' => 'n.date_news ASC',
    'views' => 'n.view_news DESC',
    default => 'n.date_news DESC'
};

// Get news
$news = db_fetch_all("
    SELECT n.*, c.category_name, c.url_slug as category_slug
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id_category
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT ? OFFSET ?
", array_merge($params, [$perPage, $offset]));

// Calculate pagination
$totalPages = ceil($totalNews / $perPage);

// Get categories for filter
$categories = db_fetch_all("
    SELECT c.*, COUNT(n.id) as news_count
    FROM categories c
    LEFT JOIN news n ON c.id_category = n.category_id AND n.approved = 1
    GROUP BY c.id_category
    ORDER BY c.category_name
");

// Page title
$pageTitle = $category ? $category['category_name'] : 'Новости образования';

// Section 1: Title with breadcrumbs
ob_start();
?>
<div style="padding: 40px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 15px;"><?= htmlspecialchars($pageTitle) ?></h1>
        <p style="font-size: 18px; opacity: 0.9;">
            <?php if ($totalNews > 0): ?>
                Найдено <?= number_format($totalNews) ?> <?= plural_form($totalNews, 'новость', 'новости', 'новостей') ?>
            <?php else: ?>
                Новости не найдены
            <?php endif; ?>
        </p>
    </div>
</div>
<div style="background: var(--bg-secondary); padding: 15px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/breadcrumbs.php';
        render_breadcrumbs(get_breadcrumbs('news-list'));
        ?>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Category filter
ob_start();
?>
<div style="padding: 30px 20px; background: white; border-bottom: 1px solid #e9ecef;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <a href="/news" 
               class="category-link <?= !$categorySlug ? 'active' : '' ?>"
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= !$categorySlug ? 'background: #007bff; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                Все категории
            </a>
            
            <?php foreach ($categories as $cat): ?>
            <a href="/news?category=<?= htmlspecialchars($cat['url_slug']) ?>" 
               class="category-link <?= $categorySlug === $cat['url_slug'] ? 'active' : '' ?>"
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; transition: all 0.3s;
                      <?= $categorySlug === $cat['url_slug'] ? 'background: #007bff; color: white;' : 'background: #f8f9fa; color: #495057;' ?>">
                <?= htmlspecialchars($cat['category_name']) ?> 
                <span style="opacity: 0.7;">(<?= $cat['news_count'] ?>)</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Search and sort
ob_start();
?>
<div style="padding: 30px 20px; background: #f8f9fa;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
            <!-- Search -->
            <form action="/news" method="get" style="flex: 1; max-width: 400px;">
                <?php if ($categorySlug): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                <?php endif; ?>
                <div style="display: flex; gap: 10px;">
                    <input type="text" 
                           name="search" 
                           placeholder="Поиск новостей..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" 
                            style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Sort -->
            <select onchange="window.location.href=this.value" 
                    style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background: white;">
                <option value="/news<?= $categorySlug ? '?category=' . $categorySlug : '' ?>">
                    Сортировка: По дате (новые)
                </option>
                <option value="/news?sort=old<?= $categorySlug ? '&category=' . $categorySlug : '' ?>"
                        <?= isset($_GET['sort']) && $_GET['sort'] === 'old' ? 'selected' : '' ?>>
                    По дате (старые)
                </option>
                <option value="/news?sort=views<?= $categorySlug ? '&category=' . $categorySlug : '' ?>"
                        <?= isset($_GET['sort']) && $_GET['sort'] === 'views' ? 'selected' : '' ?>>
                    По популярности
                </option>
            </select>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: News grid
ob_start();
?>
<div style="padding: 40px 20px; background: white;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <?php if (!empty($news)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
            <?php foreach ($news as $item): ?>
            <article style="background: #f8f9fa; border-radius: 12px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="window.location.href='/news/<?= htmlspecialchars($item['url_slug']) ?>'">
                
                <?php if ($item['image_news'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $item['image_news'])): ?>
                <img src="<?= htmlspecialchars($item['image_news']) ?>" 
                     alt="<?= htmlspecialchars($item['title_news']) ?>"
                     style="width: 100%; height: 180px; object-fit: cover;">
                <?php else: ?>
                <div style="width: 100%; height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-newspaper" style="font-size: 48px; color: white; opacity: 0.8;"></i>
                </div>
                <?php endif; ?>
                
                <div style="padding: 20px;">
                    <?php if ($item['category_name']): ?>
                    <a href="/news?category=<?= htmlspecialchars($item['category_slug']) ?>" 
                       onclick="event.stopPropagation()"
                       style="display: inline-block; background: white; color: #6c757d; padding: 4px 12px; border-radius: 15px; font-size: 12px; text-decoration: none; margin-bottom: 10px;">
                        <?= htmlspecialchars($item['category_name']) ?>
                    </a>
                    <?php endif; ?>
                    
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; line-height: 1.4; color: #333;">
                        <?= htmlspecialchars($item['title_news']) ?>
                    </h3>
                    
                    <div style="color: #6c757d; font-size: 14px; display: flex; align-items: center; gap: 15px; margin-top: 15px;">
                        <span><i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($item['date_news'])) ?></span>
                        <span><i class="far fa-eye"></i> <?= number_format($item['view_news'] ?: 0) ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="far fa-newspaper" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h3 style="color: #6c757d; margin-bottom: 10px;">Новости не найдены</h3>
            <p style="color: #adb5bd;">Попробуйте изменить параметры поиска или выбрать другую категорию</p>
            <a href="/news" style="display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none;">
                ← Вернуться ко всем новостям
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
<div style="padding: 40px 20px; background: #f8f9fa;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <nav style="display: flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap;">
            <!-- Previous -->
            <?php if ($page > 1): ?>
            <a href="/news?p=<?= $page - 1 ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                ← Предыдущая
            </a>
            <?php endif; ?>
            
            <!-- Page numbers -->
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            if ($start > 1): ?>
                <a href="/news?p=1<?= $categorySlug ? '&category=' . $categorySlug : '' ?>" 
                   style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                    1
                </a>
                <?php if ($start > 2): ?>
                <span style="color: #6c757d;">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="/news?p=<?= $i ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>" 
               style="padding: 10px 15px; border-radius: 8px; text-decoration: none;
                      <?= $i === $page ? 'background: #007bff; color: white;' : 'background: white; border: 1px solid #dee2e6; color: #495057;' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                <span style="color: #6c757d;">...</span>
                <?php endif; ?>
                <a href="/news?p=<?= $totalPages ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>" 
                   style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                    <?= $totalPages ?>
                </a>
            <?php endif; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="/news?p=<?= $page + 1 ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                Следующая →
            </a>
            <?php endif; ?>
        </nav>
        
        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 14px;">
            Страница <?= $page ?> из <?= $totalPages ?> (всего <?= number_format($totalNews) ?> новостей)
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

// Helper function
function plural_form($n, $form1, $form2, $form3) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form3;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form3;
}

include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
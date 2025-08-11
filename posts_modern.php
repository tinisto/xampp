<?php
// Modern posts (articles) listing page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get current page
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 12;
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
$where = ["1=1"]; // Remove is_published check as column doesn't exist
$params = [];

if ($categoryId) {
    $where[] = "p.category = ?";
    $params[] = $categoryId;
}

// Search
if (isset($_GET['search']) && $_GET['search']) {
    $where[] = "(p.title_post LIKE ? OR p.text_post LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = implode(' AND ', $where);

// Get total count
$totalPosts = db_fetch_column("
    SELECT COUNT(*) 
    FROM posts p 
    WHERE $whereClause
", $params);

// Sort order
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
$orderBy = match($sort) {
    'old' => 'p.date_post ASC',
    'views' => 'p.view_post DESC',
    default => 'p.date_post DESC'
};

// Get posts
$postQuery = "
    SELECT p.*, c.category_name, c.url_slug as category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category = c.id_category
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT ? OFFSET ?
";

$posts = db_fetch_all($postQuery, array_merge($params, [$perPage, $offset]));

// Debug
// echo "<pre>Query: " . $postQuery . "</pre>";
// echo "<pre>Params: " . print_r(array_merge($params, [$perPage, $offset]), true) . "</pre>";
// echo "<pre>Posts count: " . count($posts) . "</pre>";

// Calculate pagination
$totalPages = ceil($totalPosts / $perPage);

// Get categories for filter
$categories = db_fetch_all("
    SELECT c.*, COUNT(p.id) as posts_count
    FROM categories c
    LEFT JOIN posts p ON c.id_category = p.category
    GROUP BY c.id_category
    ORDER BY c.category_name
");

// Page title
$pageTitle = $category ? 'Статьи: ' . $category['category_name'] : 'Полезные статьи';

// Section 1: Title
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Гайды, советы и руководства для школьников и студентов
        </p>
        <?php if ($totalPosts > 0): ?>
        <p style="font-size: 16px; margin-top: 10px; color: #717171;">
            Найдено <?= number_format($totalPosts) ?> <?= plural_form($totalPosts, 'статья', 'статьи', 'статей') ?>
        </p>
        <?php endif; ?>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Category filter
ob_start();
?>
<style>
.category-link {
    padding: 4px 10px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.category-link:not(.active) {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border-color: var(--border-color);
}

.category-link.active {
    background: linear-gradient(135deg, #0072ff 0%, #00c6ff 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(0, 114, 255, 0.3);
    transform: translateY(-1px);
}

.category-link:not(.active):hover {
    background: var(--link-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 114, 255, 0.2);
    border-color: var(--link-color);
}

.category-link .count {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.category-link:not(.active) .count {
    background: var(--text-secondary);
    color: var(--bg-primary);
    opacity: 0.7;
}

.category-link:not(.active):hover .count {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    opacity: 1;
}

@media (max-width: 768px) {
    .category-link {
        padding: 10px 14px;
        font-size: 13px;
    }
}
</style>

<div style="padding: 30px 20px; background: var(--bg-primary); border-bottom: 1px solid var(--border-color);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            <a href="/posts" class="category-link <?= !$categorySlug ? 'active' : '' ?>">
                Все статьи
                <span class="count"><?= array_sum(array_column($categories, 'posts_count')) ?></span>
            </a>
            
            <?php foreach ($categories as $cat): ?>
            <?php if ($cat['posts_count'] > 0): ?>
            <a href="/posts?category=<?= htmlspecialchars($cat['url_slug']) ?>" 
               class="category-link <?= $categorySlug === $cat['url_slug'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
                <span class="count"><?= $cat['posts_count'] ?></span>
            </a>
            <?php endif; ?>
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
            <form action="/posts" method="get" style="flex: 1; max-width: 400px;">
                <?php if ($categorySlug): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                <?php endif; ?>
                <div style="display: flex; gap: 10px;">
                    <input type="text" 
                           name="search" 
                           placeholder="Поиск статей..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" 
                            style="padding: 10px 20px; background: #0072ff; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Sort -->
            <select onchange="window.location.href=this.value" 
                    style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background: white;">
                <option value="/posts?<?= http_build_query(array_merge($_GET, ['sort' => 'new'])) ?>">
                    Сортировка: По дате (новые)
                </option>
                <option value="/posts?<?= http_build_query(array_merge($_GET, ['sort' => 'old'])) ?>"
                        <?= $sort === 'old' ? 'selected' : '' ?>>
                    По дате (старые)
                </option>
                <option value="/posts?<?= http_build_query(array_merge($_GET, ['sort' => 'views'])) ?>"
                        <?= $sort === 'views' ? 'selected' : '' ?>>
                    По популярности
                </option>
            </select>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Posts grid
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <?php 
        // Debug: Show posts count
        // echo "<p>Debug: Found " . count($posts) . " posts</p>";
        ?>
        <?php if (!empty($posts)): ?>
        <style>
            @media (min-width: 1200px) {
                .posts-grid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }
            }
        </style>
        <div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; max-width: 1400px; margin: 0 auto;">
            <?php foreach ($posts as $post): ?>
            <article style="background: #f8f9fa; border-radius: 12px; overflow: hidden; transition: all 0.3s; display: flex; flex-direction: column; position: relative; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="window.location.href='/post/<?= htmlspecialchars($post['url_slug']) ?>'">
                
                <div style="padding: 15px;">
                    <?php if ($post['category_name']): ?>
                    <a href="/posts?category=<?= htmlspecialchars($post['category_slug']) ?>" 
                       onclick="event.stopPropagation()"
                       style="display: inline-block; background: #e3f2fd; color: #1976d2; padding: 6px 16px; border-radius: 20px; font-size: 13px; text-decoration: none; margin-bottom: 15px; font-weight: 500;">
                        <?= htmlspecialchars($post['category_name']) ?>
                    </a>
                    <?php endif; ?>
                    
                    <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 15px; line-height: 1.3; color: #333;">
                        <?= htmlspecialchars($post['title_post']) ?>
                    </h3>
                    
                    <p style="color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                        <?= htmlspecialchars(mb_substr(strip_tags($post['text_post']), 0, 150)) ?>...
                    </p>
                    
                    <div style="color: #6c757d; font-size: 14px; display: flex; align-items: center; gap: 15px; margin-top: auto;">
                        <span><i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($post['date_post'])) ?></span>
                        <span><i class="far fa-eye"></i> <?= number_format($post['view_post']) ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 30px 20px;">
            <i class="fas fa-book-open" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h3 style="color: #6c757d; margin-bottom: 10px;">Статьи не найдены</h3>
            <p style="color: #adb5bd;">Попробуйте изменить параметры поиска или выбрать другую категорию</p>
            <a href="/posts" style="display: inline-block; margin-top: 20px; color: #0072ff; text-decoration: none;">
                ← Вернуться ко всем статьям
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
            <a href="/posts?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                ← Предыдущая
            </a>
            <?php endif; ?>
            
            <!-- Page numbers -->
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            if ($start > 1): ?>
                <a href="/posts?<?= http_build_query(array_merge($_GET, ['p' => 1])) ?>" 
                   style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                    1
                </a>
                <?php if ($start > 2): ?>
                <span style="color: #6c757d;">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="/posts?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
               style="padding: 10px 15px; border-radius: 8px; text-decoration: none;
                      <?= $i === $page ? 'background: #0072ff; color: white;' : 'background: white; border: 1px solid #dee2e6; color: #495057;' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                <span style="color: #6c757d;">...</span>
                <?php endif; ?>
                <a href="/posts?<?= http_build_query(array_merge($_GET, ['p' => $totalPages])) ?>" 
                   style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                    <?= $totalPages ?>
                </a>
            <?php endif; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="/posts?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                Следующая →
            </a>
            <?php endif; ?>
        </nav>
        
        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 14px;">
            Страница <?= $page ?> из <?= $totalPages ?> (всего <?= number_format($totalPosts) ?> статей)
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
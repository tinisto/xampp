<?php
// Modern events listing page
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
$where = ["e.approved = 1"];
$params = [];

if ($categoryId) {
    $where[] = "e.category_id = ?";
    $params[] = $categoryId;
}

$whereClause = implode(' AND ', $where);

// Get total count
$totalEvents = db_fetch_column("
    SELECT COUNT(*) 
    FROM events e 
    WHERE $whereClause
", $params);

// Sort order
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$orderBy = match($sort) {
    'date_asc' => 'e.event_date ASC',
    'views' => 'e.views DESC',
    'title' => 'e.title ASC',
    default => 'e.event_date DESC'
};

// Get events
$events = db_fetch_all("
    SELECT e.*, c.category_name, c.url_slug as category_slug
    FROM events e
    LEFT JOIN categories c ON e.category_id = c.id_category
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT ? OFFSET ?
", array_merge($params, [$perPage, $offset]));

// Calculate pagination
$totalPages = ceil($totalEvents / $perPage);

// Get categories for filter
$categories = db_fetch_all("
    SELECT c.*, COUNT(e.id) as events_count
    FROM categories c
    LEFT JOIN events e ON c.id_category = e.category_id AND e.approved = 1
    GROUP BY c.id_category
    ORDER BY c.category_name
");

// Page title
$pageTitle = $category ? 'События: ' . $category['category_name'] : 'События образования';

// Section 1: Title
ob_start();
?>
<div style="padding: 20px 20px 20px; background: white; box-shadow: 0 1px 0 rgba(0,0,0,0.08);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: 44px; font-weight: 800; margin-bottom: 16px; color: #222222; letter-spacing: -0.02em;">
            <?= htmlspecialchars($pageTitle) ?>
        </h1>
        <p style="font-size: 18px; color: #717171; line-height: 1.5;">
            Образовательные мероприятия, конференции и олимпиады
        </p>
        <?php if ($totalEvents > 0): ?>
        <p style="font-size: 16px; margin-top: 10px; color: #717171;">
            Найдено <?= number_format($totalEvents) ?> <?= plural_form($totalEvents, 'событие', 'события', 'событий') ?>
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
.event-category-link {
    padding: 12px 18px;
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.event-category-link:not(.active) {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border-color: var(--border-color);
}

.event-category-link.active {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    transform: translateY(-1px);
}

.event-category-link:not(.active):hover {
    background: #ff6b6b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 107, 107, 0.2);
    border-color: #ff6b6b;
}

.event-category-link .count {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.event-category-link:not(.active) .count {
    background: var(--text-secondary);
    color: var(--bg-primary);
    opacity: 0.7;
}

.event-category-link:not(.active):hover .count {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    opacity: 1;
}

@media (max-width: 768px) {
    .event-category-link {
        padding: 10px 14px;
        font-size: 13px;
    }
}
</style>

<div style="padding: 30px 20px; background: var(--bg-primary); border-bottom: 1px solid var(--border-color);">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <a href="/events" class="event-category-link <?= !$categorySlug ? 'active' : '' ?>">
                Все события
                <span class="count"><?= array_sum(array_column($categories, 'events_count')) ?></span>
            </a>
            
            <?php foreach ($categories as $cat): ?>
            <?php if ($cat['events_count'] > 0): ?>
            <a href="/events?category=<?= htmlspecialchars($cat['url_slug']) ?>" 
               class="event-category-link <?= $categorySlug === $cat['url_slug'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
                <span class="count"><?= $cat['events_count'] ?></span>
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
            <form action="/events" method="get" style="flex: 1; max-width: 400px;">
                <?php if ($categorySlug): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                <?php endif; ?>
                <div style="display: flex; gap: 10px;">
                    <input type="text" 
                           name="search" 
                           placeholder="Поиск событий..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="submit" 
                            style="padding: 10px 20px; background: #ff6b6b; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Sort -->
            <select onchange="window.location.href=this.value" 
                    style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background: white;">
                <option value="/events?<?= http_build_query(array_merge($_GET, ['sort' => 'date'])) ?>">
                    Сортировка: По дате (новые)
                </option>
                <option value="/events?<?= http_build_query(array_merge($_GET, ['sort' => 'date_asc'])) ?>"
                        <?= $sort === 'date_asc' ? 'selected' : '' ?>>
                    По дате (старые)
                </option>
                <option value="/events?<?= http_build_query(array_merge($_GET, ['sort' => 'views'])) ?>"
                        <?= $sort === 'views' ? 'selected' : '' ?>>
                    По популярности
                </option>
                <option value="/events?<?= http_build_query(array_merge($_GET, ['sort' => 'title'])) ?>"
                        <?= $sort === 'title' ? 'selected' : '' ?>>
                    По алфавиту
                </option>
            </select>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Events grid
ob_start();
?>
<div style="padding: 30px 20px; background: white;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <?php if (!empty($events)): ?>
        <style>
            @media (min-width: 1200px) {
                .events-grid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }
            }
        </style>
        <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; max-width: 1400px; margin: 0 auto;">
            <?php foreach ($events as $event): ?>
            <article style="background: #f8f9fa; border-radius: 12px; overflow: hidden; transition: all 0.3s; cursor: pointer; border-left: 4px solid #ff6b6b;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                     onclick="window.location.href='/event/<?= htmlspecialchars($event['url_slug']) ?>'">
                
                <div style="padding: 25px;">
                    <!-- Event date badge -->
                    <?php if ($event['event_date']): ?>
                    <div style="display: inline-block; background: #ff6b6b; color: white; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 15px;">
                        <i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($event['event_date'])) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($event['category_name']): ?>
                    <a href="/events?category=<?= htmlspecialchars($event['category_slug']) ?>" 
                       onclick="event.stopPropagation()"
                       style="display: inline-block; background: #fff5f5; color: #e53e3e; padding: 4px 12px; border-radius: 15px; font-size: 12px; text-decoration: none; margin-bottom: 10px; margin-left: 10px;">
                        <?= htmlspecialchars($event['category_name']) ?>
                    </a>
                    <?php endif; ?>
                    
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; line-height: 1.4; color: #333;">
                        <?= htmlspecialchars($event['title']) ?>
                    </h3>
                    
                    <?php if ($event['description']): ?>
                    <p style="color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 15px;">
                        <?= htmlspecialchars(mb_substr(strip_tags($event['description']), 0, 120)) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <!-- Event details -->
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 15px;">
                        <?php if ($event['location']): ?>
                        <div style="color: #6c757d; font-size: 13px;">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($event['organizer']): ?>
                        <div style="color: #6c757d; font-size: 13px;">
                            <i class="fas fa-users"></i> <?= htmlspecialchars($event['organizer']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <div style="color: #6c757d; font-size: 13px; display: flex; gap: 15px; margin-top: 5px;">
                            <span><i class="far fa-eye"></i> <?= number_format($event['views'] ?: 0) ?></span>
                            <?php if ($event['registration_deadline']): ?>
                            <span><i class="far fa-clock"></i> До <?= date('d.m', strtotime($event['registration_deadline'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 30px 20px;">
            <i class="far fa-calendar-alt" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h3 style="color: #6c757d; margin-bottom: 10px;">События не найдены</h3>
            <p style="color: #adb5bd;">Попробуйте изменить параметры поиска или выбрать другую категорию</p>
            <a href="/events" style="display: inline-block; margin-top: 20px; color: #ff6b6b; text-decoration: none;">
                ← Вернуться ко всем событиям
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
            <a href="/events?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                ← Предыдущая
            </a>
            <?php endif; ?>
            
            <!-- Page numbers -->
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            if ($start > 1): ?>
                <a href="/events?<?= http_build_query(array_merge($_GET, ['p' => 1])) ?>" 
                   style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                    1
                </a>
                <?php if ($start > 2): ?>
                <span style="color: #6c757d;">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="/events?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>" 
               style="padding: 10px 15px; border-radius: 8px; text-decoration: none;
                      <?= $i === $page ? 'background: #ff6b6b; color: white;' : 'background: white; border: 1px solid #dee2e6; color: #495057;' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                <span style="color: #6c757d;">...</span>
                <?php endif; ?>
                <a href="/events?<?= http_build_query(array_merge($_GET, ['p' => $totalPages])) ?>" 
                   style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                    <?= $totalPages ?>
                </a>
            <?php endif; ?>
            
            <!-- Next -->
            <?php if ($page < $totalPages): ?>
            <a href="/events?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>" 
               style="padding: 10px 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #495057;">
                Следующая →
            </a>
            <?php endif; ?>
        </nav>
        
        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 14px;">
            Страница <?= $page ?> из <?= $totalPages ?> (всего <?= number_format($totalEvents) ?> событий)
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

include $_SERVER['DOCUMENT_ROOT'] . '/template.php';
?>
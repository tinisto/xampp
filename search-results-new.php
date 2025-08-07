<?php
// Search results page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get search query
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($searchQuery)) {
    header('Location: /');
    exit();
}

// Basic validation
if (!preg_match("/^[\p{L}0-9\s]+$/u", $searchQuery)) {
    header('Location: /');
    exit();
}

// Check length
if (mb_strlen($searchQuery) < 2 || mb_strlen($searchQuery) > 100) {
    header('Location: /');
    exit();
}

// Get page number
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Prepare search query for SQL
$searchPattern = '%' . $connection->real_escape_string($searchQuery) . '%';

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Результаты поиска', [
    'fontSize' => '32px',
    'margin' => '30px 0',
    'subtitle' => 'По запросу: "' . htmlspecialchars($searchQuery) . '"'
]);
$greyContent1 = ob_get_clean();

// Section 2: Search tabs
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/category-navigation.php';
$searchTabs = [
    ['title' => 'Все результаты', 'url' => '/search?query=' . urlencode($searchQuery)],
    ['title' => 'Статьи', 'url' => '/search?query=' . urlencode($searchQuery) . '&type=posts'],
    ['title' => 'Новости', 'url' => '/search?query=' . urlencode($searchQuery) . '&type=news'],
    ['title' => 'Учебные заведения', 'url' => '/search?query=' . urlencode($searchQuery) . '&type=edu'],
    ['title' => 'Тесты', 'url' => '/search?query=' . urlencode($searchQuery) . '&type=tests']
];
renderCategoryNavigation($searchTabs, $_SERVER['REQUEST_URI']);
$greyContent2 = ob_get_clean();

// Section 3: Search stats
ob_start();
// Count results by type
$counts = [];
$typeFilter = $_GET['type'] ?? '';

// Posts count
$postCount = 0;
if (!$typeFilter || $typeFilter == 'posts') {
    $stmt = $connection->prepare("SELECT COUNT(*) FROM posts WHERE (title_post LIKE ? OR text_post LIKE ?)");
    $stmt->bind_param("ss", $searchPattern, $searchPattern);
    $stmt->execute();
    $stmt->bind_result($postCount);
    $stmt->fetch();
    $stmt->close();
    $counts['posts'] = $postCount;
}

// News count
$newsCount = 0;
if (!$typeFilter || $typeFilter == 'news') {
    $stmt = $connection->prepare("SELECT COUNT(*) FROM news WHERE (title_news LIKE ? OR content_news LIKE ?) AND status = 'published'");
    $stmt->bind_param("ss", $searchPattern, $searchPattern);
    $stmt->execute();
    $stmt->bind_result($newsCount);
    $stmt->fetch();
    $stmt->close();
    $counts['news'] = $newsCount;
}

// Educational institutions count
$eduCount = 0;
if (!$typeFilter || $typeFilter == 'edu') {
    // Schools
    $stmt = $connection->prepare("SELECT COUNT(*) FROM schools WHERE title_school LIKE ?");
    $stmt->bind_param("s", $searchPattern);
    $stmt->execute();
    $stmt->bind_result($schoolCount);
    $stmt->fetch();
    $stmt->close();
    
    // VPO
    $stmt = $connection->prepare("SELECT COUNT(*) FROM vpo WHERE title LIKE ?");
    $stmt->bind_param("s", $searchPattern);
    $stmt->execute();
    $stmt->bind_result($vpoCount);
    $stmt->fetch();
    $stmt->close();
    
    // SPO
    $stmt = $connection->prepare("SELECT COUNT(*) FROM spo WHERE title LIKE ?");
    $stmt->bind_param("s", $searchPattern);
    $stmt->execute();
    $stmt->bind_result($spoCount);
    $stmt->fetch();
    $stmt->close();
    
    $eduCount = $schoolCount + $vpoCount + $spoCount;
    $counts['edu'] = $eduCount;
}

// Tests count
$testCount = 0;
if (!$typeFilter || $typeFilter == 'tests') {
    $stmt = $connection->prepare("SELECT COUNT(*) FROM tests WHERE title_test LIKE ? AND status = 'active'");
    $stmt->bind_param("s", $searchPattern);
    $stmt->execute();
    $stmt->bind_result($testCount);
    $stmt->fetch();
    $stmt->close();
    $counts['tests'] = $testCount;
}

$totalResults = array_sum($counts);
?>
<div style="text-align: center; padding: 20px; color: #666;">
    Найдено <strong style="color: #28a745;"><?= number_format($totalResults) ?></strong> результатов
    <?php if (!$typeFilter && count($counts) > 1): ?>
        (<?php 
        $parts = [];
        if (isset($counts['posts']) && $counts['posts'] > 0) $parts[] = $counts['posts'] . ' статей';
        if (isset($counts['news']) && $counts['news'] > 0) $parts[] = $counts['news'] . ' новостей';
        if (isset($counts['edu']) && $counts['edu'] > 0) $parts[] = $counts['edu'] . ' учебных заведений';
        if (isset($counts['tests']) && $counts['tests'] > 0) $parts[] = $counts['tests'] . ' тестов';
        echo implode(', ', $parts);
        ?>)
    <?php endif; ?>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: New search box
ob_start();
echo '<div style="text-align: center; padding: 20px;">';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline([
    'placeholder' => 'Новый поиск...',
    'buttonText' => 'Найти',
    'width' => '500px',
    'value' => htmlspecialchars($searchQuery)
]);
echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Search results
ob_start();

$results = [];

// Collect results based on type filter
if (!$typeFilter || $typeFilter == 'posts') {
    $stmt = $connection->prepare("
        SELECT 'post' as type, id as item_id, title_post as title, url_slug as url, 
               SUBSTRING(text_post, 1, 200) as description, date_post as created_at,
               c.title_category, c.url_category
        FROM posts p
        LEFT JOIN categories c ON p.category = c.id_category
        WHERE title_post LIKE ? OR text_post LIKE ?
        ORDER BY date_post DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ssii", $searchPattern, $searchPattern, $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}

if (!$typeFilter || $typeFilter == 'news') {
    $remaining = $perPage - count($results);
    if ($remaining > 0) {
        $stmt = $connection->prepare("
            SELECT 'news' as type, id_news as item_id, title_news as title, url_news as url,
                   SUBSTRING(content_news, 1, 200) as description, created_at,
                   c.title_category, c.url_category
            FROM news n
            LEFT JOIN categories c ON n.category_id = c.id_category
            WHERE (title_news LIKE ? OR content_news LIKE ?) AND status = 'published'
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ssi", $searchPattern, $searchPattern, $remaining);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}

if (!$typeFilter || $typeFilter == 'edu') {
    $remaining = $perPage - count($results);
    if ($remaining > 0) {
        // Schools
        $stmt = $connection->prepare("
            SELECT 'school' as type, id_school as item_id, title_school as title, 
                   url_school as url, city_school as description, NULL as created_at,
                   r.title_region as title_category, r.url_region as url_category
            FROM schools s
            LEFT JOIN regions r ON s.region_id = r.id_region
            WHERE title_school LIKE ?
            LIMIT ?
        ");
        $stmt->bind_param("si", $searchPattern, $remaining);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}

// Display results
if (count($results) > 0) {
    echo '<div style="padding: 20px;">';
    foreach ($results as $item) {
        $typeLabel = [
            'post' => 'Статья',
            'news' => 'Новость',
            'school' => 'Школа',
            'vpo' => 'ВУЗ',
            'spo' => 'СПО',
            'test' => 'Тест'
        ][$item['type']] ?? '';
        
        $itemUrl = match($item['type']) {
            'post' => '/post/' . $item['url'],
            'news' => '/news/' . $item['url'],
            'school' => '/school/' . $item['url'],
            'vpo' => '/vpo/' . $item['url'],
            'spo' => '/spo/' . $item['url'],
            'test' => '/test/' . $item['url'],
            default => '#'
        };
        ?>
        <div style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; gap: 15px; align-items: start;">
                <div style="flex: 1;">
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px;">
                            <?= htmlspecialchars($typeLabel) ?>
                        </span>
                        <?php if ($item['title_category']): ?>
                            <a href="/category/<?= htmlspecialchars($item['url_category']) ?>" style="color: #666; font-size: 14px; text-decoration: none;">
                                <?= htmlspecialchars($item['title_category']) ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($item['created_at']): ?>
                            <span style="color: #999; font-size: 14px;">
                                <?= date('d.m.Y', strtotime($item['created_at'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 style="margin: 0 0 10px 0;">
                        <a href="<?= htmlspecialchars($itemUrl) ?>" style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    </h3>
                    <?php if ($item['description']): ?>
                        <p style="color: #666; margin: 0; line-height: 1.6;">
                            <?= htmlspecialchars(strip_tags($item['description'])) ?>...
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    echo '</div>';
} else {
    echo '<div style="text-align: center; padding: 60px 20px; color: #666;">
            <i class="fas fa-search fa-3x" style="opacity: 0.3; margin-bottom: 20px;"></i>
            <p style="font-size: 18px; margin: 0;">По вашему запросу ничего не найдено</p>
            <p style="margin-top: 10px;">Попробуйте изменить поисковый запрос</p>
          </div>';
}

$greyContent5 = ob_get_clean();

// Section 6: Pagination (if needed)
ob_start();
if ($totalResults > $perPage) {
    $totalPages = ceil($totalResults / $perPage);
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    $baseUrl = '/search?query=' . urlencode($searchQuery) . ($typeFilter ? "&type=$typeFilter" : '');
    renderPaginationModern($page, $totalPages, $baseUrl);
}
$greyContent6 = ob_get_clean();

// Section 7: No comments for search results
$blueContent = '';

// Set page title
$pageTitle = 'Поиск: ' . htmlspecialchars($searchQuery);
$metaD = 'Результаты поиска по запросу: ' . htmlspecialchars($searchQuery);
$metaK = htmlspecialchars($searchQuery) . ', поиск, результаты';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
<?php
// Search process page - migrated to use real_template.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get search query
$searchQuery = $_GET['query'] ?? '';
$searchQuery = trim($searchQuery);

if (empty($searchQuery)) {
    header('Location: /search');
    exit();
}

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Search in different tables
$results = [];
$totalResults = 0;

// Escape search query for SQL
$searchLike = '%' . $connection->real_escape_string($searchQuery) . '%';

// Search in schools
$query = "SELECT 'school' as type, id_school as id, name, url_slug, address, NULL as text 
          FROM schools 
          WHERE name LIKE ? OR address LIKE ?
          LIMIT ?, ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('ssii', $searchLike, $searchLike, $offset, $limit);
$stmt->execute();
$schoolResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Search in VPO
$query = "SELECT 'vpo' as type, id_university as id, name, url_slug, address, NULL as text 
          FROM universities 
          WHERE name LIKE ? OR address LIKE ?
          LIMIT ?, ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('ssii', $searchLike, $searchLike, $offset, $limit);
$stmt->execute();
$vpoResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Search in SPO
$query = "SELECT 'spo' as type, id_college as id, name, url_slug, address, NULL as text 
          FROM colleges 
          WHERE name LIKE ? OR address LIKE ?
          LIMIT ?, ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('ssii', $searchLike, $searchLike, $offset, $limit);
$stmt->execute();
$spoResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Search in posts
$query = "SELECT 'post' as type, id_post as id, title_post as name, url_post as url_slug, 
          NULL as address, LEFT(text_post, 200) as text
          FROM posts 
          WHERE title_post LIKE ? OR text_post LIKE ?
          LIMIT ?, ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('ssii', $searchLike, $searchLike, $offset, $limit);
$stmt->execute();
$postResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Search in news
$query = "SELECT 'news' as type, id, title_news as name, url_slug, 
          NULL as address, LEFT(text_news, 200) as text
          FROM news 
          WHERE title_news LIKE ? OR text_news LIKE ? AND approved = 1
          LIMIT ?, ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('ssii', $searchLike, $searchLike, $offset, $limit);
$stmt->execute();
$newsResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Combine all results
$results = array_merge($schoolResults, $vpoResults, $spoResults, $postResults, $newsResults);
$totalResults = count($results);

// Get total count for pagination
$totalQuery = "SELECT 
    (SELECT COUNT(*) FROM schools WHERE name LIKE ? OR address LIKE ?) +
    (SELECT COUNT(*) FROM universities WHERE name LIKE ? OR address LIKE ?) +
    (SELECT COUNT(*) FROM colleges WHERE name LIKE ? OR address LIKE ?) +
    (SELECT COUNT(*) FROM posts WHERE title_post LIKE ? OR text_post LIKE ?) +
    (SELECT COUNT(*) FROM news WHERE (title_news LIKE ? OR text_news LIKE ?) AND approved = 1) 
    as total";
$stmt = $connection->prepare($totalQuery);
$stmt->bind_param('ssssssssss', 
    $searchLike, $searchLike, 
    $searchLike, $searchLike, 
    $searchLike, $searchLike, 
    $searchLike, $searchLike,
    $searchLike, $searchLike
);
$stmt->execute();
$totalCount = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$totalPages = ceil($totalCount / $limit);

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('Результаты поиска', [
    'fontSize' => '28px',
    'margin' => '30px 0',
    'subtitle' => 'По запросу: "' . htmlspecialchars($searchQuery) . '" найдено ' . $totalCount . ' результатов'
]);
$greyContent1 = ob_get_clean();

// Section 2: Search form
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/search-inline.php';
renderSearchInline('Введите новый поисковый запрос...', 'Найти', $searchQuery);
$greyContent2 = ob_get_clean();

// Section 3: Empty
$greyContent3 = '';

// Section 4: Empty
$greyContent4 = '';

// Section 5: Search results
ob_start();
?>
<div style="padding: 20px;">
    <?php if (empty($results)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-search" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-primary, #333);">Ничего не найдено</h3>
            <p style="color: var(--text-secondary, #666);">
                По вашему запросу "<?= htmlspecialchars($searchQuery) ?>" ничего не найдено.
            </p>
            <p style="color: var(--text-secondary, #666); margin-top: 20px;">
                Попробуйте изменить поисковый запрос или воспользуйтесь навигацией сайта.
            </p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px; max-width: 900px; margin: 0 auto;">
            <?php foreach ($results as $result): ?>
                <?php
                // Determine URL and icon based on type
                switch ($result['type']) {
                    case 'school':
                        $url = '/school/' . $result['url_slug'];
                        $icon = 'fa-school';
                        $typeLabel = 'Школа';
                        break;
                    case 'vpo':
                        $url = '/vpo/' . $result['url_slug'];
                        $icon = 'fa-graduation-cap';
                        $typeLabel = 'ВПО';
                        break;
                    case 'spo':
                        $url = '/spo/' . $result['url_slug'];
                        $icon = 'fa-university';
                        $typeLabel = 'СПО';
                        break;
                    case 'post':
                        $url = '/post/' . $result['url_slug'];
                        $icon = 'fa-file-alt';
                        $typeLabel = 'Статья';
                        break;
                    case 'news':
                        $url = '/news/' . $result['url_slug'];
                        $icon = 'fa-newspaper';
                        $typeLabel = 'Новость';
                        break;
                }
                ?>
                <a href="<?= htmlspecialchars($url) ?>" class="search-result">
                    <div class="result-icon">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="result-content">
                        <h4><?= htmlspecialchars($result['name']) ?></h4>
                        <?php if (!empty($result['address'])): ?>
                            <p class="result-address">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($result['address']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($result['text'])): ?>
                            <p class="result-text">
                                <?= htmlspecialchars(strip_tags($result['text'])) ?>...
                            </p>
                        <?php endif; ?>
                        <span class="result-type"><?= $typeLabel ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.search-result {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: var(--surface, #ffffff);
    border-radius: 12px;
    text-decoration: none;
    color: var(--text-primary, #333);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.search-result:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.result-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 50px;
    width: 50px;
    height: 50px;
    background: #28a745;
    color: white;
    border-radius: 10px;
    font-size: 20px;
}

.result-content {
    flex: 1;
}

.result-content h4 {
    margin: 0 0 10px 0;
    color: var(--text-primary, #333);
    font-size: 18px;
    line-height: 1.4;
}

.result-address {
    margin: 5px 0;
    color: var(--text-secondary, #666);
    font-size: 14px;
}

.result-address i {
    width: 16px;
    margin-right: 5px;
    color: #28a745;
}

.result-text {
    margin: 10px 0;
    color: var(--text-secondary, #666);
    font-size: 14px;
    line-height: 1.5;
}

.result-type {
    display: inline-block;
    margin-top: 10px;
    padding: 4px 12px;
    background: #e8f5e9;
    color: #28a745;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

[data-theme="dark"] .search-result {
    background: var(--surface-dark, #2d3748);
}

[data-theme="dark"] .result-content h4 {
    color: var(--text-primary, #e4e6eb);
}

[data-theme="dark"] .result-address,
[data-theme="dark"] .result-text {
    color: var(--text-secondary, #b0b3b8);
}

[data-theme="dark"] .result-type {
    background: rgba(40, 167, 69, 0.2);
}

@media (max-width: 768px) {
    .search-result {
        padding: 15px;
    }
    
    .result-icon {
        min-width: 40px;
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Pagination
ob_start();
if ($totalPages > 1) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/pagination-modern.php';
    renderPaginationModern($page, $totalPages, '/search-process?query=' . urlencode($searchQuery) . '&page=');
}
$greyContent6 = ob_get_clean();

// Blue section: Empty
$blueContent = '';

// Page title
$pageTitle = 'Поиск: ' . htmlspecialchars($searchQuery) . ' - 11-классники';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
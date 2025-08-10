<?php
// Homepage - migrated to use real_template.php
// For local development - skip database connections
$connection = null;
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

// Prepare content for template sections
ob_start();
?>
<script>
// Remove hash from URL on homepage
if (window.location.hash) {
    history.replaceState(null, null, window.location.pathname + window.location.search);
}
</script>
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle('11-классники', [
    'fontSize' => '36px',
    'margin' => '30px 0',
    'subtitle' => 'Образовательный портал для школьников, абитуриентов и студентов'
]);
$greyContent1 = ob_get_clean();

// Section 2: Empty for homepage
$greyContent2 = '';

// Section 3: Stats section
ob_start();
?>
<div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; padding: 20px;">
    <?php
    // Get stats from database or use mock data for local development
    if ($connection) {
        $stats = [
            ['count' => mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM schools"))['c'], 'label' => 'Школ'],
            ['count' => mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM vpo"))['c'], 'label' => 'ВУЗов'],
            ['count' => mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM spo"))['c'], 'label' => 'ССУЗов'],
            ['count' => mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM posts"))['c'], 'label' => 'Статей']
        ];
    } else {
        // Mock data for local development
        $stats = [
            ['count' => 3318, 'label' => 'Школ'],
            ['count' => 2520, 'label' => 'ВУЗов'], 
            ['count' => 1850, 'label' => 'ССУЗов'],
            ['count' => 496, 'label' => 'Статей']
        ];
    }
    foreach ($stats as $stat): ?>
        <div style="text-align: center;">
            <div style="font-size: 32px; font-weight: 700; color: #007bff;"><?= number_format($stat['count']) ?></div>
            <div style="font-size: 16px; color: #666;"><?= $stat['label'] ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Search
ob_start();
echo '<div style="text-align: center; padding: 20px;">';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/unified-search.php';
renderUnifiedSearch([
    'placeholder' => 'Поиск по сайту...',
    'buttonText' => 'Найти',
    'style' => 'large',
    'name' => 'search',
    'maxWidth' => '500px'
]);
echo '</div>';
$greyContent4 = ob_get_clean();

// Section 5: Main content - posts grid
ob_start();
// Get featured posts
$queryFeatured = "
    (SELECT id, title_post, text_post, url_slug, date_post, '11-классники' as category_name, '/category/11-klassniki' as category_url, 'teal' as badge_color 
     FROM posts WHERE category = 21 ORDER BY date_post DESC LIMIT 8)
    UNION ALL
    (SELECT id, title_post, text_post, url_slug, date_post, 'Абитуриентам' as category_name, '/category/abiturientam' as category_url, 'orange' as badge_color 
     FROM posts WHERE category = 6 ORDER BY date_post DESC LIMIT 8)
    ORDER BY date_post DESC
";
$resultFeatured = mysqli_query($connection, $queryFeatured);
$posts = [];
while ($row = mysqli_fetch_assoc($resultFeatured)) {
    $posts[] = [
        'id_news' => $row['id'],
        'title_news' => $row['title_post'],
        'url_news' => $row['url_slug'],
        'image_news' => file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id']}_1.jpg") 
            ? "/images/posts-images/{$row['id']}_1.jpg" 
            : '/images/default-news.jpg',
        'created_at' => $row['date_post'],
        'category_title' => $row['category_name'],
        'category_url' => $row['category_url']
    ];
}

if (count($posts) > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($posts, 'post', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
} else {
    echo '<div style="text-align: center; padding: 60px 20px;">';
    echo '<h3 style="color: #666; margin-bottom: 20px;">Статьи скоро появятся</h3>';
    echo '<p style="color: #999;">В данный момент статьи на сайте отсутствуют.</p>';
    echo '<p style="color: #999;">Проверьте другие разделы: <a href="/vpo-all-regions" style="color: #007bff;">ВУЗы</a>, <a href="/spo-all-regions" style="color: #007bff;">ССУЗы</a>, <a href="/schools-all-regions" style="color: #007bff;">Школы</a>, <a href="/news" style="color: #007bff;">Новости</a></p>';
    echo '</div>';
}
$greyContent5 = ob_get_clean();

// Section 6 & 7: Empty for homepage
$greyContent6 = '';
$blueContent = '';

// Set page title
$pageTitle = 'Главная';
$metaD = '11-классники - образовательный портал для школьников, абитуриентов и студентов. Школы, ВУЗы, колледжи, тесты ЕГЭ и полезные статьи.';
$metaK = '11-классники, образование, школы, ВУЗы, СПО, абитуриенты, ЕГЭ, тесты';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
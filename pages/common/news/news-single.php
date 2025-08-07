<?php
// Single news page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$newsUrl = $_GET['url_news'] ?? '';

// Get news article
$query = "SELECT n.*, c.title_category, c.url_category, 
                 u.username as author_name,
                 (SELECT COUNT(*) FROM comments WHERE entity_type = 'news' AND entity_id = n.id_news) as comment_count
          FROM news n
          LEFT JOIN categories c ON n.category_id = c.id_category
          LEFT JOIN users u ON n.author_id = u.id
          WHERE n.url_news = ? AND n.status = 'published'";

$stmt = $connection->prepare($query);
$stmt->bind_param("s", $newsUrl);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    header("Location: /404");
    exit;
}

// Update views
$updateViews = "UPDATE news SET views = views + 1 WHERE id_news = ?";
$stmt = $connection->prepare($updateViews);
$stmt->bind_param("i", $news['id_news']);
$stmt->execute();

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($news['title_news'], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
?>
<div style="padding: 15px 20px;">
    <nav style="font-size: 14px; color: #666;">
        <a href="/" style="color: #28a745; text-decoration: none;">Главная</a>
        <span style="margin: 0 10px;">/</span>
        <a href="/news" style="color: #28a745; text-decoration: none;">Новости</a>
        <?php if ($news['title_category']): ?>
            <span style="margin: 0 10px;">/</span>
            <a href="/category/<?= htmlspecialchars($news['url_category']) ?>" style="color: #28a745; text-decoration: none;">
                <?= htmlspecialchars($news['title_category']) ?>
            </a>
        <?php endif; ?>
        <span style="margin: 0 10px;">/</span>
        <span><?= htmlspecialchars($news['title_news']) ?></span>
    </nav>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center;">
        <?php if ($news['author_name']): ?>
            <div>
                <i class="fas fa-user" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($news['author_name']) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <i class="fas fa-calendar" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= date('d.m.Y', strtotime($news['created_at'])) ?></span>
        </div>
        <div>
            <i class="fas fa-eye" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= number_format($news['views']) ?> просмотров</span>
        </div>
        <div>
            <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $news['comment_count'] ?> комментариев</span>
        </div>
    </div>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Empty for single news
$greyContent4 = '';

// Section 5: Main content
ob_start();
?>
<div style="padding: 30px 20px;">
    <?php if ($news['image_news'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $news['image_news'])): ?>
        <div style="margin-bottom: 30px; text-align: center;">
            <img src="<?= htmlspecialchars($news['image_news']) ?>" 
                 alt="<?= htmlspecialchars($news['title_news']) ?>" 
                 style="max-width: 100%; height: auto; border-radius: 8px;">
        </div>
    <?php endif; ?>
    
    <div style="font-size: 16px; line-height: 1.8; color: #333;">
        <?= $news['content_news'] ?>
    </div>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related news
ob_start();
// Get related news
$relatedQuery = "SELECT id_news, title_news, url_news, image_news, created_at 
                 FROM news 
                 WHERE category_id = ? AND id_news != ? AND status = 'published'
                 ORDER BY created_at DESC 
                 LIMIT 4";
$stmt = $connection->prepare($relatedQuery);
$stmt->bind_param("ii", $news['category_id'], $news['id_news']);
$stmt->execute();
$relatedResult = $stmt->get_result();
$relatedNews = [];
while ($row = $relatedResult->fetch_assoc()) {
    $row['category_title'] = $news['title_category'];
    $row['category_url'] = $news['url_category'];
    $relatedNews[] = $row;
}

if (count($relatedNews) > 0) {
    echo '<div style="padding: 20px;">';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
    renderRealTitle('Похожие новости', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
    
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    renderCardsGrid($relatedNews, 'news', [
        'columns' => 4,
        'gap' => 20,
        'showBadge' => true
    ]);
    echo '</div>';
}
$greyContent6 = ob_get_clean();

// Section 7: Comments
ob_start();
?>
<div style="padding: 30px 20px; color: white;">
    <h3 style="margin: 0 0 20px 0;">Комментарии (<?= $news['comment_count'] ?>)</h3>
    <?php
    // Include comments component
    include $_SERVER['DOCUMENT_ROOT'] . '/comments/display_comments.php';
    displayComments('news', $news['id_news']);
    ?>
</div>
<?php
$blueContent = ob_get_clean();

// Set page title
$pageTitle = $news['title_news'];

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
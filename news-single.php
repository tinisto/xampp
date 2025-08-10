<?php
// Modern news single page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get news ID or slug from URL
$newsId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$newsSlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$newsId && !$newsSlug) {
    header('Location: /news');
    exit;
}

// Fetch news article
if ($newsId) {
    $news = db_fetch_one("
        SELECT n.*, c.category_name, c.url_slug as category_slug 
        FROM news n
        LEFT JOIN categories c ON n.category_id = c.id_category
        WHERE n.id = ? AND n.approved = 1
    ", [$newsId]);
} else {
    $news = db_fetch_one("
        SELECT n.*, c.category_name, c.url_slug as category_slug 
        FROM news n
        LEFT JOIN categories c ON n.category_id = c.id_category
        WHERE n.url_slug = ? AND n.approved = 1
    ", [$newsSlug]);
}

if (!$news) {
    header('HTTP/1.0 404 Not Found');
    include $_SERVER['DOCUMENT_ROOT'] . '/404-new.php';
    exit;
}

// Update views
db_query("UPDATE news SET view_news = view_news + 1 WHERE id = ?", [$news['id']]);

// Fetch related news
$relatedNews = db_fetch_all("
    SELECT id, title_news, url_slug, image_news, date_news, view_news
    FROM news 
    WHERE category_id = ? AND id != ? AND approved = 1
    ORDER BY date_news DESC
    LIMIT 4
", [$news['category_id'], $news['id']]);

// Prepare content for template
$pageTitle = $news['title_news'];

// Section 1: Title and metadata
ob_start();
?>
<div style="padding: 30px 20px; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <?php if ($news['category_name']): ?>
        <a href="/news/category/<?= htmlspecialchars($news['category_slug']) ?>" 
           style="color: #007bff; text-decoration: none; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
            <?= htmlspecialchars($news['category_name']) ?>
        </a>
        <?php endif; ?>
        
        <h1 style="font-size: 32px; font-weight: 700; color: #333; margin: 15px 0; line-height: 1.3;">
            <?= htmlspecialchars($news['title_news']) ?>
        </h1>
        
        <div style="display: flex; align-items: center; gap: 20px; color: #666; font-size: 14px; margin-top: 20px;">
            <span><i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($news['date_news'])) ?></span>
            <span><i class="far fa-eye"></i> <?= number_format($news['view_news'] ?: 0) ?> просмотров</span>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumbs
ob_start();
?>
<div style="padding: 15px 20px; background: #f8f9fa; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/breadcrumbs.php';
        $breadcrumbData = [
            'title' => $news['title_news'],
            'category_id' => $news['category_id'],
            'category_name' => $news['category_name']
        ];
        render_breadcrumbs(get_breadcrumbs('news-single', $breadcrumbData));
        ?>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Featured image (if exists)
ob_start();
if ($news['image_news'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $news['image_news'])):
?>
<div style="padding: 20px; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <img src="<?= htmlspecialchars($news['image_news']) ?>" 
             alt="<?= htmlspecialchars($news['title_news']) ?>"
             style="width: 100%; height: auto; border-radius: 8px;">
    </div>
</div>
<?php
endif;
$greyContent3 = ob_get_clean();

// Section 4: Article content
ob_start();
?>
<div style="padding: 20px; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="article-content" style="font-size: 18px; line-height: 1.8; color: #333;">
            <?= $news['text_news'] ?>
        </div>
        
        <!-- Rating widget -->
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rating.php';
        include_rating('news', $news['id']);
        ?>
        
        <!-- Reading list widget -->
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/reading_list_widget.php';
        include_reading_list_widget('news', $news['id']);
        ?>
        
        <!-- Share buttons -->
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">Поделиться:</h3>
            <div style="display: flex; gap: 10px;">
                <a href="https://vk.com/share.php?url=<?= urlencode('https://11klassniki.ru/news/article-' . $newsId) ?>" 
                   target="_blank" 
                   style="background: #4267B2; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none;">
                    <i class="fab fa-vk"></i> ВКонтакте
                </a>
                <a href="https://t.me/share/url?url=<?= urlencode('https://11klassniki.ru/news/article-' . $newsId) ?>" 
                   target="_blank"
                   style="background: #0088cc; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none;">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
            </div>
        </div>
        
        <!-- Similar content recommendations -->
        <?php 
        if (isset($_SESSION['user_id'])) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/recommendations.php';
            include_similar_content_widget('news', $news['id']);
        }
        ?>
        
        <!-- Comments section -->
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/comments.php';
        include_comments('news', $news['id']);
        ?>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Related news
ob_start();
if (!empty($relatedNews)):
?>
<div style="background: #f8f9fa; padding: 40px 20px; margin: 0;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 30px; text-align: center;">Похожие новости</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($relatedNews as $related): ?>
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <?php if ($related['image_news'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $related['image_news'])): ?>
                <img src="<?= htmlspecialchars($related['image_news']) ?>" 
                     alt="<?= htmlspecialchars($related['title_news']) ?>"
                     style="width: 100%; height: 150px; object-fit: cover;">
                <?php else: ?>
                <div style="width: 100%; height: 150px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                    <i class="far fa-newspaper" style="font-size: 48px; color: #adb5bd;"></i>
                </div>
                <?php endif; ?>
                
                <div style="padding: 20px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 10px; line-height: 1.4;">
                        <a href="/news/<?= htmlspecialchars($related['url_slug']) ?>" 
                           style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($related['title_news']) ?>
                        </a>
                    </h3>
                    <div style="color: #666; font-size: 14px;">
                        <?= date('d.m.Y', strtotime($related['date_news'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
endif;
$greyContent5 = ob_get_clean();

// Section 6: Navigation
ob_start();
?>
<div style="padding: 20px; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <a href="/news" 
           style="display: inline-block; background: #007bff; color: white; padding: 12px 30px; border-radius: 4px; text-decoration: none; font-weight: 500;">
            ← Вернуться к новостям
        </a>
    </div>
</div>
<?php
$greyContent6 = ob_get_clean();

// Include template
$blueContent = ''; // No comments for now
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
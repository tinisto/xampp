<?php
// Modern post (article) single page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Get post ID or slug from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$postSlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$postId && !$postSlug) {
    header('Location: /posts');
    exit;
}

// Fetch post article
if ($postId) {
    $post = db_fetch_one("
        SELECT p.*, c.category_name, c.url_slug as category_slug 
        FROM posts p
        LEFT JOIN categories c ON p.category = c.id_category
        WHERE p.id = ?
    ", [$postId]);
} else {
    $post = db_fetch_one("
        SELECT p.*, c.category_name, c.url_slug as category_slug 
        FROM posts p
        LEFT JOIN categories c ON p.category = c.id_category
        WHERE p.url_slug = ?
    ", [$postSlug]);
}

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    include $_SERVER['DOCUMENT_ROOT'] . '/404_modern.php';
    exit;
}

// Update views
db_query("UPDATE posts SET view_post = view_post + 1 WHERE id = ?", [$post['id']]);

// Fetch related posts
$relatedPosts = db_fetch_all("
    SELECT id, title_post, url_slug, date_post, view_post
    FROM posts 
    WHERE category = ? AND id != ?
    ORDER BY date_post DESC
    LIMIT 4
", [$post['category'], $post['id']]);

// Prepare content for template
$pageTitle = $post['title_post'];

// Section 1: Title and metadata
ob_start();
?>
<div style="padding: 40px 20px; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div style="max-width: 800px; margin: 0 auto;">
        <?php if ($post['category_name']): ?>
        <a href="/posts?category=<?= htmlspecialchars($post['category_slug']) ?>" 
           style="color: white; text-decoration: none; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">
            <?= htmlspecialchars($post['category_name']) ?>
        </a>
        <?php endif; ?>
        
        <h1 style="font-size: 36px; font-weight: 700; margin: 15px 0; line-height: 1.3;">
            <?= htmlspecialchars($post['title_post']) ?>
        </h1>
        
        <div style="display: flex; align-items: center; gap: 20px; font-size: 14px; margin-top: 20px; opacity: 0.9;">
            <span><i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($post['date_post'])) ?></span>
            <span><i class="far fa-eye"></i> <?= number_format($post['view_post'] ?: 0) ?> просмотров</span>
            <span><i class="far fa-clock"></i> <?= ceil(str_word_count($post['text_post']) / 200) ?> мин чтения</span>
        </div>
    </div>
</div>
<?php
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumbs
ob_start();
?>
<div style="padding: 15px 20px; background: var(--bg-secondary); margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/breadcrumbs.php';
        $breadcrumbData = [
            'title' => $post['title_post'],
            'category_id' => $post['category'],
            'category_name' => $post['category_name']
        ];
        render_breadcrumbs(get_breadcrumbs('post-single', $breadcrumbData));
        ?>
    </div>
</div>
<?php
$greyContent2 = ob_get_clean();

// Section 3: Table of contents (if article is long)
ob_start();
$wordCount = str_word_count($post['text_post']);
if ($wordCount > 500):
?>
<div style="padding: 30px 20px; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background: var(--bg-secondary); border-radius: 12px; padding: 25px; border: 1px solid var(--border-color);">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: var(--link-color);">
                <i class="fas fa-list"></i> Содержание
            </h3>
            <div style="color: var(--text-primary);">
                <p>В этой статье вы узнаете:</p>
                <ul style="margin: 10px 0 0 20px;">
                    <li>Основные понятия и определения</li>
                    <li>Пошаговое руководство</li>
                    <li>Полезные советы и рекомендации</li>
                    <li>Часто задаваемые вопросы</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
endif;
$greyContent3 = ob_get_clean();

// Section 4: Article content
ob_start();
?>
<div style="padding: 40px 20px; margin: 0; background: var(--bg-primary);">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="article-content" style="font-size: 18px; line-height: 1.8; color: var(--text-primary);">
            <style>
                .article-content p { color: var(--text-primary) !important; margin-bottom: 1.5rem; }
                .article-content h1, .article-content h2, .article-content h3, .article-content h4, .article-content h5, .article-content h6 { 
                    color: var(--text-primary) !important; margin-top: 2rem; margin-bottom: 1rem; 
                }
                .article-content a { color: var(--link-color) !important; }
                .article-content a:hover { color: var(--link-hover) !important; }
                .article-content blockquote { 
                    border-left: 4px solid var(--link-color); 
                    background: var(--bg-secondary); 
                    padding: 1rem 1.5rem; 
                    margin: 1.5rem 0; 
                    color: var(--text-primary) !important; 
                }
                .article-content ul, .article-content ol { color: var(--text-primary) !important; }
                .article-content li { color: var(--text-primary) !important; margin-bottom: 0.5rem; }
                .article-content strong, .article-content b { color: var(--text-primary) !important; }
                .article-content em, .article-content i { color: var(--text-secondary) !important; }
            </style>
            <?= $post['text_post'] ?>
        </div>
        
        <!-- Call to action -->
        <div style="background: var(--bg-secondary); border-left: 4px solid var(--link-color); padding: 20px; margin: 40px 0; border-radius: 4px;">
            <h4 style="margin: 0 0 10px 0; color: var(--link-color);">💡 Полезный совет</h4>
            <p style="margin: 0; color: var(--text-secondary);">
                Сохраните эту статью в закладки, чтобы вернуться к ней позже. 
                Также рекомендуем ознакомиться с другими нашими материалами по теме образования.
            </p>
        </div>
        
        <!-- Rating widget -->
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rating.php';
        include_rating('post', $postId);
        ?>
        
        <!-- Reading list widget -->
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/reading_list_widget.php';
        include_reading_list_widget('post', $postId);
        ?>
        
        <!-- Share buttons -->
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-color);">
            <h3 style="font-size: 18px; margin-bottom: 15px; color: var(--text-primary);">Поделиться статьей:</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="https://vk.com/share.php?url=<?= urlencode('https://11klassniki.ru/post/' . $post['url_slug']) ?>&title=<?= urlencode($post['title_post']) ?>" 
                   target="_blank" 
                   style="background: #4267B2; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-vk"></i> ВКонтакте
                </a>
                <a href="https://t.me/share/url?url=<?= urlencode('https://11klassniki.ru/post/' . $post['url_slug']) ?>&text=<?= urlencode($post['title_post']) ?>" 
                   target="_blank"
                   style="background: #0088cc; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
                <a href="https://wa.me/?text=<?= urlencode($post['title_post'] . ' https://11klassniki.ru/post/' . $post['url_slug']) ?>" 
                   target="_blank"
                   style="background: #25D366; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
        
        <!-- Author info -->
        <div style="background: var(--bg-secondary); border-radius: 12px; padding: 25px; margin: 40px 0; display: flex; align-items: center; gap: 20px;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user" style="color: white; font-size: 24px;"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 18px; color: var(--text-primary);">Редакция 11klassniki.ru</h4>
                <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">
                    Мы создаем полезный контент для школьников, абитуриентов и студентов
                </p>
            </div>
        </div>
    </div>
</div>
<?php
$greyContent4 = ob_get_clean();

// Section 5: Related posts
ob_start();
if (!empty($relatedPosts)):
?>
<div style="background: var(--bg-secondary); padding: 60px 20px; margin: 0;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 40px; text-align: center;">Похожие статьи</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($relatedPosts as $related): ?>
            <article style="background: var(--bg-primary); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px var(--shadow); transition: transform 0.3s;">
                <div style="height: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                
                <div style="padding: 25px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px; line-height: 1.4;">
                        <a href="/post/<?= htmlspecialchars($related['url_slug']) ?>" 
                           style="color: var(--text-primary); text-decoration: none;">
                            <?= htmlspecialchars($related['title_post']) ?>
                        </a>
                    </h3>
                    
                    <div style="color: var(--text-secondary); font-size: 14px; display: flex; align-items: center; gap: 15px;">
                        <span><i class="far fa-calendar"></i> <?= date('d.m.Y', strtotime($related['date_post'])) ?></span>
                        <span><i class="far fa-eye"></i> <?= number_format($related['view_post'] ?: 0) ?></span>
                    </div>
                    
                    <a href="/post/<?= htmlspecialchars($related['url_slug']) ?>" 
                       style="display: inline-flex; align-items: center; gap: 5px; margin-top: 15px; color: #667eea; text-decoration: none; font-weight: 500;">
                        Читать далее <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
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
<div style="padding: 40px 20px; margin: 0; background: var(--bg-primary);">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <a href="/posts" 
           style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: transform 0.3s;"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            <i class="fas fa-arrow-left"></i> Все статьи
        </a>
    </div>
</div>
<?php
$greyContent6 = ob_get_clean();

// Section 7: Similar content and Comments
ob_start();
?>
<div style="padding: 40px 20px; margin: 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Similar content recommendations -->
        <?php 
        if (isset($_SESSION['user_id'])) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/recommendations.php';
            include_similar_content_widget('post', $postId);
        }
        ?>
        
        <!-- Comments section -->
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/comments.php';
        include_comments('post', $postId);
        ?>
    </div>
</div>
<?php
$blueContent = ob_get_clean();

// Include template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template_local.php';
?>
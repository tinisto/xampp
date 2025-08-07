<?php
// Single post page - migrated to use real_template.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get URL parameter - support both url_post and url_slug parameters
$url_param = '';
if (isset($_GET['url_post'])) {
    $url_param = mysqli_real_escape_string($connection, $_GET['url_post']);
} elseif (isset($_GET['url_slug'])) {
    $url_param = mysqli_real_escape_string($connection, $_GET['url_slug']);
}

if (empty($url_param)) {
    header("Location: /404");
    exit();
}

// Fetch post data - try both url_slug and url_post fields
$query = "SELECT p.*, c.title_category, c.url_category, 
                 u.username as author_name,
                 (SELECT COUNT(*) FROM comments WHERE entity_type = 'post' AND entity_id = p.id) as comment_count
          FROM posts p
          LEFT JOIN categories c ON p.category = c.id_category
          LEFT JOIN users u ON p.author_id = u.id
          WHERE p.url_slug = ? OR p.url_post = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "ss", $url_param, $url_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $postData = $row;
} else {
    header("Location: /404");
    exit();
}

mysqli_stmt_close($stmt);

// Update views if not visited in this session
if (!isset($_SESSION['visited_post_' . $postData['id']])) {
    $updateViews = "UPDATE posts SET view_post = view_post + 1 WHERE id = ?";
    $stmt = $connection->prepare($updateViews);
    $stmt->bind_param("i", $postData['id']);
    $stmt->execute();
    $_SESSION['visited_post_' . $postData['id']] = true;
}

// Section 1: Title
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
renderRealTitle($postData['title_post'], [
    'fontSize' => '32px',
    'margin' => '30px 0'
]);
$greyContent1 = ob_get_clean();

// Section 2: Breadcrumb navigation
ob_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/breadcrumb.php';
$breadcrumbItems = [
    ['text' => 'Главная', 'url' => '/'],
    ['text' => 'Статьи', 'url' => '/posts']
];
if ($postData['title_category']) {
    $breadcrumbItems[] = ['text' => $postData['title_category'], 'url' => '/category/' . $postData['url_category']];
}
$breadcrumbItems[] = ['text' => $postData['title_post']];
renderBreadcrumb($breadcrumbItems);
$greyContent2 = ob_get_clean();

// Section 3: Metadata
ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
    <div style="display: flex; gap: 30px; align-items: center;">
        <?php if ($postData['author_post'] || $postData['author_name']): ?>
            <div>
                <i class="fas fa-user" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= htmlspecialchars($postData['author_name'] ?? $postData['author_post']) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <i class="fas fa-calendar" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= date('d.m.Y', strtotime($postData['date_post'])) ?></span>
        </div>
        <div>
            <i class="fas fa-eye" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= number_format($postData['view_post']) ?> просмотров</span>
        </div>
        <div>
            <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
            <span style="color: #666;"><?= $postData['comment_count'] ?> комментариев</span>
        </div>
    </div>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div style="display: flex; gap: 10px;">
            <a href="/edit/post/<?= $postData['id'] ?>" style="padding: 6px 12px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-edit"></i> Редактировать
            </a>
            <a href="/delete-post.php?id=<?= $postData['id'] ?>" onclick="return confirm('Вы уверены, что хотите удалить этот пост?')" style="padding: 6px 12px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-trash"></i> Удалить
            </a>
        </div>
    <?php endif; ?>
</div>
<?php
$greyContent3 = ob_get_clean();

// Section 4: Empty for single post
$greyContent4 = '';

// Section 5: Main content
ob_start();
?>
<div style="padding: 30px 20px;">
    <?php if (!empty($postData['description_post'])): ?>
        <p style="color: #666; font-size: 1.1em; margin-bottom: 20px; line-height: 1.6;">
            <?= htmlspecialchars($postData['description_post']) ?>
        </p>
    <?php endif; ?>

    <?php 
    // Display main image
    $imageToShow = null;
    
    if (!empty($postData['image_post'])) {
        $imageToShow = $postData['image_post'];
    } elseif (!empty($postData['img1_post'])) {
        $oldImagePath = "/images/posts-images/{$postData['id']}_1.jpg";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImagePath)) {
            $imageToShow = $oldImagePath;
        }
    }
    
    if ($imageToShow): ?>
        <div style="margin-bottom: 30px; text-align: center;">
            <img src="<?= htmlspecialchars($imageToShow) ?>" 
                 alt="<?= htmlspecialchars($postData['title_post']) ?>" 
                 style="max-width: 100%; height: auto; border-radius: 8px;">
        </div>
    <?php endif; ?>

    <?php if (!empty($postData['bio_post'])): ?>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #3b82f6;">
            <div style="font-style: italic; color: #333;">
                <?php 
                $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6>';
                echo strip_tags($postData['bio_post'], $allowed_tags);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($postData['text_post'])): ?>
        <div style="color: #333; line-height: 1.8; font-size: 16px;">
            <?php 
            $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
            echo strip_tags($postData['text_post'], $allowed_tags);
            ?>
        </div>
    <?php endif; ?>

    <?php 
    // Additional images
    for ($i = 2; $i <= 3; $i++) {
        if (!empty($postData["img{$i}_post"])) {
            $imagePath = "/images/posts-images/{$postData['id']}_{$i}.jpg";
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)): ?>
                <div style="margin: 20px 0; text-align: center;">
                    <img src="<?= htmlspecialchars($imagePath) ?>" 
                         alt="Image <?= $i ?>" 
                         style="max-width: 100%; height: auto; border-radius: 8px;">
                </div>
            <?php endif;
        }
    }
    ?>
</div>
<?php
$greyContent5 = ob_get_clean();

// Section 6: Related posts
ob_start();
// Get related posts from same category
if ($postData['category']) {
    $relatedQuery = "SELECT id, title_post, url_slug, url_post, date_post, view_post
                     FROM posts 
                     WHERE category = ? AND id != ? 
                     ORDER BY date_post DESC 
                     LIMIT 4";
    $stmt = $connection->prepare($relatedQuery);
    $stmt->bind_param("ii", $postData['category'], $postData['id']);
    $stmt->execute();
    $relatedResult = $stmt->get_result();
    $relatedPosts = [];
    while ($row = $relatedResult->fetch_assoc()) {
        $relatedPosts[] = [
            'id_news' => $row['id'],
            'title_news' => $row['title_post'],
            'url_news' => $row['url_slug'] ?: $row['url_post'],
            'image_news' => file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id']}_1.jpg") 
                ? "/images/posts-images/{$row['id']}_1.jpg" 
                : '/images/default-news.jpg',
            'created_at' => $row['date_post'],
            'category_title' => $postData['title_category'],
            'category_url' => $postData['url_category']
        ];
    }

    if (count($relatedPosts) > 0) {
        echo '<div style="padding: 20px;">';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle('Похожие статьи', ['fontSize' => '24px', 'margin' => '0 0 20px 0']);
        
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
        renderCardsGrid($relatedPosts, 'post', [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => true
        ]);
        echo '</div>';
    }
}
$greyContent6 = ob_get_clean();

// Section 7: Comments (prepared but not implemented per user request)
ob_start();
?>
<div style="padding: 30px 20px; color: white;">
    <h3 style="margin: 0 0 20px 0;">Комментарии (<?= $postData['comment_count'] ?>)</h3>
    <!-- Comments will be added later per user request -->
</div>
<?php
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $postData['title_post'];
$metaD = $postData['meta_d_post'] ?? '';
$metaK = $postData['meta_k_post'] ?? '';

// Include the template
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>
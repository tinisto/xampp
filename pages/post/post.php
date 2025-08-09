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
    // Show debug info instead of redirect
    $greyContent1 = '<div style="padding: 30px;"><h1 style="color: #333;">DEBUG: No URL Parameter</h1></div>';
    $greyContent2 = '<div style="padding: 20px;"><p><strong>GET Parameters:</strong><br>' . htmlspecialchars(print_r($_GET, true)) . '</p></div>';
    $greyContent3 = '<div style="padding: 20px;"><p><strong>REQUEST_URI:</strong><br>' . htmlspecialchars($_SERVER['REQUEST_URI']) . '</p></div>';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 20px;"><p><strong>Script Name:</strong><br>' . htmlspecialchars($_SERVER['SCRIPT_NAME']) . '</p></div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Debug: No URL Parameter';
    $metaD = '';
    $metaK = '';
    return; // Don't continue processing
}

// Try simple query first - just the posts table
$simpleQuery = "SELECT * FROM posts WHERE url_slug = ?";
$stmt = mysqli_prepare($connection, $simpleQuery);
mysqli_stmt_bind_param($stmt, "s", $url_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Found the post! Set basic data
    $postData = $row;
    $postData['title_category'] = 'Unknown Category'; // Default
    $postData['url_category'] = '';
    $postData['comment_count'] = 0; // Default
    $postData['author_name'] = $postData['author_post'] ?? 'Unknown';
} else {
    // Show debug info
    $greyContent1 = '<div style="padding: 30px;"><h1 style="color: #333;">DEBUG: Post Not Found</h1></div>';
    $greyContent2 = '<div style="padding: 20px;"><p><strong>URL Parameter:</strong><br>' . htmlspecialchars($url_param) . '</p></div>';
    
    // Check what posts actually exist
    $firstPostsQuery = "SELECT url_slug, title_post FROM posts ORDER BY id LIMIT 5";
    $firstPostsResult = mysqli_query($connection, $firstPostsQuery);
    $firstPosts = [];
    while ($firstRow = mysqli_fetch_assoc($firstPostsResult)) {
        $firstPosts[] = $firstRow['url_slug'] . ' (' . $firstRow['title_post'] . ')';
    }
    
    $greyContent3 = '<div style="padding: 20px;"><p><strong>First 5 posts in posts table:</strong><br>' . htmlspecialchars(implode('<br>', $firstPosts)) . '</p></div>';
    $greyContent4 = '<div style="padding: 20px;"><p><strong>Total posts:</strong><br>' . mysqli_num_rows(mysqli_query($connection, "SELECT id FROM posts")) . ' posts</p></div>';
    
    // Check if MySQL error occurred
    if (mysqli_error($connection)) {
        $greyContent5 = '<div style="padding: 20px;"><p><strong>MySQL Error:</strong><br>' . htmlspecialchars(mysqli_error($connection)) . '</p></div>';
    } else {
        $greyContent5 = '<div style="padding: 20px;"><p><strong>No MySQL errors</strong></p></div>';
    }
    
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Debug: Post Not Found';
    $metaD = '';
    $metaK = '';
    return; // Don't continue processing
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
    $relatedQuery = "SELECT id, title_post, url_slug, date_post, view_post
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
            'url_news' => $row['url_slug'],
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

// Section 7: Beautiful Threaded Comments
ob_start();
// Include the new threaded comments component
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/threaded-comments.php';
renderThreadedComments('posts', $postData['id'], [
    'title' => 'Обсуждение',
    'loadLimit' => 10,
    'allowNewComments' => true,
    'allowReplies' => true,
    'maxDepth' => 5
]);
$blueContent = ob_get_clean();

// Set page title and metadata
$pageTitle = $postData['title_post'];
$metaD = $postData['meta_d_post'] ?? '';
$metaK = $postData['meta_k_post'] ?? '';

// Template is included by post-new.php router - don't include again
?>
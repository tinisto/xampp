<?php
// Single news page - improved with error handling
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$newsUrl = $_GET['url_news'] ?? '';

// Basic validation
if (empty($newsUrl)) {
    header("Location: /404");
    exit;
}

try {
    // Get news article - using actual database field names
    $query = "SELECT n.*, c.title_category, c.url_category, 
                     u.username as author_name
              FROM news n
              LEFT JOIN categories c ON n.category_news = c.id_category
              LEFT JOIN users u ON n.user_id = u.id
              WHERE n.url_slug = ? AND n.approved = 1";

    $stmt = $connection->prepare($query);
    if (!$stmt) {
        error_log("News single prepare failed: " . $connection->error);
        header("Location: /404");
        exit;
    }
    
    $stmt->bind_param("s", $newsUrl);
    if (!$stmt->execute()) {
        error_log("News single execute failed: " . $stmt->error);
        header("Location: /404");
        exit;
    }
    
    $result = $stmt->get_result();
    $news = $result->fetch_assoc();

    if (!$news) {
        header("Location: /404");
        exit;
    }

    // Get comment count separately to avoid issues
    $commentCount = 0;
    try {
        $commentQuery = "SELECT COUNT(*) as count FROM comments WHERE entity_type = 'news' AND entity_id = ?";
        $commentStmt = $connection->prepare($commentQuery);
        if ($commentStmt) {
            $commentStmt->bind_param("i", $news['id']);
            $commentStmt->execute();
            $commentResult = $commentStmt->get_result();
            $commentRow = $commentResult->fetch_assoc();
            $commentCount = $commentRow['count'] ?? 0;
        }
    } catch (Exception $e) {
        error_log("Comment count failed: " . $e->getMessage());
        // Continue without comments
    }

    // Update views
    try {
        $updateViews = "UPDATE news SET view_news = COALESCE(view_news, 0) + 1 WHERE id = ?";
        $updateStmt = $connection->prepare($updateViews);
        if ($updateStmt) {
            $updateStmt->bind_param("i", $news['id']);
            $updateStmt->execute();
        }
    } catch (Exception $e) {
        error_log("Update views failed: " . $e->getMessage());
        // Continue without updating views
    }

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
            <?php if (!empty($news['title_category'])): ?>
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
            <?php if (!empty($news['author_name'])): ?>
                <div>
                    <i class="fas fa-user" style="color: #666; margin-right: 8px;"></i>
                    <span style="color: #666;"><?= htmlspecialchars($news['author_name']) ?></span>
                </div>
            <?php endif; ?>
            <div>
                <i class="fas fa-calendar" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= date('d.m.Y', strtotime($news['date_news'])) ?></span>
            </div>
            <div>
                <i class="fas fa-eye" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= number_format($news['view_news'] ?? 0) ?> просмотров</span>
            </div>
            <div>
                <i class="fas fa-comments" style="color: #666; margin-right: 8px;"></i>
                <span style="color: #666;"><?= $commentCount ?> комментариев</span>
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
        <?php if (!empty($news['image_news']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $news['image_news'])): ?>
            <div style="margin-bottom: 30px; text-align: center;">
                <img src="<?= htmlspecialchars($news['image_news']) ?>" 
                     alt="<?= htmlspecialchars($news['title_news']) ?>" 
                     style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
        <?php endif; ?>
        
        <div style="font-size: 16px; line-height: 1.8; color: #333;">
            <?= $news['text_news'] ?>
        </div>
    </div>
    <?php
    $greyContent5 = ob_get_clean();

    // Section 6: Related news (simplified to avoid errors)
    ob_start();
    try {
        $relatedQuery = "SELECT id, title_news, url_slug, image_news, date_news 
                         FROM news 
                         WHERE category_news = ? AND id != ? AND approved = 1
                         ORDER BY date_news DESC 
                         LIMIT 4";
        $relatedStmt = $connection->prepare($relatedQuery);
        $relatedNews = [];
        
        if ($relatedStmt) {
            $relatedStmt->bind_param("si", $news['category_news'], $news['id']);
            $relatedStmt->execute();
            $relatedResult = $relatedStmt->get_result();
            
            while ($row = $relatedResult->fetch_assoc()) {
                $relatedNews[] = [
                    'id_news' => $row['id'],
                    'title_news' => $row['title_news'],
                    'url_news' => $row['url_slug'],
                    'image_news' => $row['image_news'],
                    'created_at' => $row['date_news'],
                    'category_title' => $news['title_category'],
                    'category_url' => $news['url_category']
                ];
            }
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
    } catch (Exception $e) {
        error_log("Related news failed: " . $e->getMessage());
        // Continue without related news
    }
    $greyContent6 = ob_get_clean();

    // Section 7: Comments (simplified to avoid errors)
    ob_start();
    ?>
    <div style="padding: 30px 20px; color: white;">
        <h3 style="margin: 0 0 20px 0;">Комментарии (<?= $commentCount ?>)</h3>
        <?php
        $commentsFile = $_SERVER['DOCUMENT_ROOT'] . '/comments/display_comments.php';
        if (file_exists($commentsFile)) {
            try {
                include $commentsFile;
                if (function_exists('displayComments')) {
                    displayComments('news', $news['id']);
                } else {
                    echo '<p>Комментарии временно недоступны</p>';
                }
            } catch (Exception $e) {
                error_log("Comments display failed: " . $e->getMessage());
                echo '<p>Комментарии временно недоступны</p>';
            }
        } else {
            echo '<p>Комментарии временно недоступны</p>';
        }
        ?>
    </div>
    <?php
    $blueContent = ob_get_clean();

    // Set page title
    $pageTitle = $news['title_news'];

    // Include the template
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';

} catch (Exception $e) {
    error_log("News single page error: " . $e->getMessage());
    header("Location: /404");
    exit;
}
?>
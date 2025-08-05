<?php
/**
 * Simple Comments Loader for Testing
 */

// Get entity information from the URL
$currentUrl = $_SERVER['REQUEST_URI'];
preg_match('/\/post\/([\w-]+)/', $currentUrl, $postMatches);

if (isset($postMatches[1])) {
    $entity_type = 'post';
    $url_slug = $postMatches[1];
    
    // Get post ID directly from database
    $postQuery = "SELECT id FROM posts WHERE url_slug = ?";
    $postStmt = $connection->prepare($postQuery);
    if (!$postStmt) {
        echo "<div class='comments-empty'>Database error: " . $connection->error . "</div>";
        return;
    }
    
    $postStmt->bind_param("s", $url_slug);
    $postStmt->execute();
    $postResult = $postStmt->get_result();
    
    if ($postResult->num_rows > 0) {
        $post = $postResult->fetch_assoc();
        $entity_id = $post['id'];
    } else {
        echo "<div class='comments-empty'>Post not found: $url_slug</div>";
        return;
    }
    $postStmt->close();
} else {
    echo "<div class='comments-empty'>Invalid URL format</div>";
    return;
}

if (!$entity_id) {
    echo "<div class='comments-empty'>No valid post ID found</div>";
    return;
}

// Simple date formatting functions with timezone correction
function simpleTimeAgo($datetime) {
    // Convert to Moscow timezone
    $moscowTime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
    $commentTime = new DateTime($datetime, new DateTimeZone('UTC'));
    $commentTime->setTimezone(new DateTimeZone('Europe/Moscow'));
    
    $time = $moscowTime->getTimestamp() - $commentTime->getTimestamp();
    
    if ($time < 60) return 'только что';
    if ($time < 3600) return floor($time/60) . ' мин назад';
    if ($time < 86400) return floor($time/3600) . ' ч назад';
    if ($time < 2592000) return floor($time/86400) . ' дн назад';
    return $commentTime->format('d.m.Y');
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$commentsPerPage = 10;
$offset = ($page - 1) * $commentsPerPage;

// Get comments with user info
$query = "SELECT 
    comments.id,
    comments.id_entity,
    comments.user_id,
    comments.comment_text,
    comments.date,
    comments.parent_id,
    comments.author_of_comment,
    users.first_name,
    users.last_name,
    users.email
FROM comments 
LEFT JOIN users ON comments.user_id = users.id 
WHERE comments.id_entity = ? 
  AND comments.entity_type = ? 
  AND comments.parent_id = 0 
ORDER BY comments.date DESC 
LIMIT ? OFFSET ?";

$stmt = $connection->prepare($query);
if (!$stmt) {
    echo "<div class='comments-empty'>Database error: " . $connection->error . "</div>";
    return;
}

$stmt->bind_param("isii", $entity_id, $entity_type, $commentsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$commentCount = 0;
while ($comment = $result->fetch_assoc()) {
    $commentCount++;
    $commentId = $comment['id'];
    $isGuest = ($comment['user_id'] == 0);
    
    // Get author name
    if ($isGuest) {
        $authorName = htmlspecialchars($comment['author_of_comment'] ?? 'Гость');
    } else {
        $firstName = $comment['first_name'] ?? '';
        $lastName = $comment['last_name'] ?? '';
        $authorName = trim($firstName . ' ' . $lastName);
        if (empty($authorName)) {
            $authorName = htmlspecialchars($comment['email'] ?? 'Пользователь');
        }
    }
    
    // Format date
    $timeAgo = simpleTimeAgo($comment['date']);
    $fullDate = date('d.m.Y H:i', strtotime($comment['date']));
    
    // Clean comment text with XSS protection
    $commentText = $comment['comment_text'];
    $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
    $safeText = strip_tags($commentText, $allowed_tags);
    ?>
    
    <div class="comment-item" id="comment-<?= $commentId ?>" style="border-bottom: 1px solid #e5e7eb; padding: 1.5rem 0;">
        <div style="display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                <?= strtoupper(substr($authorName, 0, 1)) ?>
            </div>
            
            <div style="flex: 1;">
                <div style="font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">
                    <?= $authorName ?>
                    <?php if ($isGuest): ?>
                        <span style="color: #6b7280; font-weight: normal; font-size: 0.875rem;">(Гость)</span>
                    <?php endif; ?>
                </div>
                
                <div style="color: #374151; line-height: 1.6; margin-bottom: 0.75rem;">
                    <?= nl2br($safeText) ?>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.875rem; color: #6b7280;">
                    <span title="<?= htmlspecialchars($fullDate) ?>">
                        <?= htmlspecialchars($timeAgo) ?>
                    </span>
                    
                    <?php if (isset($_SESSION['email'])): ?>
                        <button onclick="alert('Reply feature coming soon!')" 
                                style="background: none; border: none; color: #3b82f6; cursor: pointer; font-size: 0.875rem;">
                            Ответить
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
}

if ($commentCount === 0) {
    echo "<div class='comments-empty' style='padding: 2rem; text-align: center; color: #6b7280; font-style: italic;'>Комментариев пока нет</div>";
} else {
    echo "<div style='padding: 1rem; font-size: 0.875rem; color: #6b7280; text-align: center;'>Показано комментариев: $commentCount</div>";
}
?>
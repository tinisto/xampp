<?php
/**
 * Modern Comments Loader with Professional Styling
 */

// Get entity information from the URL
$currentUrl = $_SERVER['REQUEST_URI'];
preg_match('/\/school\/(\d+)/', $currentUrl, $schoolMatches);
preg_match('/\/vpo\/([\w-]+)/', $currentUrl, $universityMatches);
preg_match('/\/spo\/([\w-]+)/', $currentUrl, $collegeMatches);
preg_match('/\/post\/([\w-]+)/', $currentUrl, $postMatches);

// Determine entity type and ID
$entity_type = 'unknown';
$entity_id = null;

if (isset($schoolMatches[1])) {
    $entity_type = 'school';
    $entity_id = $schoolMatches[1];
} elseif (isset($universityMatches[1])) {
    $entity_type = 'vpo';
    $entity_id = $universityMatches[1];
} elseif (isset($collegeMatches[1])) {
    $entity_type = 'spo';
    $entity_id = $collegeMatches[1];
} elseif (isset($postMatches[1])) {
    $entity_type = 'post';
    // For posts, get ID from database
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php';
    $entityData = getEntityIdFromPostURL($connection);
    $entity_id = $entityData['id_entity'];
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
    COALESCE(users.avatar_url, 'default_avatar.jpg') AS avatar,
    users.first_name,
    users.last_name,
    users.email
FROM comments 
LEFT JOIN users ON comments.user_id = users.id 
WHERE comments.entity_id = ? 
  AND comments.entity_type = ? 
  AND comments.parent_id = 0 
ORDER BY comments.date DESC 
LIMIT ? OFFSET ?";

$stmt = $connection->prepare($query);
$stmt->bind_param("isii", $entity_id, $entity_type, $commentsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

while ($comment = $result->fetch_assoc()) {
    $commentId = $comment['id'];
    $isGuest = ($comment['user_id'] == 0);
    
    // Get author name
    if ($isGuest) {
        $authorName = htmlspecialchars($comment['author_of_comment'] ?? 'Гость');
        $avatarPath = '/images/avatars/default_avatar.jpg';
    } else {
        $firstName = $comment['first_name'] ?? '';
        $lastName = $comment['last_name'] ?? '';
        $authorName = trim($firstName . ' ' . $lastName);
        if (empty($authorName)) {
            $authorName = htmlspecialchars($comment['email'] ?? 'Пользователь');
        }
        $avatarPath = !empty($comment['avatar']) && $comment['avatar'] !== 'default_avatar.jpg' 
                     ? '/images/avatars/' . $comment['avatar'] 
                     : '/images/avatars/default_avatar.jpg';
    }
    
    // Format date
    $timestamp = strtotime($comment['date']);
    $timeAgo = getElapsedTime($comment['date']); // Will use user's timezone automatically
    $fullDate = getFormattedDate($timestamp);
    
    // Clean comment text with XSS protection
    $commentText = $comment['comment_text'];
    $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
    $safeText = strip_tags($commentText, $allowed_tags);
    ?>
    
    <div class="comment-item" id="comment-<?= $commentId ?>">
        <div style="display: flex; align-items: flex-start;">
            <img src="<?= htmlspecialchars($avatarPath) ?>" 
                 alt="Avatar" 
                 class="comment-avatar"
                 onerror="this.src='/images/avatars/default_avatar.jpg'">
            
            <div class="comment-content">
                <div class="comment-author">
                    <?= $authorName ?>
                    <?php if ($isGuest): ?>
                        <span style="color: var(--comments-text-light); font-weight: normal; font-size: 0.75rem;">(Гость)</span>
                    <?php endif; ?>
                </div>
                
                <div class="comment-text">
                    <?= nl2br($safeText) ?>
                </div>
                
                <div class="comment-meta">
                    <span class="comment-time" 
                          title="<?= htmlspecialchars($fullDate) ?>"
                          data-toggle="tooltip">
                        <?= htmlspecialchars($timeAgo) ?>
                    </span>
                    
                    <?php if (isset($_SESSION['email']) && !getUserSuspensionStatus($_SESSION['email'])): ?>
                        <button class="comment-reply-btn" 
                                onclick="toggleReplyForm(<?= $commentId ?>)">
                            Ответить
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Reply Form (initially hidden) -->
                <?php if (isset($_SESSION['email']) && !getUserSuspensionStatus($_SESSION['email'])): ?>
                    <div id="reply-form-<?= $commentId ?>" style="display: none; margin-top: 1rem; padding: 1rem; background: var(--comments-light); border-radius: 8px;">
                        <form method="post" action="/comments/process_comments.php">
                            <input type="hidden" name="entity_type" value="<?= htmlspecialchars($entity_type) ?>">
                            <input type="hidden" name="id_entity" value="<?= htmlspecialchars($entity_id) ?>">
                            <input type="hidden" name="parent_id" value="<?= $commentId ?>">
                            
                            <textarea name="comment" 
                                    class="comments-textarea" 
                                    style="min-height: 80px; margin-bottom: 0.5rem;"
                                    placeholder="Ваш ответ..." 
                                    maxlength="2000" 
                                    required></textarea>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="submit" class="comments-submit-btn" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                                    Ответить
                                </button>
                                <button type="button" 
                                        onclick="toggleReplyForm(<?= $commentId ?>)" 
                                        style="background: #6b7280; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">
                                    Отмена
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                
                <!-- Child Comments (Replies) -->
                <?php
                $childComments = getChildComments($commentId, $connection);
                if (!empty($childComments)):
                ?>
                    <div style="margin-top: 1rem; padding-left: 1rem; border-left: 3px solid var(--comments-border);">
                        <?php foreach ($childComments as $reply): 
                            $replyIsGuest = ($reply['user_id'] == 0);
                            
                            if ($replyIsGuest) {
                                $replyAuthorName = htmlspecialchars($reply['author_of_comment'] ?? 'Гость');
                                $replyAvatarPath = '/images/avatars/default_avatar.jpg';
                            } else {
                                $replyFirstName = $reply['first_name'] ?? '';
                                $replyLastName = $reply['last_name'] ?? '';
                                $replyAuthorName = trim($replyFirstName . ' ' . $replyLastName);
                                if (empty($replyAuthorName)) {
                                    $replyAuthorName = htmlspecialchars($reply['email'] ?? 'Пользователь');
                                }
                                $replyAvatarPath = !empty($reply['avatar']) && $reply['avatar'] !== 'default_avatar.jpg' 
                                                 ? '/images/avatars/' . $reply['avatar'] 
                                                 : '/images/avatars/default_avatar.jpg';
                            }
                            
                            $replyTimestamp = strtotime($reply['date']);
                            $replyTimeAgo = getElapsedTime($reply['date']); // Will use user's timezone automatically
                            $replyFullDate = getFormattedDate($replyTimestamp);
                            
                            // Clean reply text
                            $replySafeText = strip_tags($reply['comment_text'], $allowed_tags);
                        ?>
                            <div style="display: flex; align-items: flex-start; margin-bottom: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                                <img src="<?= htmlspecialchars($replyAvatarPath) ?>" 
                                     alt="Avatar" 
                                     style="width: 32px; height: 32px; border-radius: 50%; margin-right: 0.75rem; border: 1px solid var(--comments-border);"
                                     onerror="this.src='/images/avatars/default_avatar.jpg'">
                                
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: var(--comments-text); margin-bottom: 0.25rem; font-size: 0.875rem;">
                                        <?= $replyAuthorName ?>
                                        <?php if ($replyIsGuest): ?>
                                            <span style="color: var(--comments-text-light); font-weight: normal; font-size: 0.75rem;">(Гость)</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="color: var(--comments-text); line-height: 1.5; font-size: 0.875rem; margin-bottom: 0.5rem;">
                                        <?= nl2br($replySafeText) ?>
                                    </div>
                                    
                                    <div style="font-size: 0.75rem; color: var(--comments-text-light);">
                                        <span title="<?= htmlspecialchars($replyFullDate) ?>" data-toggle="tooltip">
                                            <?= htmlspecialchars($replyTimeAgo) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php
}

// Get total comments count for pagination
$countQuery = "SELECT COUNT(*) as total FROM comments WHERE entity_id = ? AND entity_type = ? AND parent_id = 0";
$countStmt = $connection->prepare($countQuery);
$countStmt->bind_param("is", $entity_id, $entity_type);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalComments = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalComments / $commentsPerPage);

// Pagination
if ($totalPages > 1): ?>
    <div style="padding: 2rem; text-align: center; border-top: 1px solid var(--comments-border); background: var(--comments-light);">
        <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" 
                   style="padding: 0.5rem 1rem; background: var(--comments-primary); color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                    ← Назад
                </a>
            <?php endif; ?>
            
            <span style="padding: 0.5rem 1rem; color: var(--comments-text-light); font-size: 0.875rem;">
                Страница <?= $page ?> из <?= $totalPages ?>
            </span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" 
                   style="padding: 0.5rem 1rem; background: var(--comments-primary); color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                    Далее →
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script>
function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        form.querySelector('textarea').focus();
    } else {
        form.style.display = 'none';
    }
}

// Initialize tooltips if Bootstrap is available
if (typeof bootstrap !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
}
</script>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$id_entity = isset($_GET['id_entity']) ? $_GET['id_entity'] : $id_entity;
$entity_type = isset($_GET['entity_type']) ? $_GET['entity_type'] : $entity_type;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

include_once 'comment_functions.php';

// Check if the user is logged in
$isUserLoggedIn = isset($_SESSION['email']);

// Assuming 5 comments per page
$commentsPerPage = 5;
$offset = ($page - 1) * $commentsPerPage;

// Query to count total comments
$queryCount = "SELECT COUNT(*) AS total FROM comments WHERE id_entity=? AND entity_type=? AND parent_id=0";
$stmtCount = mysqli_prepare($connection, $queryCount);
mysqli_stmt_bind_param($stmtCount, "is", $id_entity, $entity_type);
mysqli_stmt_execute($stmtCount);
$resultCount = mysqli_stmt_get_result($stmtCount);
$totalComments = mysqli_fetch_assoc($resultCount)['total'];

// Query to fetch comments
$queryComments = "SELECT
    comments.id,
    comments.id_entity,
    comments.user_id,
    COALESCE(users.avatar, 'default_avatar.jpg') AS avatar,
    comments.comment_text,
    comments.date,
    COALESCE(users.timezone, 'UTC') AS user_timezone,
    comments.author_of_comment
FROM
    comments
LEFT JOIN users ON comments.user_id = users.id
WHERE
    comments.id_entity=? AND comments.entity_type=? AND parent_id=0
ORDER BY
    comments.date DESC LIMIT ?, ?;
";

$stmtComments = mysqli_prepare($connection, $queryComments);

if (!$stmtComments) {
    header("Location: /error");
    exit();
}

// Perform type casting
$id_entity = (int)$id_entity;
$entity_type = (string)$entity_type;
$offset = (int)$offset;
$commentsPerPage = (int)$commentsPerPage;

// Bind parameters based on entity type and ID
mysqli_stmt_bind_param($stmtComments, "isii", $id_entity, $entity_type, $offset, $commentsPerPage);

mysqli_stmt_execute($stmtComments);

if ($stmtComments->errno) {
    header("Location: /error");
    exit();
}

$resultComments = mysqli_stmt_get_result($stmtComments);

if (!$resultComments) {
    header("Location: /error");
    exit();
}

// Output comments
while ($comment = mysqli_fetch_assoc($resultComments)) {
    if ($comment !== null) {
        // Dynamically generate a unique container ID for each comment
        $containerID = 'commentContainer_page' . $page . '_comment' . $comment['id'];
?>
        <div class="comment-item" id="<?php echo $containerID; ?>">
            <div class="comment-header">
                <div class="comment-avatar">
                    <?php 
                    $avatarPath = getAvatar($comment['avatar']);
                    if ($avatarPath && $comment['avatar'] !== 'default_avatar.jpg'): 
                    ?>
                        <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="Avatar">
                    <?php else: 
                        // Show first letter of name
                        $firstLetter = 'U';
                        if ($comment['user_id'] == 0) {
                            $firstLetter = strtoupper(substr($comment['author_of_comment'], 0, 1));
                        } else {
                            $userNames = getUserNames($comment['user_id'], $connection);
                            $firstLetter = strtoupper(substr($userNames['firstname'], 0, 1));
                        }
                        echo $firstLetter;
                    ?>
                    <?php endif; ?>
                </div>
                
                <div class="comment-meta">
                    <div class="comment-author">
                        <?php
                        // Check if the user_id is 0
                        if ($comment['user_id'] == 0) {
                            echo htmlspecialchars($comment['author_of_comment']);
                        } else {
                            // Fetch user's first and last names based on user_id
                            $userNames = getUserNames($comment['user_id'], $connection);
                            echo htmlspecialchars($userNames['firstname'] . ' ' . $userNames['lastname']);
                        }
                        ?>
                    </div>
                    <div class="comment-date">
                        <?php
                        $timestamp = isset($comment['date']) ? strtotime($comment['date']) : null;
                        if ($timestamp) {
                            echo date('d.m.Y H:i', $timestamp);
                        }
                        ?>
                    </div>
                </div>
                
                <div style="margin-left: auto;">
                    <i class="toggle-icon fas fa-eye-slash toggleCard" 
                       style="color: var(--text-muted); cursor: pointer; font-size: 0.875rem;"></i>
                </div>
            </div>
            
            <div class="comment-content">
                <?php
                $commentText = $comment['comment_text'];
                if ($commentText !== null) {
                    echo nl2br(htmlspecialchars($commentText));
                } else {
                    echo 'Comment text is empty.';
                }
                ?>
            </div>
            
            <?php include 'comment-reply-block-file-modern.php'; ?>
            
            <!-- Reply Form (Initially Hidden) -->
            <div id="replyForm_<?php echo $comment['id']; ?>" class="mt-3" style="display: none;">
                <?php include 'comment_form_reply_modern.php'; ?>
            </div>
            
            <?php include 'load_child_comments_modern.php'; ?>
        </div>
<?php
    }
}
?>

<?php if ($totalComments > $commentsPerPage): ?>
    <div style="text-align: center; margin-top: 2rem;">
        <button id="loadMoreComments" 
                style="background: var(--gradient); color: white; border: none; padding: 0.75rem 2rem; 
                       border-radius: 12px; cursor: pointer; font-weight: 500;"
                data-page="<?php echo $page + 1; ?>"
                data-entity-id="<?php echo $id_entity; ?>"
                data-entity-type="<?php echo $entity_type; ?>">
            Загрузить еще комментарии
        </button>
    </div>
<?php endif; ?>

<?php mysqli_stmt_close($stmtComments); ?>

<script>
// Toggle comment visibility
document.querySelectorAll('.toggleCard').forEach(function(toggle) {
    toggle.addEventListener('click', function() {
        const commentItem = this.closest('.comment-item');
        const content = commentItem.querySelector('.comment-content');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            content.style.display = 'none';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
});

// Load more comments
const loadMoreBtn = document.getElementById('loadMoreComments');
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
        const page = this.getAttribute('data-page');
        const entityId = this.getAttribute('data-entity-id');
        const entityType = this.getAttribute('data-entity-type');
        
        // Here you would make an AJAX request to load more comments
        // For now, just reload the page with the new page parameter
        window.location.href = window.location.pathname + '?page=' + page;
    });
}
</script>
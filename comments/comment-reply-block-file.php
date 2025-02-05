<?php
// Check if the user is suspended
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check-user-suspend.php';

// Check if the email index is set in the session
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Check if the user is suspended
$isSuspended = $userEmail ? getUserSuspensionStatus($userEmail) : false;
?>

<?php if (!$userEmail): ?>
    <div class="comment-reply-block">
        <span class="time-tooltip" data-toggle="tooltip" data-placement="top"
            title="<?php echo getFormattedDate($timestamp); ?>">
            <?php echo getElapsedTime($comment['date'], $comment['user_timezone']); ?>
        </span>
        <span class="reply-link" onclick="redirectToLogin()">Ответить</span>
    </div>
<?php else: ?>
    <div class="comment-reply-block">
        <span class="time-tooltip" data-toggle="tooltip" data-placement="top"
            title="<?php echo getFormattedDate($timestamp); ?>">
            <?php echo getElapsedTime($comment['date'], $comment['user_timezone']); ?>
        </span>
        <?php if ($isUserLoggedIn && !$isSuspended): ?>
            <span class="reply-link" onclick="toggleReplyForm(<?php echo $comment['id']; ?>)">Ответить</span>
        <?php else: ?>
            <span class="reply-link" onclick="redirectToLogin()">Ответить</span>
        <?php endif; ?>
    </div>
<?php endif; ?>
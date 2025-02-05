<?php
// Fetch child comments
$commentId = $comment['id'];

$childComments = getChildComments($commentId, $connection, $entity_type, $id_entity);
// echo $commentId, $connection, $entity_type, $id_entity;

// Display replies under the parent comment
foreach ($childComments as $reply):
    ?>
    <?php
    $replyUserNames = getUserNames($reply['user_id'], $connection);
    ?>

    <div class="d-flex align-items-start">
        <!-- Avatar Column -->
        <?php if (isset($reply['avatar']) && $reply['avatar']): ?>
            <img src="../images/avatars/<?php echo $reply['avatar']; ?>" alt="Avatar" class="rounded-circle" width="30"
                height="30">
        <?php else: ?>
            <!-- Default placeholder avatar if no avatar is available -->
            <img src="../images/avatars/default_avatar.jpg" alt="Default Avatar" class="rounded-circle" width="30" height="30">
        <?php endif; ?>
        <div class="custom-comment-reply text-break">
            <?php
            echo '<strong>';
            // Check if the user_id is 0
            if ($reply['user_id'] == 0) {
                echo $reply['author_of_comment'];

            } else {
                echo $replyUserNames['firstname'] . ' ' . $replyUserNames['lastname'];

            }

            echo '</strong><br>';
            echo nl2br(htmlspecialchars($reply['comment_text']));
            ?>
        </div>
    </div>
    <?php $timestampReply = $reply['date'] ? strtotime($reply['date']) : null; ?>
    <p class="comment-reply-block">
        <span class="time-tooltip" data-toggle="tooltip" data-placement="top"
            title="<?php echo getFormattedDate($timestampReply); ?>">
            <?php echo getElapsedTime($reply['date'], $comment['user_timezone']); ?>
        </span>
    </p>
<?php endforeach; ?>
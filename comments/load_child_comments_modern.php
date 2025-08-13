<?php
// Query to fetch child comments for a parent comment
$queryChildComments = "SELECT
    comments.id,
    comments.user_id,
    COALESCE(users.avatar, 'default_avatar.jpg') AS avatar,
    comments.comment_text,
    comments.date,
    comments.author_of_comment
FROM
    comments
LEFT JOIN users ON comments.user_id = users.id
WHERE
    comments.parent_id = ?
ORDER BY
    comments.date ASC";

$stmtChildComments = mysqli_prepare($connection, $queryChildComments);

if ($stmtChildComments) {
    mysqli_stmt_bind_param($stmtChildComments, "i", $comment['id']);
    mysqli_stmt_execute($stmtChildComments);
    $resultChildComments = mysqli_stmt_get_result($stmtChildComments);

    while ($childComment = mysqli_fetch_assoc($resultChildComments)) {
?>
        <div style="margin-left: 3rem; margin-top: 1rem;">
            <div class="comment-item" style="background: var(--bg-primary); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                <div class="comment-header">
                    <div class="comment-avatar" style="width: 36px; height: 36px;">
                        <?php 
                        $childAvatarPath = getAvatar($childComment['avatar']);
                        if ($childAvatarPath && $childComment['avatar'] !== 'default_avatar.jpg'): 
                        ?>
                            <img src="<?php echo htmlspecialchars($childAvatarPath); ?>" alt="Avatar">
                        <?php else: 
                            // Show first letter of name
                            $childFirstLetter = 'U';
                            if ($childComment['user_id'] == 0) {
                                $childFirstLetter = strtoupper(substr($childComment['author_of_comment'], 0, 1));
                            } else {
                                $childUserNames = getUserNames($childComment['user_id'], $connection);
                                $childFirstLetter = strtoupper(substr($childUserNames['firstname'], 0, 1));
                            }
                            echo $childFirstLetter;
                        ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="comment-meta">
                        <div class="comment-author" style="font-size: 0.875rem;">
                            <?php
                            if ($childComment['user_id'] == 0) {
                                echo htmlspecialchars($childComment['author_of_comment']);
                            } else {
                                $childUserNames = getUserNames($childComment['user_id'], $connection);
                                echo htmlspecialchars($childUserNames['firstname'] . ' ' . $childUserNames['lastname']);
                            }
                            ?>
                        </div>
                        <div class="comment-date" style="font-size: 0.75rem;">
                            <?php
                            $childTimestamp = isset($childComment['date']) ? strtotime($childComment['date']) : null;
                            if ($childTimestamp) {
                                echo date('d.m.Y H:i', $childTimestamp);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="comment-content" style="margin-top: 0.75rem; font-size: 0.875rem;">
                    <?php echo nl2br(htmlspecialchars($childComment['comment_text'])); ?>
                </div>
            </div>
        </div>
<?php
    }
    mysqli_stmt_close($stmtChildComments);
}
?>
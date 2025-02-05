<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

$userId = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/getRussianDate.php';

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $connection->prepare($sql);
if (!$stmt) {
    header("Location: /error");
    exit();
}
$stmt->bind_param('i', $userId);
if (!$stmt->execute()) {
    header("Location: /error");
    exit();
}
$userData = $stmt->get_result()->fetch_assoc();

// Fetch user comments
$sqlComments = "SELECT * FROM comments WHERE user_id = ? ORDER BY date DESC";
$stmtComments = $connection->prepare($sqlComments);
if (!$stmtComments) {
    header("Location: /error");
    exit();
}
$stmtComments->bind_param('i', $userId);
if (!$stmtComments->execute()) {
    header("Location: /error");
    exit();
}
$comments = $stmtComments->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php if (!empty($comments)): ?>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Дата</th>
                    <th scope="col">Комментарий</th>
                    <th scope="col" class="text-end">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= getRussianDate($comment['date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($comment['comment_text'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-end">
                            <a href="/pages/account/comments-user/comments-user-edit/comments-user-edit.php?action=edit&comment_id=<?= $comment["id"] ?>"><i class="fas fa-pencil-alt icon" data-action="edit"></i></a>
                            <a href="/pages/account/comments-user/comment-user-delete.php?action=delete&comment_id=<?= $comment["id"] ?>"
                                onclick="return confirmDelete(<?= $comment['id'] ?>, '<?= htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8') ?>');" class="text-danger" title="Удалить"><i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Комментариев не найдено.</p>
<?php endif; ?>

<script>
    function confirmDelete(commentId, commentText) {
        return confirm("Are you sure you want to delete this comment? ID: " + commentId + "\n\nComment: " + commentText);
    }
</script>
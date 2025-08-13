<?php
// Check if the user is logged in
$isUserLoggedIn = isset($_SESSION['email']);
$userId = isset($_SESSION['user_id']);
include 'comment_functions.php'; ?>

<script>
    var id_entity = <?php echo json_encode($id_entity); ?>;
    var entity_type = <?php echo json_encode($entity_type); ?>;
</script>

<!-- Set a data attribute with the login status -->
<div id="loginStatus" data-is-user-logged-in="<?php echo $isUserLoggedIn ? 'true' : 'false'; ?>"></div>

<?php
// Execute the query to check if comments exist
$queryCommentsExist = "SELECT COUNT(*) AS commentCount
FROM comments
WHERE id_entity=? AND entity_type=? AND parent_id=0";
$stmtCommentsExist = mysqli_prepare($connection, $queryCommentsExist);

if (!$stmtCommentsExist) {
    redirectToErrorPage($connection->error, __FILE__, __LINE__);
}

mysqli_stmt_bind_param($stmtCommentsExist, "is", $id_entity, $entity_type);
mysqli_stmt_execute($stmtCommentsExist);

if ($stmtCommentsExist->errno) {
    redirectToErrorPage($connection->error, __FILE__, __LINE__);
}

$resultCommentsExist = mysqli_stmt_get_result($stmtCommentsExist);
$rowCommentsExist = mysqli_fetch_assoc($resultCommentsExist);

// Check if there are comments
if ($rowCommentsExist['commentCount'] > 0) {
    include 'load_comments_modern.php';
} else {
    echo '<div style="text-align: center; color: var(--text-secondary); padding: 2rem;">
            <p>Пока нет комментариев.</p>
          </div>';
}

// Close the statement
mysqli_stmt_close($stmtCommentsExist);
?>
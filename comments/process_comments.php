<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php';
require_once 'comment-filter-functions.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/redirectToErrorPage.php";

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"]) && isset($_POST["parent_id"]) && isset($_POST["entity_type"]) && isset($_POST["id_entity"])) {

    $comment_text = trim($_POST['comment']);
    $commentText = substr($comment_text, 0, 2000); // Truncate to 2000 characters
    $commentText = sanitizeComment($commentText);
    $commentText = filterBadWords($commentText);

    $entityType = isset($_POST['entity_type']) ? $_POST['entity_type'] : ''; // Type of entity (school, university, college)
    $id_entity = isset($_POST['id_entity']) ? intval($_POST['id_entity']) : 0;
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0; // Parent comment ID

    // Validate and sanitize the input based on entity type
    switch ($entityType) {
        case 'school':
            // $id_entity = isset($_POST['id_school']) ? intval($_POST['id_school']) : 0;
            break;
        case 'vpo':
            $entityType = 'vpo'; // Set $entityType to 'vpo'
            break;
        case 'spo':
            $entityType = 'spo'; // Set $entityType to 'spo'
            break;
        case 'post':
            $entityType = 'post'; // Set $entityType to 'post'
            break;
        default:
            redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Fetch user's id based on email
    $email = $_SESSION['email'];
    $queryUserId = "SELECT id FROM users WHERE email=?";
    $stmtUserId = mysqli_prepare($connection, $queryUserId);

    if (!$stmtUserId) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    mysqli_stmt_bind_param($stmtUserId, "s", $email);
    mysqli_stmt_execute($stmtUserId);
    $resultUserId = mysqli_stmt_get_result($stmtUserId);

    // Check if user exists
    if ($rowUserId = mysqli_fetch_assoc($resultUserId)) {
        $user_id = $rowUserId['id'];

        $insertCommentQuery = "INSERT INTO comments (id_entity, user_id, comment_text, parent_id, entity_type) VALUES (?, ?, ?, ?, ?)";

        $stmtInsertComment = mysqli_prepare($connection, $insertCommentQuery);

        if (!$stmtInsertComment) {
            redirectToErrorPage($connection->error, __FILE__, __LINE__);
        } else {
            $subject = 'New Comment Notification';
            $body = "A new comment has been posted.<br><br>Comment Text: " . nl2br(htmlspecialchars($commentText ?? '')) . "";
            sendToAdmin($subject, $body);
        }

        mysqli_stmt_bind_param($stmtInsertComment, "iisss", $id_entity, $user_id, $commentText, $parent_id, $entityType);

        if (!mysqli_stmt_execute($stmtInsertComment)) {
            redirectToErrorPage($connection->error, __FILE__, __LINE__);
        }

        mysqli_stmt_close($stmtInsertComment);
    } else {
        // Handle the case where the user ID is not found
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Fetch comments for the current entity
    $queryComments = "SELECT comments.id, comments.id_entity, comments.user_id, users.avatar, comments.comment_text, comments.date
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE comments.id_entity=? AND comments.entity_type=?
    ORDER BY comments.date DESC";

    $stmtComments = mysqli_prepare($connection, $queryComments);

    if (!$stmtComments) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    mysqli_stmt_bind_param($stmtComments, "is", $id_entity, $entityType);

    if (!mysqli_stmt_execute($stmtComments)) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $resultSet = mysqli_stmt_get_result($stmtComments);

    $resultComments = array();
    while ($row = mysqli_fetch_assoc($resultSet)) {
        $resultComments[] = $row;
    }

    // Use $resultComments for further processing

    // Close the statement
    mysqli_stmt_close($stmtComments);

    // echo "Entity Type: $entityType, Entity ID: $id_entity";
    if ($entityType === 'school') {
        header("Location: /$entityType/$id_entity");
        exit();
    } else {
        $url = getEntityNameById($connection, $entityType, $id_entity);
        if ($url !== null) {
            header("Location: $url");
            exit();
        } else {
            // Handle case where URL is not constructed
            redirectToErrorPage($connection->error, __FILE__, __LINE__);
        }
    }
}

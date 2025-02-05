<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

// Function to delete a comment and its child comments recursively
function deleteComment($commentId, $connection)
{
    // Validate $commentId to prevent SQL injection
    $commentIdToDelete = intval($commentId);

    if ($commentIdToDelete <= 0) {
        // Set error message in session and redirect
        $_SESSION["error-message"] = "Invalid comment ID";
        header("Location: /account");
        exit();
    }

    // Fetch child comments recursively
    $childComments = getChildComments($commentIdToDelete, $connection);

    // Use a prepared statement to delete the comment and its child comments
    $deleteSql = "DELETE FROM comments WHERE id = ?";
    $stmt = $connection->prepare($deleteSql);

    if (!$stmt) {
        // Set error message in session and redirect
        $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
        header("Location: /account");
        exit();
    }

    // Iterate through child comments and delete them
    foreach ($childComments as $childComment) {
        // Delete each child comment
        $stmt->bind_param("i", $childComment["id"]);
        $stmt->execute();
    }

    // Now, delete the parent comment
    $stmt->bind_param("i", $commentIdToDelete);
    $stmt->execute();

    // Set success message in session and redirect to comments page
    $_SESSION["success-message"] = "Комментарий успешно удалён!";
    header("Location: /account");
    exit();

    // Close the statement
    $stmt->close();
}

// Function to fetch child comments recursively
function getChildComments($parentId, $connection)
{
    $childComments = [];

    $selectSql = "SELECT * FROM comments WHERE parent_id = ?";
    $stmt = $connection->prepare($selectSql);

    if (!$stmt) {
        // Set error message in session and redirect
        $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
        header("Location: /account");
        exit();
    }

    $stmt->bind_param("i", $parentId);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Fetch child comments recursively
        $childComments[] = $row;
        $childComments = array_merge(
            $childComments,
            getChildComments($row["id"], $connection)
        );
    }

    // Close the statement
    $stmt->close();

    return $childComments;
}

// Check if the action is delete and comment_id is set
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['comment_id'])) {
    $commentId = filter_var($_GET['comment_id'], FILTER_SANITIZE_NUMBER_INT);
    deleteComment($commentId, $connection);
} else {
    // Set error message in session and redirect
    $_SESSION["error-message"] = "Invalid action or comment ID not specified.";
    header("Location: /account");
    exit();
}

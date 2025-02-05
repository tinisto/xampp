<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";


ensureAdminAuthenticated();

// Function to delete a comment and its child comments recursively
function deleteComment($commentId, $connection)
{
    // Validate $commentId to prevent SQL injection
    $commentIdToDelete = intval($commentId);

    if ($commentIdToDelete <= 0) {
        // Set error message in session and redirect
        $_SESSION["error-message"] = "Invalid comment ID";
        header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
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
        header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
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
    $_SESSION["success-message"] = "Comment and its child comments deleted successfully!";
    header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
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
        header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
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

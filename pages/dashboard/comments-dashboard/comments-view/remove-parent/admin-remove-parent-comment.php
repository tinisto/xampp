<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

function removeParentComment($commentId, $connection)
{
    // Validate $commentId to prevent SQL injection
    $commentIdToUpvote = intval($commentId);

    if ($commentIdToUpvote <= 0) {
        // Set an error message in session and redirect
        $_SESSION["error-message"] = "Invalid comment ID.";
        header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
        exit();
    }

    // Use prepared statement to update the parent_id
    $updateSql = "UPDATE comments SET parent_id = 0 WHERE id = ?";
    $stmt = $connection->prepare($updateSql);

    if (!$stmt) {
        // Set error message in session if there is a problem with the query
        $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
        header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
        exit();
    }

    $stmt->bind_param("i", $commentIdToUpvote);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        // Set success message in session
        $_SESSION["success-message"] = "Parent comment removed successfully!";
    } else {
        // Set error message in session if no rows were affected
        $_SESSION["error-message"] = "No comment found with that ID or it is already a child comment.";
    }

    // Close the statement
    $stmt->close();

    // Redirect back to the comments page
    header("Location: /pages/dashboard/comments-dashboard/comments-view/comments-view.php");
    exit();
}

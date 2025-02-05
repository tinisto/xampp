<?php
function deleteUserComments($userId, $connection) {
    $deleteUserCommentsQuery = "DELETE FROM comments WHERE user_id = ?";
    $stmtDeleteUserComments = $connection->prepare($deleteUserCommentsQuery);
    $stmtDeleteUserComments->bind_param("i", $userId);

    if ($stmtDeleteUserComments === false) {
        echo "Error preparing user comments query: " . $connection->error;
        return false;
    }

    if (!$stmtDeleteUserComments->execute()) {
        echo "Error executing user comments deletion: " . $stmtDeleteUserComments->error;
        return false;
    }

    return true;
}
?>

<?php
function searchComments($connection, $entityType, $entityID, $currentPage, $commentsPerPage)
{
    // Modify the SQL query to consider both EntityType and EntityID
    $sql = "SELECT * FROM comments WHERE 1";

    // Check if EntityType is provided
    if (!empty($entityType)) {
        $sql .= " AND entity_type = ?";
    }

    // Check if EntityID is provided
    if (!empty($entityID)) {
        $sql .= " AND id_entity = ?";
    }

    $sql .= " ORDER BY date DESC LIMIT ?, ?";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $offset = max(0, ($currentPage - 1) * $commentsPerPage);

    // Bind parameters
    if (!empty($entityType) && !empty($entityID)) {
        mysqli_stmt_bind_param($stmt, "siii", $entityType, $entityID, $offset, $commentsPerPage);
    } elseif (!empty($entityType)) {
        mysqli_stmt_bind_param($stmt, "sii", $entityType, $offset, $commentsPerPage);
    } elseif (!empty($entityID)) {
        mysqli_stmt_bind_param($stmt, "iii", $entityID, $offset, $commentsPerPage);
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $offset, $commentsPerPage);
    }

    mysqli_stmt_execute($stmt);

    // After executing the query, check for errors
    if (mysqli_stmt_error($stmt)) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $result = mysqli_stmt_get_result($stmt);

    // After executing the query, check for errors
    if (!$result) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $comments;
}

<?php
function getUserData($connection, $userId)
{
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
    return $stmt->get_result()->fetch_assoc();
}

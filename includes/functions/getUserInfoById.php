<?php

/**
 * Retrieves user information by their ID.
 *
 * @param mysqli $connection The MySQLi connection object.
 * @param int|null $userId The ID of the user.
 * @return array|null The user information if found, or null if not found.
 */
function getUserInfoById(mysqli $connection, ?int $userId): ?array
{
    if ($userId === null) {
        return [
            'firstname' => 'Unknown',
            'lastname' => 'User',
            'avatar' => ''
        ];
    }

    $stmt = $connection->prepare("SELECT first_name, last_name, avatar_url FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($firstname, $lastname, $avatar);
        if ($stmt->fetch()) {
            $stmt->close();
            return [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'avatar' => $avatar
            ];
        } else {
            $stmt->close();
            return null;
        }
    } else {
        error_log("Error preparing statement: " . $connection->error);
        return null;
    }
}

<?php

/**
 * Retrieves the email address of a user by their ID.
 *
 * @param mysqli $connection The MySQLi connection object.
 * @param int $userId The ID of the user.
 * @return string|null The email address if found, or null if not found.
 */
function getUserEmailById(mysqli $connection, int $userId): ?string
{
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $connection->prepare("SELECT email FROM users WHERE id = ?");
    if ($stmt) {
        // Bind the user ID parameter as an integer
        $stmt->bind_param("i", $userId);
        // Execute the statement
        $stmt->execute();
        // Bind the result to a variable
        $stmt->bind_result($email);
        // Fetch the result
        if ($stmt->fetch()) {
            // Close the statement
            $stmt->close();
            // Return the email
            return $email;
        } else {
            // Close the statement
            $stmt->close();
            // Return null if no user is found
            return null;
        }
    } else {
        // Handle errors with preparing the statement
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
}

<?php

// Check if user email exists in session and set it, otherwise null
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Function to log the search query
function logSearchQuery($connection, $query, $userEmail = null)
{
    // Escape the query to prevent SQL injection
    $query = $connection->real_escape_string(trim($query));

    // Prepare the SQL query
    $sql = "INSERT INTO search_queries (query, user_email) VALUES (?, ?)";
    $stmt = $connection->prepare($sql);

    if (!$stmt) {
        // Handle SQL prepare error
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Bind parameters and execute
    $stmt->bind_param('ss', $query, $userEmail);

    if (!$stmt->execute()) {
        // Handle error in case of failure to insert
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $stmt->close();
}

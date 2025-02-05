<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get id from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Prepare and bind
    $stmt = $connection->prepare("SELECT url_post FROM posts WHERE id_post = ?");
    if ($stmt === false) {
        error_log("Error preparing statement: " . $connection->error);
        header("Location: /404");
        exit();
    }

    $stmt->bind_param("i", $id);

    // Execute the query
    if (!$stmt->execute()) {
        error_log("Error executing statement: " . $stmt->error);
        header("Location: /404");
        exit();
    }

    // Bind the result
    $stmt->bind_result($url_post);

    // Fetch the result
    if ($stmt->fetch()) {
        // Redirect to the clean URL
        header("Location: /post/$url_post", true, 301);
        exit();
    } else {
        // Handle the case where no url_post is found
        header("Location: /404");
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    header("Location: /404");
    exit();
}

// Close the connection
$connection->close();

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

if (isset($_GET["id"])) {
    $searchId = $_GET["id"];

    // Delete the message from the database
    $deleteMessageQuery = "DELETE FROM search_queries WHERE id = ?";
    $stmtDeleteMessage = $connection->prepare($deleteMessageQuery);

    if (!$stmtDeleteMessage) {
        $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
        header("Location: /dashboard/admin-search.php");
        exit();
    }

    $stmtDeleteMessage->bind_param("i", $searchId);

    if (!$stmtDeleteMessage->execute()) {
        $_SESSION["error-message"] = "Error deleting search: " . $stmtDeleteMessage->error;
    } else {
        $_SESSION["success-message"] = "Search deleted successfully!";
    }

    // Redirect back to thn-search page after deletion
    header("Location: /dashboard/admin-search.php");
    exit();
} else {
    // Handle the case where the message id is not provided or found
    $_SESSION["error-message"] = "Message not found.";
    header("Location: /dashboard/admin-search.php");
    exit();
}

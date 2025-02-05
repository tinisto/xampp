<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

if (isset($_GET["id"])) {
    $messageId = $_GET["id"];

    // Delete the message from the database
    $deleteMessageQuery = "DELETE FROM messages WHERE id = ?";
    $stmtDeleteMessage = $connection->prepare($deleteMessageQuery);

    if (!$stmtDeleteMessage) {
        $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
        header("Location: /pages/dashboard/messages-dashboard/messages-view/messages-view.php");
        exit();
    }

    $stmtDeleteMessage->bind_param("i", $messageId);

    if (!$stmtDeleteMessage->execute()) {
        $_SESSION["error-message"] = "Error deleting message: " . $stmtDeleteMessage->error;
    } else {
        $_SESSION["success-message"] = "Message deleted successfully!";
    }

    // Redirect back to the messages page after deletion
    header("Location: /pages/dashboard/messages-dashboard/messages-view/messages-view.php");
    exit();
} else {
    // Handle the case where the message id is not provided or found
    $_SESSION["error-message"] = "Message not found.";
    header("Location: /pages/dashboard/messages-dashboard/messages-view/messages-view.php");
    exit();
}

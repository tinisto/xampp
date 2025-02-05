<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

// Check if 'id' parameter is present in the URL
if (isset($_GET["id"])) {
    // Retrieve and sanitize the collegeId from the URL
    $collegeId = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);

    // Debugging: Check database connection
    if ($connection->connect_error) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Step 1: Delete all comments associated with the college
    // Delete child comments first (comments with parent_id)
    $deleteChildCommentsQuery = "DELETE FROM comments WHERE id_entity = ? AND entity_type = 'spo' AND parent_id IS NOT NULL";
    $stmtDeleteChildComments = $connection->prepare($deleteChildCommentsQuery);

    if ($stmtDeleteChildComments === false) {
        // Log MySQL error if prepare() fails
        error_log("MySQL prepare error (deleting child comments): " . $connection->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $stmtDeleteChildComments->bind_param("i", $collegeId);
    if (!$stmtDeleteChildComments->execute()) {
        error_log("Error executing query for child comments: " . $stmtDeleteChildComments->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Delete parent comments (comments without parent_id)
    $deleteParentCommentsQuery = "DELETE FROM comments WHERE id_entity = ? AND entity_type = 'spo' AND parent_id IS NULL";
    $stmtDeleteParentComments = $connection->prepare($deleteParentCommentsQuery);

    if ($stmtDeleteParentComments === false) {
        // Log MySQL error if prepare() fails
        error_log("MySQL prepare error (deleting parent comments): " . $connection->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $stmtDeleteParentComments->bind_param("i", $collegeId);
    if (!$stmtDeleteParentComments->execute()) {
        error_log("Error executing query for parent comments: " . $stmtDeleteParentComments->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Step 2: Delete the college itself
    $deleteQuery = "DELETE FROM spo WHERE id_spo = ?";
    $deleteStatement = $connection->prepare($deleteQuery);

    if ($deleteStatement === false) {
        // Log MySQL error if prepare() fails
        error_log("MySQL prepare error (deleting college): " . $connection->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $deleteStatement->bind_param("i", $collegeId);
    $result = $deleteStatement->execute();

    if (!$result) {
        error_log("Error deleting college: " . $deleteStatement->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $_SESSION["success-message"] = "College and associated comments deleted successfully.";

    // Close statements
    $deleteStatement->close();
    $stmtDeleteParentComments->close();
    $stmtDeleteChildComments->close();
} else {
    $_SESSION["error-message"] = "No collegeId specified in the URL.";
    header("Location: /error");
    exit();
}

// Redirect to the dashboard after deletion
header("Location: /dashboard");
exit();

// Flush the output buffer
ob_end_flush();

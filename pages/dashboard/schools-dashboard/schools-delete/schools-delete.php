<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

// Check if 'id' parameter is present in the URL
if (isset($_GET["id_school"])) {
    // Retrieve and sanitize the schoolId from the URL
    $schoolId = filter_var($_GET["id_school"], FILTER_SANITIZE_NUMBER_INT);

    // Debugging: Check database connection
    if ($connection->connect_error) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Step 1: Delete all comments associated with the school
    // Delete child comments first (comments with parent_id)
    $deleteChildCommentsQuery = "DELETE FROM comments WHERE id_entity = ? AND entity_type = 'school' AND parent_id IS NOT NULL";
    $stmtDeleteChildComments = $connection->prepare($deleteChildCommentsQuery);

    if ($stmtDeleteChildComments === false) {
        // Log MySQL error if prepare() fails
        error_log("MySQL prepare error (deleting child comments): " . $connection->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $stmtDeleteChildComments->bind_param("i", $schoolId);
    if (!$stmtDeleteChildComments->execute()) {
        error_log("Error executing query for child comments: " . $stmtDeleteChildComments->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Delete parent comments (comments without parent_id)
    $deleteParentCommentsQuery = "DELETE FROM comments WHERE id_entity = ? AND entity_type = 'school' AND parent_id IS NULL";
    $stmtDeleteParentComments = $connection->prepare($deleteParentCommentsQuery);

    if ($stmtDeleteParentComments === false) {
        // Log MySQL error if prepare() fails
        error_log("MySQL prepare error (deleting parent comments): " . $connection->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $stmtDeleteParentComments->bind_param("i", $schoolId);
    if (!$stmtDeleteParentComments->execute()) {
        error_log("Error executing query for parent comments: " . $stmtDeleteParentComments->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    // Step 2: Delete the school itself
    $deleteQuery = "DELETE FROM schools WHERE id_school = ?";
    $deleteStatement = $connection->prepare($deleteQuery);

    if ($deleteStatement === false) {
        // Log MySQL error if prepare() fails
        error_log("MySQL prepare error (deleting school): " . $connection->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $deleteStatement->bind_param("i", $schoolId);
    $result = $deleteStatement->execute();

    if (!$result) {
        error_log("Error deleting school: " . $deleteStatement->error);
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $_SESSION["success-message"] = "School and associated comments deleted successfully.";

    // Close statements
    $deleteStatement->close();
    $stmtDeleteParentComments->close();
    $stmtDeleteChildComments->close();
} else {
    $_SESSION["error-message"] = "No schoolId specified in the URL.";
    header("Location: /error");
    exit();
}

// Redirect to the dashboard after deletion
header("Location: /pages/dashboard/schools-dashboard/schools-view/schools-view.php");
exit();

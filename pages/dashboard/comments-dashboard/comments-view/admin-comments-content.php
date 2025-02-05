<h3 class="text-center text-white mb-3"><?php echo $pageTitle; ?></h3>

<?php
require_once "admin-comments-functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/getEntityIdFromURL.php";
include $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/comments-dashboard/comments-view/delete-comment/admin-delete-comment.php";
include $_SERVER["DOCUMENT_ROOT"] . "/pages/dashboard/comments-dashboard/comments-view/remove-parent/admin-remove-parent-comment.php";

// Check for actions
if (isset($_GET["action"])) {
    $action = $_GET["action"];

    if ($action === "removeParent") {
        // Call the function to handle removing the parent
        removeParentComment($_GET["comment_id"], $connection);
    } elseif ($action === "delete") {
        // Call the function to handle deleting the comment
        deleteComment($_GET["comment_id"], $connection);
    }
    // Add more conditions for other actions if needed
}

// Assuming that $connection is a valid MySQLi connection object
$currentPage = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

// Display comments and pagination
displayComments($connection, $currentPage);

?>
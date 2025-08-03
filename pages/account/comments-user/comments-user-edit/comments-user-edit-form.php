<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

// Function to edit a comment
function editComment($commentId, $newCommentText, $connection)
{
    // Validate input to prevent SQL injection
    $commentIdToUpdate = intval($commentId);
    $newCommentText = $connection->real_escape_string(trim($newCommentText));

    // Update the comment text
    $updateSql = "UPDATE comments SET comment_text = '$newCommentText' WHERE id = $commentIdToUpdate";

    if ($connection->query($updateSql) === true) {
        // Set a success message in session and redirect
        $_SESSION["success-message"] = "Comment updated successfully!";
        header("Location: /account");
        exit();
    } else {
        // Set an error message in session and redirect
        $_SESSION["error-message"] = "Error editing comment: " . $connection->error;
        header("Location: /account");
        exit();
    }
}

// Check for form submission
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["action"]) &&
    $_POST["action"] === "saveEdit" &&
    isset($_POST["comment_id"])
) {
    $editedCommentText = $_POST["edited_comment_text"];
    editComment($_POST["comment_id"], $editedCommentText, $connection);
}

// Check for edit action
if (
    isset($_GET["action"]) &&
    $_GET["action"] === "edit" &&
    isset($_GET["comment_id"])
) {
    // Fetch the existing comment data (assuming you have a function to retrieve comment details)
    $commentDetails = getCommentDetails($_GET["comment_id"], $connection);
    if ($commentDetails) { ?>
        <form method="post" action="?action=saveEdit&comment_id=<?php echo $commentDetails["id"]; ?>" class="my-4">
            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 300px"
                    name="edited_comment_text"><?php echo $commentDetails["comment_text"]; ?></textarea>
            </div>
            <button type="submit" class="custom-button mr-2">Update</button>
        </form>
<?php
    } else {
        // If the comment details are not found, set an error message in session and redirect
        $_SESSION["error-message"] = "Comment not found.";
        header("Location: /account");
        exit();
    }
}

// Check for saveEdit action
if (
    isset($_GET["action"]) &&
    $_GET["action"] === "saveEdit" &&
    isset($_GET["comment_id"])
) {
    $editedCommentText = $_POST["edited_comment_text"];
    editComment($_GET["comment_id"], $editedCommentText, $connection);
}

function getCommentDetails($commentId, $connection)
{
    $commentIdToFetch = intval($commentId);
    $sql = "SELECT * FROM comments WHERE id = $commentIdToFetch";
    $result = $connection->query($sql);

    if (!$result) {
        // Handle SQL error and provide feedback to the user
        $_SESSION["error-message"] = "Error fetching comment details. Please try again later.";
        error_log("SQL Error: " . $connection->error); // Optional logging
        return null;
    }

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        // Handle the case where the comment is not found
        $_SESSION["error-message"] = "Comment not found.";
        return null;
    }
}
?>
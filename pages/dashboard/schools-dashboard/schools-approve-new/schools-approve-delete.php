<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

// Check if 'id' parameter is present in the URL
if (isset($_GET["id_school"])) {
    // Retrieve and sanitize the postId from the URL
    $schoolId = filter_var($_GET["id_school"], FILTER_SANITIZE_NUMBER_INT);

    // Retrieve the image file name associated with the post
    $getImageQuery = "SELECT image_school_1 FROM schools WHERE id_school = ?";
    $getImageStatement = $connection->prepare($getImageQuery);

    // Bind parameters
    $getImageStatement->bind_param("i", $schoolId);

    // Execute the query
    $getImageStatement->execute();
    $getImageStatement->bind_result($imageFileName);
    $getImageStatement->fetch();

    // Close the statement for getting image file name
    $getImageStatement->close();

    // Perform the deletion query
    $deleteQuery = "DELETE FROM schools WHERE id_school = ?";
    $deleteStatement = $connection->prepare($deleteQuery);

    // Bind parameters
    $deleteStatement->bind_param("i", $schoolId);

    // Execute the deletion query
    $result = $deleteStatement->execute();

    // Check if the deletion was successful
    if ($result) {
        // Delete the image file associated with the school
        $uploadDirectory =
            $_SERVER["DOCUMENT_ROOT"] . "/images/schools-images/";
        $imageFilePath = $uploadDirectory . $imageFileName;
        if (file_exists($imageFilePath)) {
            unlink($imageFilePath);
        } else {
            // Set a session or a flag to show an image not found message after redirect
            $_SESSION["warning-message"] = "Файл изображения не найден.";
        }

        // Set a success message
        $_SESSION["success-message"] = "School deleted successfully!";
    } else {
        // Set an error message for failure
        $_SESSION["error-message"] =
            "Error deleting VPO: " . $deleteStatement->error;
    }

    // Close the statement for deletion
    $deleteStatement->close();
} else {
    $_SESSION["error-message"] = "No School specified for deletion.";
}

// Redirect to your posts page after deletion (adjust the path accordingly)
header("Location: /dashboard/admin-approve-school.php");
exit();

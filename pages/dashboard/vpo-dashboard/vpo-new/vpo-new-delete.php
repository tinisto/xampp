<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";


// Check if 'id' parameter is present in the URL
if (isset($_GET["id_vpo"])) {
    // Retrieve and sanitize the postId from the URL
    $collegeId = filter_var($_GET["id_vpo"], FILTER_SANITIZE_NUMBER_INT);

    // Retrieve the image file name associated with the post
    $getImageQuery =
        "SELECT image_university_1 FROM vpo WHERE id_vpo = ?";
    $getImageStatement = $connection->prepare($getImageQuery);

    // Bind parameters
    $getImageStatement->bind_param("i", $collegeId);

    // Execute the query
    $getImageStatement->execute();
    $getImageStatement->bind_result($imageFileName);
    $getImageStatement->fetch();

    // Close the statement for getting image file name
    $getImageStatement->close();

    // Perform the deletion query
    $deleteQuery = "DELETE FROM vpo WHERE id_vpo = ?";
    $deleteStatement = $connection->prepare($deleteQuery);

    // Bind parameters
    $deleteStatement->bind_param("i", $collegeId);

    // Execute the deletion query
    $result = $deleteStatement->execute();

    // Check if the deletion was successful
    if ($result) {
        // Delete the image file associated with the college
        $uploadDirectory =
            $_SERVER["DOCUMENT_ROOT"] . "/images/universities-images/";
        $imageFilePath = $uploadDirectory . $imageFileName;
        if (file_exists($imageFilePath)) {
            unlink($imageFilePath);
        } else {
            // Set a session or a flag to show an image not found message after redirect
            $_SESSION["warning-message"] = "Файл изображения не найден.";
        }

        $_SESSION["success-message"] = "VPO deleted successfully!";
    } else {
        // Set an error message for failure
        $_SESSION["error-message"] =
            "Error deleting VPO: " . $deleteStatement->error;
    }

    // Close the statement for deletion
    $deleteStatement->close();
} else {
    $_SESSION["error-message"] = "No VPO specified for deletion.";
}

// Redirect to your posts page after deletion (adjust the path accordingly)
header("Location: /dashboard/admin-approve-vpo.php");
exit();

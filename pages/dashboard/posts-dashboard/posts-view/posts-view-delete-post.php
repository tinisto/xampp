<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
ensureAdminAuthenticated();

// Check if 'id' parameter is present in the URL
if (isset($_GET['id'])) {
  // Retrieve and sanitize the postId from the URL
  $postId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

  // Retrieve the image file names associated with the post
  $getImageQuery = "SELECT image_post_1, image_post_2, image_post_3 FROM posts WHERE id_post = ?";
  $getImageStatement = $connection->prepare($getImageQuery);

  // Bind parameters
  $getImageStatement->bind_param("i", $postId);

  // Execute the query
  $getImageStatement->execute();
  $getImageStatement->bind_result($imageFile1, $imageFile2, $imageFile3);
  $getImageStatement->fetch();

  // Close the statement for getting image file names
  $getImageStatement->close();

  // Perform the deletion query
  $deleteQuery = "DELETE FROM posts WHERE id_post = ?";
  $deleteStatement = $connection->prepare($deleteQuery);

  // Bind parameters
  $deleteStatement->bind_param("i", $postId);

  // Execute the deletion query
  $result = $deleteStatement->execute();

  // Check if the deletion was successful
  if ($result) {
    // Delete the image files associated with the post
    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/images/posts-images/';
    $imageFiles = [$imageFile1, $imageFile2, $imageFile3];
    foreach ($imageFiles as $imageFile) {
      $imageFilePath = $uploadDirectory . $imageFile;
      if ($imageFile && is_file($imageFilePath)) {
        unlink($imageFilePath);
      } elseif ($imageFile) {
        // Set a session or a flag to show an image not found message after redirect
        $_SESSION["warning-message"] = "Файл изображения не найден: $imageFile";
      }
    }

    $_SESSION["success-message"] = "Post deleted successfully!";
  } else {
    // Set an error message for failure
    $_SESSION["error-message"] =
      "Error deleting VPO: " . $deleteStatement->error;
  }

  // Close the statement for deletion
  $deleteStatement->close();
} else {
  $_SESSION["error-message"] = "No postId specified for deletion.";
}

// Redirect to your posts page after deletion (adjust the path accordingly)
header("Location: /pages/dashboard/posts-dashboard/posts-view/posts-view.php");
exit();

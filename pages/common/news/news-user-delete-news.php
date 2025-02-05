<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

// Check if 'id_news' parameter is present in the URL
if (isset($_GET['id_news'])) {
  // Retrieve and sanitize the newsId from the URL
  $newsId = filter_var($_GET['id_news'], FILTER_SANITIZE_NUMBER_INT);

  // Retrieve the image file names associated with the post
  $getImageQuery = "SELECT image_news_1, image_news_2, image_news_3 FROM news WHERE id_news = ?";
  $getImageStatement = $connection->prepare($getImageQuery);

  // Bind parameters
  $getImageStatement->bind_param("i", $newsId);

  // Execute the query
  $getImageStatement->execute();
  $getImageStatement->bind_result($imageFileName1, $imageFileName2, $imageFileName3);
  $getImageStatement->fetch();

  // Close the statement for getting image file names
  $getImageStatement->close();

  // Perform the deletion query
  $deleteQuery = "DELETE FROM news WHERE id_news = ?";
  $deleteStatement = $connection->prepare($deleteQuery);

  // Bind parameters
  $deleteStatement->bind_param("i", $newsId);

  // Execute the deletion query
  $result = $deleteStatement->execute();

  // Check if the deletion was successful
  if ($result) {
    // Delete the image files associated with the post
    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/images/news-images/';
    $imageFilePaths = [
      $uploadDirectory . $imageFileName1,
      $uploadDirectory . $imageFileName2,
      $uploadDirectory . $imageFileName3
    ];

    foreach ($imageFilePaths as $imageFilePath) {
      if (is_file($imageFilePath)) {
        unlink($imageFilePath);
      }
    }

    $_SESSION["success-message"] = "Новость успешно удалена!";
  } else {
    $_SESSION["error-message"] = "Error deleting News: " . $deleteStatement->error;
  }

  // Close the statement for deletion
  $deleteStatement->close();
} else {
  $_SESSION["error-message"] = "No newsId specified for deletion.";
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($isAdmin) {
  header("Location: /pages/dashboard/news-dashboard/admin-view-news/admin-view-news.php");
} else {
  header("Location: /account");
}

exit();

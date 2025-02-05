<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';


$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if file was uploaded without errors
  if ($_FILES['newAvatar']['error'] == UPLOAD_ERR_OK && !empty($_FILES['newAvatar']['tmp_name'])) {
    // Get information about the uploaded file
    $fileInfo = pathinfo($_FILES['newAvatar']['name']);

    // Validate avatar file type and size
    $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if (!in_array(strtolower($fileInfo['extension']), $allowedFileTypes) || $_FILES['newAvatar']['size'] > $maxFileSize) {
      $errors[] = "Неверный формат или размер файла аватара. Разрешены: " . implode(', ', $allowedFileTypes) . ". Max size: 5 MB.";
    }

    if (empty($errors)) {

      // Generate a unique name for the file
      $avatarFileName = 'avatar_' . uniqid() . '.' . strtolower($fileInfo['extension']);
      // Move the file to a specified directory
      $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/images/avatars/';
      $destination = $uploadDirectory . $avatarFileName;
      move_uploaded_file($_FILES['newAvatar']['tmp_name'], $destination);


      // Update the user's avatar in the database if needed
      $email = $_SESSION['email'];
      $updateAvatarQuery = "UPDATE users SET avatar = ? WHERE email = ?";
      $stmt = $connection->prepare($updateAvatarQuery);
      $stmt->bind_param("ss", $avatarFileName, $email);
      $stmt->execute();
      $stmt->close();

      // Update the session variable with the new avatar filename
      $_SESSION['avatar'] = $avatarFileName;
    }
  }
}

// Redirect back to the profile page after creating the avatar
header("Location: /account");
$connection->close();
exit();

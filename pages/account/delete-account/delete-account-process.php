<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

require_once 'delete-child-comments.php';
require_once 'delete-user-comments.php';

// Verify CSRF token
if (
  !isset($_POST['csrf_token']) ||
  !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
  // CSRF token mismatch, handle the error
  $_SESSION["error-message"] = "CSRF token mismatch. Please try again.";
  header("Location: /account");
  exit();
}

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($email)) {
  $_SESSION["error-message"] = "Email не найден в сеансе. Пожалуйста, попробуйте снова.";
  header("Location: /account");
  exit();
}

// Retrieve user data based on email
$getUserQuery = "SELECT id, password, avatar FROM users WHERE email = ?";
$stmtUser = $connection->prepare($getUserQuery);

if (!$stmtUser) {
  $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
  header("Location: /account");
  exit();
}

$stmtUser->bind_param("s", $email);

if (!$stmtUser->execute()) {
  $_SESSION["error-message"] = "Error executing statement: " . $stmtUser->error;
  header("Location: /account");
  exit();
}

$resultUser = $stmtUser->get_result();

if (!$resultUser) {
  $_SESSION["error-message"] = "Error getting user data: " . $stmtUser->error;
  header("Location: /account");
  exit();
}

// Fetch user data
$userData = $resultUser->fetch_assoc();

// Check if the user exists
if (!$userData) {
  $_SESSION["error-message"] = "Данные пользователя не найдены. Пожалуйста, попробуйте снова.";
  header("Location: /account");
  exit();
}

$userId = $userData['id'];
$avatarFileName = $userData['avatar'];

// Close the result set
$stmtUser->close();

$password = isset($_POST["password"]) ? $_POST["password"] : '';

if (password_verify($password, $userData['password'])) {
  // Remove the foreign key constraint temporarily
  $removeForeignKeyQuery = "SET foreign_key_checks = 0";
  $stmtRemoveForeignKey = $connection->prepare($removeForeignKeyQuery);

  if (!$stmtRemoveForeignKey) {
    $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
    header("Location: /account");
    exit();
  }

  if (!$stmtRemoveForeignKey->execute()) {
    $_SESSION["error-message"] = "Error executing statement: " . $stmtRemoveForeignKey->error;
    header("Location: /account");
    exit();
  }

  // Step 1: Get the IDs of the user's comments (parent comments)
  $getUserCommentsQuery = "SELECT id FROM comments WHERE user_id = ?";
  $stmtGetUserComments = $connection->prepare($getUserCommentsQuery);
  $stmtGetUserComments->bind_param("i", $userId);

  if (!$stmtGetUserComments) {
    $_SESSION["error-message"] = "Error preparing query: " . $connection->error;
    header("Location: /account");
    exit();
  }

  if (!$stmtGetUserComments->execute()) {
    $_SESSION["error-message"] = "Error executing user comments query: " . $stmtGetUserComments->error;
    header("Location: /account");
    exit();
  }

  $result = $stmtGetUserComments->get_result();
  $parentIds = []; // Initialize the parentIds array

  while ($row = $result->fetch_assoc()) {
    $parentIds[] = $row['id'];  // Collect the parent comment IDs
  }

  // Step 2: Delete all child comments that reference the user's comments (parent comments)
  if (!empty($parentIds) && !deleteChildComments($parentIds, $connection)) {
    $_SESSION["error-message"] = "Error deleting child comments.";
    header("Location: /account");
    exit();
  }

  // Step 3: Delete the user's own comments (parent comments)
  if (!deleteUserComments($userId, $connection)) {
    $_SESSION["error-message"] = "Error deleting user's comments.";
    header("Location: /account");
    exit();
  }

  // Delete the user's avatar file
  $avatarPath = $_SERVER['DOCUMENT_ROOT'] . '/images/avatars/' . $avatarFileName;
  if (file_exists($avatarPath) && is_file($avatarPath)) {
    unlink($avatarPath); // Delete the file
  }

  // Delete the user
  $deleteQuery = "DELETE FROM users WHERE id = ?";
  $stmtDelete = $connection->prepare($deleteQuery);

  if (!$stmtDelete) {
    $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
    header("Location: /account");
    exit();
  }

  $stmtDelete->bind_param("i", $userId);

  if (!$stmtDelete->execute()) {
    $_SESSION["error-message"] = "Error executing statement: " . $stmtDelete->error;
    header("Location: /account");
    exit();
  }

  // Re-add the foreign key constraint
  $addForeignKeyQuery = "SET foreign_key_checks = 1";
  $stmtAddForeignKey = $connection->prepare($addForeignKeyQuery);

  if (!$stmtAddForeignKey) {
    $_SESSION["error-message"] = "Error preparing statement: " . $connection->error;
    header("Location: /account");
    exit();
  }

  if (!$stmtAddForeignKey->execute()) {
    $_SESSION["error-message"] = "Error executing statement: " . $stmtAddForeignKey->error;
    header("Location: /account");
    exit();
  }
  header("Location: /success-delete.php");
  exit();
} else {
  // Password is incorrect, set an error message
  $_SESSION['error-message'] = "Неверный пароль. Пожалуйста, попробуйте снова.";
  header("Location: /account");
  exit();
}

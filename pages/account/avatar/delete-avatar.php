<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

// Check if the user has an avatar
$email = $_SESSION['email'];
$query = "SELECT avatar FROM users WHERE email = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (empty($userData['avatar'])) {
  header("Location: /account"); // Redirect to profile if no avatar
  exit();
}

// Delete the avatar file
$avatarFile = $_SERVER['DOCUMENT_ROOT'] . '/images/avatars/' . $userData['avatar'];
if (file_exists($avatarFile)) {
  unlink($avatarFile);
}

// Remove the avatar reference from the database
$updateQuery = "UPDATE users SET avatar = NULL WHERE email = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

$_SESSION['avatar'] = NULL; // Clear the session avatar
header("Location: /account"); // Redirect back to the profile page

$connection->close();
exit();

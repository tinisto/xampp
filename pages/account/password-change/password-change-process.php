<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config/constants.php';

// Customize the email subject and body
$customSubject = 'Изменение пароля на сайте 11klassniki.ru';

$customBody = "
<p style='font-size: 14px;'>Здравствуйте!<br><br>
Ваш пароль был успешно изменен на сайте 11klassniki.ru<br>
Если вы не совершали это действие, пожалуйста, свяжитесь с нами по адресу 
<a href='mailto:" . ADMIN_EMAIL . "'>" . ADMIN_EMAIL . "</a><br><br>
С наилучшими пожеланиями, команда 11klassniki.ru</p>";


// Get the user's current information
$email = $_SESSION['email'];

// Handle changing password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changePassword'])) {
  $oldPassword = $_POST["oldPassword"];
  $newPassword = $_POST["newPassword"];
  $confirmPassword = $_POST["confirmPassword"];

  // Verify the old password
  $query = "SELECT * FROM users WHERE email = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $userData = $result->fetch_assoc();
  $stmt->close();

  if (password_verify($oldPassword, $userData['password'])) {
    // Validate the new password
    if (!empty($newPassword) && $newPassword == $confirmPassword) {
      // Hash the new password
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

      // Update the password in the database
      $updatePasswordQuery = "UPDATE users SET password = ? WHERE email = ?";
      $stmt = $connection->prepare($updatePasswordQuery);
      $stmt->bind_param("ss", $hashedPassword, $email);
      $stmt->execute();
      $stmt->close();

      sendToUser($email, $customSubject, $customBody);

      // Set a session variable for success
      $_SESSION['success-message'] = "Пароль обновлен успешно.";

      // Redirect back to profile page
      header("Location: /account");
      exit();
    } else {
      $_SESSION['error-message'] = "Введенные пароли не совпадают. Пожалуйста, проверьте их и повторите попытку.";
      header("Location: /account");
    }
  } else {
    $_SESSION['error-message'] = "Старый пароль введен неверно.";
    header("Location: /account");
  }

  exit();
}

// Close the main database connection
$connection->close();

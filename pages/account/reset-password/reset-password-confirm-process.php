<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// Customize the email subject and body
$customSubject = 'Изменение пароля на сайте 11klassniki.ru';
$customBody = "
<p style='font-size: 14px;'>Здравствуйте!<br><br>
Ваш пароль был успешно изменен на сайте 11klassniki.ru<br>
Если вы не совершали это действие, пожалуйста, свяжитесь с нами по адресу <a href='mailto:support@11klassniki.ru'>support@11klassniki.ru</a><br><br>
С наилучшими пожеланиями, команда 11klasssniki.ru</p>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if 'token' and 'password' keys are present in $_POST
  if (isset($_POST['token'], $_POST['email'], $_POST['password'])) {
    // Validate and sanitize token and password
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Validate password strength
    $errorMessage = null;
    if (strlen($password) < 8) {
        $errorMessage = "Пароль должен содержать минимум 8 символов.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/', $password)) {
        $errorMessage = "Пароль должен содержать минимум одну строчную букву, одну заглавную букву, одну цифру и один специальный символ.";
    }
    
    if (!$errorMessage) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Debugging output
        // echo "Debug: token=$token, email=$email, hashedPassword=$hashedPassword<br>";

        // Update the user's password and token in the database
        $updateQuery = "UPDATE users SET password = ?, activation_token = ? WHERE email = ?";
        // echo "Debug: Update Query - $updateQuery<br>"; // Debugging output


        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($connection, $updateQuery);

    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "sss", $hashedPassword, $token, $email);

      if (mysqli_stmt_execute($stmt)) {
        sendPasswordChangedEmail($email, $customSubject, $customBody);
        $successMessage = "Пароль успешно изменен. Теперь вы можете войти.";
        header("Location: /login?registration_success=true&message=" . urlencode($successMessage));
        // Redirect to login with success message
        exit();
      } else {
        $errorMessage = "Ошибка при обновлении пароля. Пожалуйста, попробуйте снова.";
        // Debugging output
        echo "Debug: MySQL Error - " . mysqli_error($connection) . "<br>";
      }

      // Close the statement
      mysqli_stmt_close($stmt);
    } else {
      $errorMessage = "Ошибка при обновлении пароля. Пожалуйста, попробуйте снова.";
      // Debugging output
      echo "Debug: MySQL Error - " . mysqli_error($connection) . "<br>";
    }
    } // End of password validation if
  } else {
    // Handle missing keys
    $errorMessage = "Invalid form submission. Please try again.";
  }

  if (isset($errorMessage)) {
    echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
  }
}
// Close the database connection
mysqli_close($connection);

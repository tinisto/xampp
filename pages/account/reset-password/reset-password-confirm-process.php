<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-changed.php';

// Customize the email subject
$customSubject = 'Пароль изменен - 11klassniki.ru';

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
        // Get user's first name if available
        $nameQuery = "SELECT firstname FROM users WHERE email = ?";
        $nameStmt = mysqli_prepare($connection, $nameQuery);
        $firstname = '';
        if ($nameStmt) {
            mysqli_stmt_bind_param($nameStmt, "s", $email);
            mysqli_stmt_execute($nameStmt);
            $nameResult = mysqli_stmt_get_result($nameStmt);
            if ($row = mysqli_fetch_assoc($nameResult)) {
                $firstname = $row['firstname'];
            }
            mysqli_stmt_close($nameStmt);
        }
        
        // Send email with modern template
        $customBody = getPasswordChangedEmailTemplate($firstname);
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

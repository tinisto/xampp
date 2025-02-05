<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Debugging statement to inspect $_POST
  // echo '<pre>' . print_r($_POST, true) . '</pre>';

  // Check if 'token', 'email', and 'password' keys are present in $_POST
  if (isset($_POST['token'], $_POST['email'], $_POST['password'])) {
    // Validate and sanitize token, email, and password
    $token = filter_var($_POST['token'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  } else {
    // Log or echo a message to indicate missing keys
    echo '<div class="alert alert-danger" role="alert">Неверная отправка формы. Пожалуйста, попробуйте снова.</div>';
  }
}
?>
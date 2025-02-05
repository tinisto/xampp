<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config/constants.php';

$message = "";

if (isset($_GET['token'])) {
  $activationToken = $_GET['token'];

  // Update user's activation status
  $updateQuery = "UPDATE users SET is_active = 1, activation_token = NULL, activation_link = NULL WHERE activation_token = ?";
  $stmt = $connection->prepare($updateQuery);
  $stmt->bind_param("s", $activationToken);
  $stmt->execute();

  // Check if the activation was successful
  $affectedRows = $stmt->affected_rows;
  $stmt->close();

  if ($affectedRows > 0) {
    // Account activated successfully
    $message .= 'Аккаунт успешно активирован. Теперь вы можете войти в свой аккаунт.';
    header("Location: /login?registration_success=true&message=" . urlencode($message));
    // Redirect to login with success message
    exit();
  } else {
    // Activation token is not valid
    $message = '<div class="alert alert-danger" role="alert">';
    $message .= '<h4 class="alert-heading">Недействительный токен активации!</h4>';
    $message .= '<p>Возможно, токен был использован или устарел.</p>';
    $message .= '<p>Пожалуйста, проверьте вашу электронную почту (включая папку спама) для активации.</p>';
    $message .= '<p>Если вы не нашли письмо, вы можете запросить новый токен активации, используя <a href="/login">опцию "Отправить снова"</a> на странице входа.</p>';
    $message .= '<p>Если проблема не устранена, свяжитесь с поддержкой для получения дополнительной помощи: ';
    $message .= '<a href="mailto:' . ADMIN_EMAIL . '">' . ADMIN_EMAIL . '</a>';
    $message .= '</div>';
  }
} else {
  // Token is not provided in the URL
  $message .= 'Отсутствует токен активации в URL. Пожалуйста, проверьте вашу электронную почту или свяжитесь с поддержкой: ';
  $message .= '<a href="mailto:' . ADMIN_EMAIL . '">' . ADMIN_EMAIL . '</a>';
  $message .= '</div>';
}
$connection->close();

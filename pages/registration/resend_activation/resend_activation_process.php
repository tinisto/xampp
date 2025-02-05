<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

include $_SERVER['DOCUMENT_ROOT'] . '/config/constants.php';

include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
$message = ""; // Initialize message

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['email'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : trim($_GET['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Введите действительный адрес электронной почты.";
    } else {
        try {
            // Check if email exists in the database
            $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
            $stmt = $connection->prepare($checkEmailQuery);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $activationToken = bin2hex(random_bytes(32));
                $updateTokenQuery = "UPDATE users SET activation_token = ? WHERE email = ?";
                $stmt = $connection->prepare($updateTokenQuery);
                $stmt->bind_param("ss", $activationToken, $email);
                $stmt->execute();

                $activationLink = "http://$_SERVER[HTTP_HOST]/pages/registration/activate_account/activate_account.php?token=$activationToken";

                $customSubject = 'Не забывайте: Активируйте свой аккаунт на 11klassniki.ru';
                $customBody = "
                    <p style='font-size: 14px;'>Здравствуйте!<br><br>
    Для активации вашего аккаунта, перейдите по следующей <a href=\"$activationLink\">ссылке</a>.<br><br>
    Пожалуйста, активируйте ваш аккаунт в течение 24 часов. После этого срока токен активации может устареть.<br><br>
    Если вы не совершали это действие, пожалуйста, свяжитесь с нами по адресу 
    <a href='mailto:" . ADMIN_EMAIL . "'>" . ADMIN_EMAIL . "</a><br><br>
                С наилучшими пожеланиями, команда 11klasssniki.ru</p>";
                sendActivationEmail($email, $activationLink, $customSubject, $customBody);
                $message = "Ваш аккаунт не активирован. Новый токен активации был отправлен на вашу электронную почту.";
                header("Location: /login?registration_success=true&message=" . urlencode($message));
                exit();
            } else {
                $message = "Адрес электронной почты не найден в базе данных.";
            }
        } catch (Exception $e) {
            $message = "Произошла ошибка при обработке запроса. Пожалуйста, попробуйте позже.";
        }
    }
}

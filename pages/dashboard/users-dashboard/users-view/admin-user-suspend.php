<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

ensureAdminAuthenticated();

if (isset($_GET["id"])) {
    $userId = $_GET["id"];

    // Fetch the user's email by ID
    $userEmail = getUserEmailById($userId);

    if ($userEmail) {
        // Perform the suspend operation, for example:
        $sql = "UPDATE users SET is_suspended = 1 WHERE id = $userId";
        $result = $connection->query($sql);

        $subject = "Ваш аккаунт заблокирован";
        $reasonForSuspension = "Нарушение правил сообщества"; // Замените на реальную причину

        $body = "Уважаемый(ая) пользователь,<br><br>";
        $body .=
            "С сожалением сообщаем вам, что ваш аккаунт был заблокирован из-за нарушения правил нашего сообщества или условий использования.<br><br>";
        $body .= "Причина блокировки: $reasonForSuspension<br><br>";
        $body .=
            "Если вы считаете, что блокировка была совершена по ошибке, или у вас есть вопросы, пожалуйста, свяжитесь с нашей службой поддержки по адресу support@11klassniki.ru.<br><br>";
        $body .= "Благодарим за ваше понимание.<br><br>";
        $body .= "С наилучшими пожеланиями,<br>";
        $body .= "Команда поддержки 11klassniki";

        sendEmailToAdminAboutNewComment($userEmail, $subject, $body);

        // Redirect back to the users page after suspension
        header("Location: /dashboard/admin-users.php");
        exit();
    } else {
        // Handle the case where user email is not found
        echo "User email not found";
    }
}

function getUserEmailById($userId)
{
    global $connection; // Assuming $connection is your database connection object

    // Perform a database query to fetch the user's email by ID
    $sql = "SELECT email FROM users WHERE id = $userId";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["email"];
    } else {
        // Handle the case where the user is not found
        return null;
    }
}

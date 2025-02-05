<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

ensureAdminAuthenticated();

if (isset($_GET["id"])) {
    $userId = $_GET["id"];

    // Fetch the user's email by ID
    $userEmail = getUserEmailById($userId);

    if ($userEmail) {
        // Perform the unsuspend operation (set is_suspended to 0)
        $sql = "UPDATE users SET is_suspended = 0 WHERE id = $userId";
        $result = $connection->query($sql);

        $subject = "Ваш аккаунт был восстановлен";
        $body = "Уважаемый(ая) пользователь,<br><br>";
        $body .= "Мы рады сообщить вам, что ваш аккаунт был восстановлен.<br><br>";
        $body .= "Если у вас есть дополнительные вопросы, не стесняйтесь обратиться в нашу службу поддержки по адресу support@11klassniki.ru.<br><br>";
        $body .= "С наилучшими пожеланиями,<br>";
        $body .= "Команда поддержки 11klassniki";

        sendEmailToAdminAboutNewComment($userEmail, $subject, $body);

        // Redirect back to the users page after unsuspension
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

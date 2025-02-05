<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';


// Initialize $token variable outside the block
$token = '';

// Initialize $resetSuccess variable to track the success of sending the email
$resetSuccess = false;

// Handle form submission to initiate password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the email field is set and not empty
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        // Validate and sanitize email address
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        // Check if the email exists in the database
        $emailExists = checkEmailExistsInDatabase($email, $connection);

        if ($emailExists) {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));

            // Set the expiration time for the token (e.g., 1 hour)
            $expiration_time = time() + 3600; // 1 hour

            // Store the token, email, and expiration time in the database
            // Implement your database connection and queries here
            // Example: mysqli_query($conn, "INSERT INTO password_reset_tokens (email, token, expiration_time) VALUES ('$email', '$token', '$expiration_time')");

            // Send an email to the user with a link containing the token
            $resetLink = "http://$_SERVER[HTTP_HOST]/reset-password-confirm.php?token=$token&email=$email";

            // Customize the email subject and body
            $customSubject = 'Сброс пароля на сайте 11klassniki.ru';

            $customBody = "<p style='font-size: 14px;'>Здравствуйте!<br><br>
            Вы запросили сброс пароля на сайте 11klassniki.ru. Чтобы сбросить пароль, перейдите по следующей <a href=\"$resetLink\">ссылке</a><br><br>
            Пожалуйста, сбросьте пароль в течение 1 часа. После этого срока токен сброса пароля может устареть.<br><br>
            Если вы не совершали это действие, пожалуйста, свяжитесь с нами по адресу <a href='mailto:support@11klassniki.ru'>support@11klassniki.ru</a><br><br>
            С наилучшими пожеланиями,<br>
            команда 11klasssniki.ru</p>";

            // Use your preferred method to send the email (mail, PHPMailer, etc.)
            // Example using PHPMailer:

            // Call the sendPasswordResetEmail function with the appropriate parameters
            sendPasswordResetEmail($email, $resetLink, $customSubject, $customBody);

            // Set the success flag to true
            $resetSuccess = true;
        } else {
            // Redirect to error page if email does not exist
            header("Location: /error");
            exit();
        }
    } else {
        // Redirect to error page if email is not set or is empty
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }
}

$pageTitle = 'Сброс пароля';
$mainContent = 'reset-password-content.php';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-authorization.php';

// Function to check if email exists in the database
function checkEmailExistsInDatabase($email, $connection)
{
    $email = mysqli_real_escape_string($connection, $email); // Sanitize the email

    // Replace 'users' with your actual users table name
    $query = "SELECT COUNT(*) as count FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        // Handle the query error by redirecting to the error page
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $row = mysqli_fetch_assoc($result);

    // If the count is greater than 0, the email exists
    return $row['count'] > 0;
}

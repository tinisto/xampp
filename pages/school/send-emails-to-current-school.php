<?php
// Set the root directory path once
$rootDir = $_SERVER['DOCUMENT_ROOT'];

// Include necessary files
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

ensureAdminAuthenticated();
include $rootDir . '/functions/email_functions.php';

// Sanitize and retrieve values from POST request
$idSchool = filter_input(INPUT_POST, 'id_school', FILTER_SANITIZE_NUMBER_INT);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$directorEmail = filter_input(INPUT_POST, 'director_email', FILTER_SANITIZE_EMAIL);

// Echo input data for debugging (optional)
echo htmlspecialchars($email) . '<br>';
echo htmlspecialchars($directorEmail) . '<br>';

// Function to send emails to a specific university
function sendEmailsToSchool($idSchool, $email, $directorEmail)
{
  // Verify that $email is valid
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Define the subject and links
    $subject = 'Информация для администрации учебного заведения';
    $link = "http://$_SERVER[HTTP_HOST]/school/{$idSchool}";
    $linkCategory = "http://$_SERVER[HTTP_HOST]/category/abiturientam";

    // Email body content (HTML formatted)
    $emailBody = "
<html>
<head>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}
.container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}
h1 {
    color: #333;
}
p {
    color: #555;
    font-size: 16px;
    line-height: 1.6;
}
a {
    color: #4CAF50;
    text-decoration: none;
}
.cta-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}
.signature {
    margin-top: 20px;
    font-weight: bold;
}
.footer {
    margin-top: 20px;
    font-size: 12px;
    color: #777;
}
</style>
</head>
<body>
<div class='container'>
<h1>Здравствуйте!</h1>
<p>Меня зовут Леонид, и я представляю сайт 11klassniki.ru.</p>
<p>Мы приглашаем ваше учебное заведение размещать новости на нашем сайте, чтобы рассказать о своей деятельности.</p>
<p>Для добавления новостей необходимо авторизоваться. Вы сможете публиковать актуальную информацию, сопровождать её фотографиями и в любой момент редактировать или удалять свои публикации после модерации.</p>
<p>Просим вас обновить данные, так как текущая информация устарела. Пожалуйста, перейдите по следующей ссылке, чтобы обновить <a href=\"$link\">ваше учебное заведение</a>.</p>
<div class='signature'>
    С уважением, Леонид<br>Служба поддержки 11klassniki.ru
</div>
<div class='footer'>
    Если у вас есть вопросы, не стесняйтесь обращаться к нам.
</div>
</div>
</body>
</html>
";

    // Send email to the first recipient
    sendEmails($email, $subject, $emailBody);

    // Validate and send to director email
    if (filter_var($directorEmail, FILTER_VALIDATE_EMAIL)) {
      sendEmails($directorEmail, $subject, $emailBody);
    }

    echo "<p>Emails sent successfully.</p>";
  } else {
    echo "<p>Invalid email address: $email</p>";
  }
}

// Call the function to send emails to the specific university
sendEmailsToSchool($idSchool, $email, $directorEmail);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Notification Sent</title>
</head>

<body>
  <h1>Sent Emails to <?php echo htmlspecialchars($idSchool); ?></h1>
  <!-- Your additional HTML content here -->
</body>

</html>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

ensureAdminAuthenticated();
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// Determine the type (vpo or spo) based on the URL
$requestUri = $_SERVER['REQUEST_URI'];
$type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
$urlField = 'url_slug'; // Using 'url_slug' for both
$subject = $type === 'vpo' ? 'Приглашение к сотрудничеству на 11klassniki.ru' : 'Информация для администрации учебного заведения';

// Retrieve values from hidden input fields
$url = $_POST[$urlField];
$email = $_POST['email'];
$emailPk = $_POST['admission_email'];
$directorEmail = $_POST['director_email'];

// Echo input data for debugging (optional)
echo htmlspecialchars($email) . '<br>';
echo htmlspecialchars($emailPk) . '<br>';
echo htmlspecialchars($directorEmail) . '<br>';

// Function to send emails to a specific institution
function sendEmailsToInstitution($url, $email, $emailPk, $directorEmail, $subject)
{
    // Verify that $email is not empty and is a valid email address
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $Link = "http://$_SERVER[HTTP_HOST]/$url";
        $LinkCategory = "http://$_SERVER[HTTP_HOST]/category/abiturientam";

        // Define email body content
        $emailBody = "
<html>
<head>
<style>
body {
    font-family: 'Arial', sans-serif;
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
<p>Ваше участие на нашей платформе — это прекрасная возможность бесплатно рассказать о вашем учебном заведении, укрепить его репутацию и привлечь внимание будущих студентов.</p>
<p>Мы также приглашаем студентов вашего учебного заведения поделиться своими историями о выборе вашего учебного заведения. Мы уверены, что рассказы о личном опыте будут отличной рекламой для вашего учебного заведения и могут стать ценным вкладом в портфолио студентов. Вы можете ознакомиться с публикациями недавних школьников в рубрике <a href=\"$LinkCategory\">Абитуриентам</a>.</p>
<p>Просим вас обновить данные, так как текущая информация устарела. Пожалуйста, перейдите по следующей ссылке, чтобы обновить <a href=\"$Link\">ваше учебное заведение</a>.</p>

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

        // Use the synchronous function without delays
        sendEmails($email, $subject, $emailBody);

        // Check and send emails to additional recipients if available
        if (filter_var($emailPk, FILTER_VALIDATE_EMAIL)) {
            sendEmails($emailPk, $subject, $emailBody);
        }

        if (filter_var($directorEmail, FILTER_VALIDATE_EMAIL)) {
            sendEmails($directorEmail, $subject, $emailBody);
        }

        echo "<p>Emails sent successfully.</p>";
    } else {
        echo "<p>Invalid email address: $email</p>";
    }
}

// Call the function to send emails to the specific institution
sendEmailsToInstitution($url, $email, $emailPk, $directorEmail, $subject);
?>

<!DOCTYPE html>
<html lang="en">

<body>
  <h1>Sent Emails to
    <?php echo htmlspecialchars($url); ?>
  </h1>
  <!-- Your additional HTML content here -->
</body>

</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

ensureAdminAuthenticated();
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// Function to send emails
function sendEmailsToSchools()
{
    global $connection; // Use the global connection variable

    // Query to retrieve email addresses and school IDs
    $sql = "SELECT email, id_school FROM schools";
    $result = $connection->query($sql);

    // Check for errors in the query execution
    if (!$result) {
        redirectToErrorPage($connection->error, __FILE__, __LINE__);
    }

    $emailsSent = 0; // Initialize before checking if the button is clicked

    if ($result->num_rows > 0) {
        // Loop through each row
        while ($row = $result->fetch_assoc()) {
            $email = $row['email'];
            $schoolId = $row['id_school'];

            // Notify admin about the database change
            $subject = 'Приглашение к сотрудничеству на 11klassniki.ru - Поделитесь историями вашей школы';
            $Link = "http://$_SERVER[HTTP_HOST]/school/$schoolId";

            $body = "
                <html>
                <head>
                <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
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
                }
                .cta-button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #4caf50;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                }
                </style>
                </head>
                <body>
                    <div class='container'>
                        <h1>Здравствуйте!</h1>
                        <p>Меня зовут Леонид, и я представляю сайт 11klassniki.ru.</p>
                        <p>Мы приглашаем учеников вашей школы, которые проявляют интерес к журналистской деятельности, делиться своими статьями, проводить интервью или предоставлять другой контент, который соответствует нашим рубрикам. Ваши рассказы о собственных опытах, а также интервью с одноклассниками и учителями будут ценным вкладом. Мы уверены, что подобные материалы не только обогатят страницу вашей школы на нашем сайте, но и предоставят ученикам возможность создать портфолио, которое можно будет использовать при поступлении на журналистский факультет.</p>

                        <p>Просим вас обновить данные <a href=\"$Link\">вашего учебного заведения</a>.</p>

                        <p>Процесс обновления прост: справа, рядом с 'Дата последнего обновления', вы увидите красный значок карандаша. Нажмите на этот значок, и вы сможете добавлять или обновлять информацию. Обратите внимание, что для этого необходима предварительная регистрация на сайте.</p>
                        <p>Если у вас возникнут вопросы или потребуется дополнительная помощь, не стесняйтесь обращаться.</p>
                        <p>С уважением,
                        Леонид</p>
                    </div>
                </body>
                </html>
            ";

            // Use mail function to send the email
            sendEmails($email, $subject, $body);

            $emailsSent++;
        }
    }

    return $emailsSent;
}

// Check if the button is clicked
if (isset($_POST['send_emails'])) {
    // Call the function to send emails
    $emailsSent = sendEmailsToSchools();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Emails Page</title>
</head>

<body>
    <h1>Send Emails to Schools</h1>
    <form method="post">
        <button type="submit" name="send_emails">Send Emails</button>
    </form>

    <?php
    // Display feedback on the number of emails sent
    if (isset($emailsSent)) {
        echo "<p>Emails sent successfully to $emailsSent schools.</p>";
    }
    ?>
</body>

</html>
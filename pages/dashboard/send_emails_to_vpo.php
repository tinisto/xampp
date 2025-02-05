<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

ensureAdminAuthenticated();
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// Function to send emails with delays between chunks
function sendEmailsToSchoolsAsync($email, $subject, $body)
{
  // Use mail function to send the email
  sendEmails($email, $subject, $body);

  // Increment the counter for each email sent
  global $emailsSent;
  $emailsSent++;
}

// Function to send emails in smaller batches with delays
function sendEmailsInBatches($emails)
{
  $batchSize = 100; // Adjust the batch size based on your server's limitations
  $delayInSeconds = 2; // Adjust the delay between batches

  $emailChunks = array_chunk($emails, $batchSize);

  foreach ($emailChunks as $chunk) {
    foreach ($chunk as $emailInfo) {
      $email = $emailInfo['email'];
      $universityUrl = $emailInfo['vpo_url'];

      // Notify admin about the database change
      $subject = 'Приглашение к сотрудничеству на 11klassniki.ru';
      $Link = "http://$_SERVER[HTTP_HOST]/vpo/$universityUrl";

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
<p>Мы приглашаем студентов вашего вуза поделиться своими историями о выборе нашего учебного заведения. Мы уверены, что рассказы о личном опыте будут отличной рекламой для нашего сайта и могут стать ценным вкладом в портфолио студентов.</p>
<p>Просим вас обновить данные <a href=\"$Link\">вашего учебного заведения</a>.</p>
<p>Процесс обновления прост: справа, рядом с 'Дата последнего обновления', вы увидите красный значок карандаша. Нажмите на этот значок, и вы сможете добавлять или обновлять информацию. Обратите внимание, что для этого необходима предварительная регистрация на сайте.</p>
<p>Если у вас возникнут вопросы или потребуется дополнительная помощь, не стесняйтесь обращаться.</p>
<p>С уважением, Леонид</p>     
                </div>
            </body>
            </html>
            ";

      // Use the asynchronous function with delays
      sendEmailsToSchoolsAsync($email, $subject, $body);

      sleep($delayInSeconds); // Introduce a delay between emails
    }
  }
}

// Function to retrieve email addresses and school IDs
function getEmailsAndUrls()
{
  global $connection;

  $emailsAndUrls = array();

  $sql = "SELECT email, vpo_url FROM vpo";
  $result = $connection->query($sql);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $emailsAndUrls[] = array(
        'email' => $row['email'],
        'vpo_url' => $row['vpo_url']
      );
    }
  }

  return $emailsAndUrls;
}

// Check if the button is clicked
if (isset($_POST['send_emails'])) {
  $emailsAndUrls = getEmailsAndUrls();

  // Initialize the counter
  $emailsSent = 0;

  // Call the function to send emails in batches with delays
  sendEmailsInBatches($emailsAndUrls);

  // Display feedback on the number of emails sent
  echo "<p>Emails sent successfully with delays between batches. Total emails sent: $emailsSent</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Send Emails Page VPO</title>
  <!-- (Your additional head content here) -->
</head>

<body>
  <h1>Send Emails to VPO</h1>
  <form method="post">
    <button type="submit" name="send_emails">Send Emails to VPO</button>
  </form>

  <!-- (Your additional HTML content here) -->
</body>

</html>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

ensureAdminAuthenticated();

include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/spo-invitation-for-cooperation.php";  // This includes the template

include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

function sendEmailAsync($email, $subject, $body)
{
    echo "Attempting to send email to: $email <br>";

    $result = sendEmails($email, $subject, $body);
    if ($result === true) {
        echo "Email successfully sent to: $email <br>";
        return true;
    } else {
        echo "Failed to send email to: $email <br>";
        return false;
    }
}

function sendEmailsInBatches($emails)
{
    global $subject;
    global $body;

    $batchSize = 100;
    $delayInSeconds = 2;
    $emailsSent = 0;
    $emailsFailed = 0;

    $emailChunks = array_chunk($emails, $batchSize);

    foreach ($emailChunks as $chunk) {
        foreach ($chunk as $emailInfo) {
            $email = $emailInfo['email'];

            echo "Processing email: $email <br>";

            if (sendEmailAsync($email, $subject, $body)) {
                $emailsSent++;
            } else {
                $emailsFailed++;
            }

            sleep($delayInSeconds);
        }
    }

    echo "<p>Total emails sent: $emailsSent</p>";
    echo "<p>Total emails failed: $emailsFailed</p>";
    return ['sent' => $emailsSent, 'failed' => $emailsFailed];
}

function getEmailsAndUrls()
{
    global $connection;

    $emailsAndUrls = [];
    $sql = "SELECT director_email FROM vpo";

    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $emailsAndUrls[] = [
                'email' => $row['director_email'],
            ];
        }
    }

    $stmt->close();

    return $emailsAndUrls;
}

if (isset($_POST['send_emails'])) {
    $emailsAndUrls = getEmailsAndUrls();

    $emailResults = sendEmailsInBatches($emailsAndUrls);

    echo "<p>Emails sent successfully. Total emails sent: {$emailResults['sent']}</p>";
    echo "<p>Emails failed to send. Total emails failed: {$emailResults['failed']}</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Emails Page SPO</title>
</head>

<body>
    <h1>Send Emails to SPO</h1>
    <form method="post">
        <button type="submit" name="send_emails">Send Emails to SPO</button>
    </form>
</body>

</html>
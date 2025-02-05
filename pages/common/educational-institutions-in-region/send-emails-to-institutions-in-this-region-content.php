<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

ensureAdminAuthenticated();

// Check if id_region and type are set in the URL
if (isset($_GET['id_region']) && isset($_GET['type'])) {
    $id_region = $_GET['id_region'];
    $type = $_GET['type'];
}
?>

<?php
ensureAdminAuthenticated();

include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/invitation-for-cooperation.php";

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
            $emailsToSend = [
                $emailInfo['email'],
                $emailInfo['email_pk'],
                $emailInfo['director_email']
            ];

            foreach ($emailsToSend as $email) {
                if (!empty($email)) {
                    echo "Processing email: $email <br>";

                    if (sendEmailAsync($email, $subject, $body)) {
                        $emailsSent++;
                    } else {
                        $emailsFailed++;
                    }

                    sleep($delayInSeconds);
                }
            }
        }
    }

    echo "<p>Total emails sent: $emailsSent</p>";
    echo "<p>Total emails failed: $emailsFailed</p>";
    return ['sent' => $emailsSent, 'failed' => $emailsFailed];
}

function getEmailsAndUrls($type)
{
    global $connection;
    global $id_region;

    $emailsAndUrls = [];
    $sql = "SELECT email, email_pk, director_email FROM $type WHERE id_region = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id_region);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $emailsAndUrls[] = [
                'email' => $row['email'],
                'email_pk' => $row['email_pk'],
                'director_email' => $row['director_email'],
            ];
        }
    }

    $stmt->close();

    return $emailsAndUrls;
}

if (isset($_POST['send_emails'])) {
    $emailsAndUrls = getEmailsAndUrls($type);

    $emailResults = sendEmailsInBatches($emailsAndUrls);

    echo "<p>Emails sent successfully. Total emails sent: {$emailResults['sent']}</p>";
    echo "<p>Emails failed to send. Total emails failed: {$emailResults['failed']}</p>";
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h3>Send Emails</h3>
            <h5 class="mb-3">in This Region - <?php echo htmlspecialchars($id_region); ?></h5>
            <div class="card mb-4">
                <div class="email-body">
                    <?php echo $body; ?>
                </div>
            </div>
            <form method="post">
                <button type="submit" name="send_emails" class="btn btn-primary btn-sm">Send Emails</button>
            </form>
        </div>
    </div>
</div>

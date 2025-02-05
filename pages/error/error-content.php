<div class="col-md-6 text-center mx-auto flex-grow-1 d-flex flex-column justify-content-center align-items-center">
  <h2 class="display-4 mb-4">Произошла ошибка</h2>
  <p class="lead lh-lg">Извините, но что-то пошло не так. <br class="d-md-none">Пожалуйста, попробуйте позже.</p>
  <a href="/" class="link-custom">Перейти на главную страницу</a>
</div>

<?php
// Include the email functions
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

// Define the subject and body of the email
$subject = "Error Occurred on the Website";

// Start the HTML email body
$body = "<html>
  <head>
    <style>
      body { font-family: Arial, sans-serif; line-height: 1.6; }
      .container { padding: 20px; background-color: #f8f9fa; border-radius: 5px; }
      h2 { color: #dc3545; }
      p { font-size: 16px; color: #333; }
      .details { margin-top: 20px; }
      .details th { text-align: left; padding-right: 15px; }
      .details td { padding: 5px 0; }
      .footer { margin-top: 20px; padding-top: 10px; font-size: 12px; color: #6c757d; }
      .important { font-weight: bold; color: #d9534f; }
    </style>
  </head>
  <body>
    <div class='container'>
      <h2>Error Notification</h2>
      <p>Dear Admin,</p>
      <p>An error has occurred on the website. Below are the details of the error:</p>

      <table class='details'>
<tr><th>URL:</th><td>" . htmlspecialchars(urldecode($_SERVER['REQUEST_URI'])) . "</td></tr>
        <tr><th>Referer:</th><td>" . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A') . "</td></tr>
        <tr><th>User Agent:</th><td>" . $_SERVER['HTTP_USER_AGENT'] . "</td></tr>
        <tr><th>IP Address:</th><td>" . $_SERVER['REMOTE_ADDR'] . "</td></tr>
      </table>";

// Get the error details from the query string
// Get the error details from the query string
if (isset($_GET['error'])) {
  $decodedError = urldecode($_GET['error']);
  $errorDetails = json_decode($decodedError, true);

  if ($errorDetails && is_array($errorDetails)) {
    $errorMessage = htmlspecialchars($errorDetails['message'] ?? 'No message provided');
    $errorFile = htmlspecialchars($errorDetails['file'] ?? 'No file provided');
    $errorLine = htmlspecialchars($errorDetails['line'] ?? 'No line number provided');

    $body .= "
        <h3 class='important'>Error Details:</h3>
        <table class='details'>
          <tr><th>Error Message:</th><td>" . $errorMessage . "</td></tr>
          <tr><th>File:</th><td>" . $errorFile . "</td></tr>
          <tr><th>Line:</th><td>" . $errorLine . "</td></tr>
        </table>";
  } else {
    $body .= "
        <p>Invalid error details format received: <code>" . htmlspecialchars($decodedError) . "</code></p>";
  }
} else {
  $body .= "
        <p>No specific error details available.</p>";
}


// Footer of the email
$body .= "
      <div class='footer'>
        <p>Please check the logs for further investigation.</p>
      </div>
    </div>
  </body>
</html>";

// Send the email to the admin
sendToAdmin($subject, $body);
?>
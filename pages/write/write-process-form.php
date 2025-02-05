<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';

// Include your database connection code here if not already included
include 'write-functions.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Sanitize and retrieve form data
  $message = isset($_POST["message"]) ? $_POST["message"] : '';

  // Get user email from session (assuming the user is logged in)
  $userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

  // Get user ID from session if logged in
  $userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

  // Ensure that the message and user email are provided before proceeding
  if (!empty($userEmail) && !empty($message)) {

    // Insert message into the database
    insertMessage($connection, $userEmail, $message, $userID);

    // Send an email notification to the admin about the new message
    $subject = 'New Message Received';
    $body = "A new message has been written.<br><br>Message Text: " . nl2br(htmlspecialchars($message));
    sendToAdmin($subject, $body);

    // Redirect to thank you page after submission
    header("Location: /thank-you");
    exit(); // Always call exit() after redirect
  } else {
    // Handle missing fields
    echo "Please fill in all required fields.";
  }
}

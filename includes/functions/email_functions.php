<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config/constants.php';

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Function to send email to admin with a default email if not provided
function sendToAdmin($subject, $body, $adminEmail = null)
{
  // If no $adminEmail is provided, use the constant ADMIN_EMAIL
  $adminEmail = $adminEmail ?? ADMIN_EMAIL;

  // Ensure the admin email is defined
  if (empty($adminEmail)) {
    // Handle the case where admin_email is not defined
    echo "Error: Admin email is not defined.";
    return;
  }

  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}



function sendToUser($email, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}




// sendEmailToAdminAboutNewMessage ______________________________________________________________________________________________________________________________
function sendEmailToAdminAboutNewMessage($adminEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

// sendEmailToAdminAboutNewComment ______________________________________________________________________________________________________________________________
function sendEmailToAdminAboutNewComment($adminEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}


// Function to send activation email ______________________________________________________________________________________________________________________________
function sendActivationEmail($recipientEmail, $activationLink, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($recipientEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}


// sendPasswordChangedEmail ______________________________________________________________________________________________________________________________
function sendPasswordChangedEmail($recipientEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($recipientEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}


// Function to send password reset email ______________________________________________________________________________________________________________________________
function sendPasswordResetEmail($recipientEmail, $resetLink, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($recipientEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

// Function to send password reset email ______________________________________________________________________________________________________________________________
function suspendUser($recipientEmail, $resetLink, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($recipientEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}


// configureMailer ______________________________________________________________________________________________________________________________

function configureMailer($mail)
{
  // Set the character encoding
  $mail->CharSet = 'UTF-8';

  // Server settings
  $mail->isSMTP();
  $mail->Host = SMTP_HOST;
  $mail->SMTPAuth = true;
  $mail->Username = SMTP_USERNAME;
  $mail->Password = SMTP_PASSWORD;
  $mail->SMTPSecure = SMTP_SECURITY;
  $mail->Port = SMTP_PORT;

  // Recipients
  $mail->setFrom(ADMIN_EMAIL, '11klassniki.ru');
}



function notifyAdminOnNewUser($adminEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function notifyAdminOnDatabaseChange($adminEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function updatedVPO($adminEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function toAdmin($adminEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function sendEmails($recipientEmail, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($recipientEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
    return true; // Email sent successfully
  } catch (Exception $e) {
    error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    return false; // Email failed to send
  }
}

function commonEmail($Email, $subject, $body)
{
  $mail = new PHPMailer(true);
  configureMailer($mail);
  try {
    $mail->addAddress($Email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

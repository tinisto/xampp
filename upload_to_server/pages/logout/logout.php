<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/session_util.php';

// Unset specific session variables
unset($_SESSION['email']);
unset($_SESSION['role']);
unset($_SESSION['firstname']);
unset($_SESSION['lastname']);
unset($_SESSION['user_id']);
unset($_SESSION['avatar']);

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the index page after logout
header("Location: /");
exit();

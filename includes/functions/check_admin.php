<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/auth.php";
ensureAdminAuthenticated();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
session_start();
$_SESSION = array();
session_destroy();
setcookie('remember_user', '', time() - 3600, '/', '', true, true);
header('Location: /');
exit();
?>
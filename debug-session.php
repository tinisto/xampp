<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

echo "<h3>Session Debug</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>User from Database</h3>";
if (isset($_SESSION['user_id'])) {
    $db = Database::getInstance();
    $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} else {
    echo "No user_id in session";
}
?>
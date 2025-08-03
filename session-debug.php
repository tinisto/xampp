<?php
session_start();

echo "<h2>Session Debug</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . "\n";
echo "Session data:\n";
print_r($_SESSION);
echo "\n\nCookies:\n";
print_r($_COOKIE);
echo "</pre>";

echo "<h3>Session Variables:</h3>";
echo "<ul>";
echo "<li>user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "</li>";
echo "<li>email: " . (isset($_SESSION['email']) ? $_SESSION['email'] : 'NOT SET') . "</li>";
echo "<li>role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "</li>";
echo "</ul>";

echo "<p><a href='/account'>Go to Account</a> | <a href='/login'>Go to Login</a></p>";
?>
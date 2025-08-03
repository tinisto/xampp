<?php
session_start();

echo "<h2>Login Redirect Test</h2>";

echo "<p>Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "</p>";
echo "<p>Is empty check: " . (empty($_SESSION['user_id']) ? 'EMPTY' : 'NOT EMPTY') . "</p>";
echo "<p>Isset check: " . (isset($_SESSION['user_id']) ? 'SET' : 'NOT SET') . "</p>";

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ Redirect condition MET - should redirect</p>";
    echo "<p>Redirecting in 3 seconds...</p>";
    echo "<script>setTimeout(function(){ window.location.href = '/account'; }, 3000);</script>";
} else {
    echo "<p style='color: red;'>✗ Redirect condition NOT MET - would show login form</p>";
}

echo "<hr>";
echo "<p><a href='/login'>Test actual login page</a></p>";
echo "<p><a href='/account'>Go to account</a></p>";
?>
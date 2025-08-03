<?php
// Direct output without any includes to test
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Post Test</title>
</head>
<body>
    <h1>Direct Post Page</h1>
    <p>This is a direct test. URL parameter: <?= htmlspecialchars($_GET['url_post'] ?? 'none') ?></p>
    <p>Request URI: <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></p>
    <p>Script Name: <?= htmlspecialchars($_SERVER['SCRIPT_NAME']) ?></p>
    <p>PHP Self: <?= htmlspecialchars($_SERVER['PHP_SELF']) ?></p>
</body>
</html>
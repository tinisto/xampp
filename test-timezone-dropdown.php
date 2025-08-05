<?php
session_start();
require_once 'comments/timezone-handler.php';

$currentTimezone = getUserTimezone();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Timezone Dropdown</title>
</head>
<body>
    <h1>Test Timezone Dropdown</h1>
    
    <p>Current timezone in session: <?= $currentTimezone ?></p>
    
    <p>Dropdown test:</p>
    <?= getTimezoneSelector() ?>
    
    <script>
    function updateTimezone(timezone) {
        if (!timezone) return;
        
        fetch('/comments/timezone-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'timezone=' + encodeURIComponent(timezone)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Timezone set to: ' + data.timezone);
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>
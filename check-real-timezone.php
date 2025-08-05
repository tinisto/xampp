<?php
session_start();
require_once 'comments/timezone-handler.php';

// Get various time information
$serverTimezone = date_default_timezone_get();
$userTimezone = getUserTimezone();
$serverTime = new DateTime();
$serverTimeStr = $serverTime->format('Y-m-d H:i:s');

// Get UTC time
$utcTime = new DateTime('now', new DateTimeZone('UTC'));
$utcTimeStr = $utcTime->format('Y-m-d H:i:s');

// Calculate offset
$serverOffset = $serverTime->getOffset() / 3600;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Timezone Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .highlight { background: #fffacd; padding: 10px; margin: 10px 0; border-radius: 5px; }
        #browserInfo { background: #e6f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌍 Timezone Diagnostic</h1>
        
        <h2>Server Information:</h2>
        <table>
            <tr><th>Server Timezone</th><td><?= $serverTimezone ?></td></tr>
            <tr><th>Server Time</th><td><?= $serverTimeStr ?></td></tr>
            <tr><th>UTC Time</th><td><?= $utcTimeStr ?></td></tr>
            <tr><th>Server UTC Offset</th><td>UTC<?= sprintf('%+d', $serverOffset) ?></td></tr>
        </table>
        
        <h2>Your Session Information:</h2>
        <table>
            <tr><th>Stored Timezone</th><td id="storedTimezone"><?= $userTimezone ?></td></tr>
            <tr><th>Session ID</th><td><?= substr(session_id(), 0, 10) ?>...</td></tr>
        </table>
        
        <div id="browserInfo">
            <h2>Browser Information:</h2>
            <p>Detecting your browser timezone...</p>
        </div>
        
        <div class="highlight">
            <h3>What's your actual location?</h3>
            <p>Please tell me: Are you actually in Moscow timezone, or somewhere else?</p>
            <p>Your browser is reporting: <strong id="browserTz">detecting...</strong></p>
            <p>UTC offset: <strong id="utcOffset">detecting...</strong></p>
        </div>
        
        <button onclick="forceDetect()">🔄 Force Re-detect Timezone</button>
    </div>
    
    <script>
    // Get detailed browser timezone info
    function getBrowserTimezoneInfo() {
        const now = new Date();
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const offset = -now.getTimezoneOffset() / 60;
        
        // Get locale info
        const locale = navigator.language || navigator.userLanguage;
        
        // Try to get more specific location info
        const formatter = new Intl.DateTimeFormat('en-US', {
            timeZone: timezone,
            timeZoneName: 'long'
        });
        const parts = formatter.formatToParts(now);
        const timeZoneName = parts.find(part => part.type === 'timeZoneName')?.value || 'Unknown';
        
        return {
            timezone: timezone,
            offset: offset,
            offsetString: `UTC${offset >= 0 ? '+' : ''}${offset}`,
            locale: locale,
            timeZoneName: timeZoneName,
            isDST: now.getTimezoneOffset() < new Date(now.getFullYear(), 0, 1).getTimezoneOffset()
        };
    }
    
    // Display browser info
    function displayBrowserInfo() {
        const info = getBrowserTimezoneInfo();
        
        document.getElementById('browserInfo').innerHTML = `
            <h2>Browser Information:</h2>
            <table>
                <tr><th>Browser Timezone</th><td>${info.timezone}</td></tr>
                <tr><th>UTC Offset</th><td>${info.offsetString}</td></tr>
                <tr><th>Timezone Name</th><td>${info.timeZoneName}</td></tr>
                <tr><th>Browser Locale</th><td>${info.locale}</td></tr>
                <tr><th>Daylight Saving</th><td>${info.isDST ? 'Yes' : 'No'}</td></tr>
                <tr><th>Current Browser Time</th><td>${new Date().toLocaleString()}</td></tr>
            </table>
        `;
        
        document.getElementById('browserTz').textContent = info.timezone;
        document.getElementById('utcOffset').textContent = info.offsetString;
        
        // Check if browser timezone matches stored timezone
        const storedTz = document.getElementById('storedTimezone').textContent;
        if (storedTz !== info.timezone) {
            document.getElementById('browserInfo').innerHTML += `
                <div style="background: #ffeeee; padding: 10px; margin-top: 10px; border-radius: 5px;">
                    <strong>⚠️ Mismatch detected!</strong><br>
                    Stored: ${storedTz}<br>
                    Browser: ${info.timezone}
                </div>
            `;
        }
    }
    
    // Force re-detection
    function forceDetect() {
        sessionStorage.removeItem('timezone_detected');
        const info = getBrowserTimezoneInfo();
        
        fetch('/comments/timezone-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'timezone=' + encodeURIComponent(info.timezone)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Timezone updated to: ' + data.timezone);
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Run on load
    displayBrowserInfo();
    </script>
</body>
</html>
<?php
session_start();
require_once 'database/db_connections.php';
require_once 'comments/timezone-handler.php';

$userTimezone = getUserTimezone();
$serverTimezone = date_default_timezone_get();

echo "<h1>Debug Comment Time Display</h1>";
echo "<p>Your timezone: <strong>$userTimezone</strong></p>";
echo "<p>Server timezone: <strong>$serverTimezone</strong></p>";

// Get a recent comment
$query = "SELECT id, entity_id, entity_type, date, comment_text 
          FROM comments 
          WHERE entity_type = 'post' 
          ORDER BY date DESC 
          LIMIT 1";
$result = mysqli_query($connection, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo "<h2>Most Recent Comment:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>Comment ID</td><td>{$row['id']}</td></tr>";
    echo "<tr><td>Raw Date from DB</td><td>{$row['date']}</td></tr>";
    echo "<tr><td>Comment Preview</td><td>" . substr($row['comment_text'], 0, 50) . "...</td></tr>";
    echo "</table>";
    
    echo "<h3>Time Conversions:</h3>";
    
    // Test the conversion
    $rawDate = $row['date'];
    
    // Server time
    $serverTime = new DateTime($rawDate, new DateTimeZone($serverTimezone));
    echo "<p>Server time interpretation: " . $serverTime->format('Y-m-d H:i:s T') . "</p>";
    
    // User timezone
    $userTime = convertToUserTimezone($rawDate, $userTimezone);
    echo "<p>User time after conversion: " . $userTime->format('Y-m-d H:i:s T') . "</p>";
    
    // Current time in both zones
    $nowServer = new DateTime('now', new DateTimeZone($serverTimezone));
    $nowUser = new DateTime('now', new DateTimeZone($userTimezone));
    
    echo "<p>Current server time: " . $nowServer->format('Y-m-d H:i:s T') . "</p>";
    echo "<p>Current user time: " . $nowUser->format('Y-m-d H:i:s T') . "</p>";
    
    // Time ago calculation
    echo "<h3>Time Ago Calculation:</h3>";
    
    // Using the function
    $timeAgo = formatTimeAgoUserTZ($rawDate, $userTimezone);
    echo "<p>formatTimeAgoUserTZ result: <strong>$timeAgo</strong></p>";
    
    // Manual calculation
    $diff = $nowUser->diff($userTime);
    echo "<p>Time difference details:</p>";
    echo "<ul>";
    echo "<li>Days: {$diff->days}</li>";
    echo "<li>Hours: {$diff->h}</li>";
    echo "<li>Minutes: {$diff->i}</li>";
    echo "<li>Seconds: {$diff->s}</li>";
    echo "<li>Total hours: " . (($diff->days * 24) + $diff->h) . "</li>";
    echo "</ul>";
    
    // Check if timezone offset is the issue
    $serverOffset = $serverTime->getOffset() / 3600;
    $userOffset = $userTime->getOffset() / 3600;
    echo "<p>Server UTC offset: " . sprintf('%+d', $serverOffset) . " hours</p>";
    echo "<p>User UTC offset: " . sprintf('%+d', $userOffset) . " hours</p>";
    echo "<p>Difference: " . ($userOffset - $serverOffset) . " hours</p>";
}

echo "<hr>";
echo "<p><a href='/post/kogda-ege-ostalis-pozadi'>Back to post with comments</a></p>";
?>
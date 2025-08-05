<?php
require_once 'database/db_connections.php';

echo "<h1>MySQL Timezone Check</h1>";

// Check MySQL timezone settings
$query = "SELECT 
    @@global.time_zone as global_tz,
    @@session.time_zone as session_tz,
    NOW() as mysql_now,
    UTC_TIMESTAMP() as utc_now,
    TIMEDIFF(NOW(), UTC_TIMESTAMP()) as offset_from_utc";

$result = mysqli_query($connection, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo "<table border='1'>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    echo "<tr><td>Global Timezone</td><td>{$row['global_tz']}</td></tr>";
    echo "<tr><td>Session Timezone</td><td>{$row['session_tz']}</td></tr>";
    echo "<tr><td>MySQL NOW()</td><td>{$row['mysql_now']}</td></tr>";
    echo "<tr><td>UTC_TIMESTAMP()</td><td>{$row['utc_now']}</td></tr>";
    echo "<tr><td>Offset from UTC</td><td>{$row['offset_from_utc']}</td></tr>";
    echo "</table>";
    
    // Calculate offset in hours
    $offset_parts = explode(':', $row['offset_from_utc']);
    $offset_hours = intval($offset_parts[0]);
    $offset_minutes = intval($offset_parts[1]);
    $total_offset = $offset_hours + ($offset_minutes / 60);
    
    echo "<p>MySQL is running at UTC" . sprintf('%+.1f', $total_offset) . "</p>";
    
    // PHP timezone
    echo "<h2>PHP Timezone:</h2>";
    echo "<p>Default timezone: " . date_default_timezone_get() . "</p>";
    echo "<p>PHP date(): " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>PHP gmdate(): " . gmdate('Y-m-d H:i:s') . "</p>";
    
    // Recommendation
    echo "<h2>Recommendation:</h2>";
    if ($row['global_tz'] == 'SYSTEM' || $row['session_tz'] == 'SYSTEM') {
        echo "<p style='color: orange;'>⚠️ MySQL is using SYSTEM timezone, which can be unpredictable.</p>";
        echo "<p>The actual timezone depends on the server's system settings.</p>";
    }
    
    if (abs($total_offset) < 0.1) {
        echo "<p style='color: green;'>✅ MySQL appears to be using UTC, which is good!</p>";
    } else {
        echo "<p style='color: red;'>❌ MySQL is NOT using UTC (offset: " . sprintf('%+.1f', $total_offset) . " hours)</p>";
    }
}
?>
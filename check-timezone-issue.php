<?php
require_once 'database/db_connections.php';

// Check MySQL timezone settings
echo "<h2>Timezone Debugging</h2>";

// 1. Check MySQL server timezone
$result = $connection->query("SELECT @@global.time_zone, @@session.time_zone, NOW() as server_time");
$row = $result->fetch_assoc();
echo "<h3>MySQL Settings:</h3>";
echo "Global timezone: " . $row['@@global.time_zone'] . "<br>";
echo "Session timezone: " . $row['@@session.time_zone'] . "<br>";
echo "Server NOW(): " . $row['server_time'] . "<br><br>";

// 2. Check PHP timezone
echo "<h3>PHP Settings:</h3>";
echo "PHP timezone: " . date_default_timezone_get() . "<br>";
echo "PHP date: " . date('Y-m-d H:i:s') . "<br><br>";

// 3. Check Moscow time
echo "<h3>Moscow Time:</h3>";
$moscowTime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
echo "Moscow time: " . $moscowTime->format('Y-m-d H:i:s') . "<br><br>";

// 4. Check recent comments
echo "<h3>Recent Comments (last 5):</h3>";
$query = "SELECT id, date, comment_text FROM comments ORDER BY date DESC LIMIT 5";
$result = $connection->query($query);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Date in DB</th><th>Comment</th><th>Time Ago (Moscow)</th></tr>";

while ($comment = $result->fetch_assoc()) {
    // Calculate time ago using Moscow timezone
    $moscowNow = new DateTime('now', new DateTimeZone('Europe/Moscow'));
    $commentTime = new DateTime($comment['date'], new DateTimeZone('UTC'));
    $commentTime->setTimezone(new DateTimeZone('Europe/Moscow'));
    
    $diff = $moscowNow->getTimestamp() - $commentTime->getTimestamp();
    
    if ($diff < 60) $timeAgo = 'только что';
    elseif ($diff < 3600) $timeAgo = floor($diff/60) . ' мин назад';
    elseif ($diff < 86400) $timeAgo = floor($diff/3600) . ' ч назад';
    else $timeAgo = floor($diff/86400) . ' дн назад';
    
    echo "<tr>";
    echo "<td>" . $comment['id'] . "</td>";
    echo "<td>" . $comment['date'] . "</td>";
    echo "<td>" . substr($comment['comment_text'], 0, 50) . "...</td>";
    echo "<td>" . $timeAgo . "</td>";
    echo "</tr>";
}
echo "</table><br>";

// 5. Test comment insertion
echo "<h3>Test Comment Insertion:</h3>";
echo "<form method='POST'>";
echo "<button type='submit' name='test_insert'>Insert Test Comment</button>";
echo "</form>";

if (isset($_POST['test_insert'])) {
    $testText = "Test comment inserted at " . date('Y-m-d H:i:s');
    $stmt = $connection->prepare("INSERT INTO comments (entity_id, user_id, comment_text, parent_id, entity_type, date) VALUES (1, 1, ?, 0, 'post', NOW())");
    $stmt->bind_param("s", $testText);
    
    if ($stmt->execute()) {
        $newId = $connection->insert_id;
        echo "Inserted comment ID: " . $newId . "<br>";
        
        // Immediately fetch it back
        $checkStmt = $connection->prepare("SELECT date FROM comments WHERE id = ?");
        $checkStmt->bind_param("i", $newId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $newComment = $result->fetch_assoc();
        
        echo "Stored date: " . $newComment['date'] . "<br>";
        echo "<script>setTimeout(() => location.reload(), 1000);</script>";
    }
}

$connection->close();
?>
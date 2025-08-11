<?php
// Check posts table structure
$connection = new mysqli('127.0.0.1', 'root', 'root', '11klassniki_claude');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Show table structure
$query = "DESCRIBE posts";
$result = mysqli_query($connection, $query);

echo "<h2>Posts Table Structure:</h2>";
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Error: " . mysqli_error($connection) . "</td></tr>";
}
echo "</table>";

mysqli_close($connection);
?>
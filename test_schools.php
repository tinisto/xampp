<?php
// Direct connection without env loader
$connection = mysqli_connect(
    '11klassnikiru67871.ipagemysql.com',
    '11klone_user',
    'K8HqqBV3hTf4mha',
    '11klassniki_ru'
);

if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Check if school_url column exists
$result = mysqli_query($connection, "SHOW COLUMNS FROM schools LIKE 'school_url'");
if (mysqli_num_rows($result) > 0) {
    echo "school_url column exists\n\n";
} else {
    echo "school_url column DOES NOT exist\n\n";
}

// Show all columns
echo "Schools table columns:\n";
$result = mysqli_query($connection, "SHOW COLUMNS FROM schools");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

// Check sample data
echo "\n\nSample schools:\n";
$result = mysqli_query($connection, "SELECT * FROM schools WHERE id_region = 1 LIMIT 3");
while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id_school'] . " | Name: " . $row['school_name'] . "\n";
    // Show all fields
    foreach ($row as $key => $value) {
        if (strpos($key, 'url') !== false || strpos($key, 'URL') !== false) {
            echo "  -> $key: $value\n";
        }
    }
}

mysqli_close($connection);
?>
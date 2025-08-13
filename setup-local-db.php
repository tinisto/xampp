<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Local Database Setup</h1>";

// Try to connect to MySQL without database
$mysqli = new mysqli('127.0.0.1', 'root', 'root');

if ($mysqli->connect_error) {
    // Try without password
    $mysqli = new mysqli('127.0.0.1', 'root', '');
    
    if ($mysqli->connect_error) {
        die("Cannot connect to MySQL. Make sure XAMPP MySQL is running.<br>Error: " . $mysqli->connect_error);
    }
}

echo "<p style='color: green;'>✓ Connected to MySQL</p>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS `11klassniki_claude` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($mysqli->query($sql)) {
    echo "<p style='color: green;'>✓ Database '11klassniki_claude' created/verified</p>";
} else {
    echo "<p style='color: red;'>Error creating database: " . $mysqli->error . "</p>";
}

// Select database
$mysqli->select_db('11klassniki_claude');

// Create categories table
$sql = "CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `url_category` varchar(255) NOT NULL,
    `title_category` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($mysqli->query($sql)) {
    echo "<p style='color: green;'>✓ Categories table created/verified</p>";
    
    // Insert sample categories
    $categories = [
        ['education-news', 'Новости образования'],
        ['ege-oge', 'ЕГЭ и ОГЭ'],
        ['universities', 'Университеты'],
        ['colleges', 'Колледжи'],
        ['schools', 'Школы'],
        ['olympiads', 'Олимпиады'],
        ['exams', 'Экзамены'],
        ['admissions', 'Поступление']
    ];
    
    foreach ($categories as $cat) {
        $stmt = $mysqli->prepare("INSERT IGNORE INTO categories (url_category, title_category) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat[0], $cat[1]);
        $stmt->execute();
    }
    echo "<p style='color: green;'>✓ Sample categories added</p>";
}

// Create other necessary tables
$tables = [
    'regions' => "CREATE TABLE IF NOT EXISTS `regions` (
        `region_id` int(11) NOT NULL AUTO_INCREMENT,
        `region_name` varchar(255) NOT NULL,
        `region_name_en` varchar(255) NOT NULL,
        PRIMARY KEY (`region_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'schools' => "CREATE TABLE IF NOT EXISTS `schools` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `region_id` int(11) NOT NULL,
        `url_slug` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'universities' => "CREATE TABLE IF NOT EXISTS `universities` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `region_id` int(11) NOT NULL,
        `url_slug` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'colleges' => "CREATE TABLE IF NOT EXISTS `colleges` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `region_id` int(11) NOT NULL,
        `url_slug` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $name => $sql) {
    if ($mysqli->query($sql)) {
        echo "<p style='color: green;'>✓ Table '$name' created/verified</p>";
    }
}

echo "<h2>Setup Complete!</h2>";
echo "<p>Now you can visit: <a href='/index-working-local.php'>View the site</a></p>";

$mysqli->close();
?>
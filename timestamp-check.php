<?php
echo "Current timestamp: " . date('Y-m-d H:i:s') . "<br>";
echo "File last modified: " . date('Y-m-d H:i:s', filemtime(__FILE__)) . "<br>";
echo "Random number: " . rand(1000, 9999) . "<br>";
echo "Git commit check: ";

// Try to get the latest commit
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.git/HEAD')) {
    $head = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/.git/HEAD');
    echo trim($head) . "<br>";
} else {
    echo "No .git directory found<br>";
}

// Check news.php modification time
$newsFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($newsFile)) {
    echo "news.php last modified: " . date('Y-m-d H:i:s', filemtime($newsFile)) . "<br>";
} else {
    echo "news.php not found<br>";
}
?>
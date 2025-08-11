<?php
// Quick check for sharing buttons
echo "<h1>Sharing Buttons Check</h1>";

// Check if post-single.php contains WhatsApp
$content = file_get_contents('post-single.php');

if (strpos($content, 'whatsapp') !== false || strpos($content, 'WhatsApp') !== false) {
    echo "<p>❌ WhatsApp sharing still exists in post-single.php</p>";
} else {
    echo "<p>✅ WhatsApp sharing has been removed from post-single.php</p>";
}

// Count sharing options
preg_match_all('/<a href="[^"]*share[^"]*"/', $content, $matches);
echo "<p>Found " . count($matches[0]) . " sharing links in post-single.php</p>";

echo "<p>The sharing options now include:</p>";
echo "<ul>";
if (strpos($content, 'vk.com') !== false) echo "<li>VKontakte</li>";
if (strpos($content, 't.me') !== false) echo "<li>Telegram</li>";
if (strpos($content, 'facebook.com') !== false) echo "<li>Facebook</li>";
if (strpos($content, 'twitter.com') !== false) echo "<li>Twitter</li>";
echo "</ul>";

echo "<p><a href='/'>Go to homepage</a></p>";
?>
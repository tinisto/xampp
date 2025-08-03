<?php
echo "<h2>Verify Final Deployment</h2>";

$loginFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/login/login_process_simple.php';

echo "<h3>Current login_process_simple.php:</h3>";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($loginFile)) . "<br>";
echo "Size: " . filesize($loginFile) . " bytes<br>";

$content = file_get_contents($loginFile);

// Check for debug logging (should NOT be present in clean version)
if (strpos($content, 'debug_login.txt') !== false) {
    echo "<span style='color: orange;'>⚠️ Still contains debug logging</span><br>";
} else {
    echo "<span style='color: green;'>✓ Clean version (no debug logging)</span><br>";
}

// Check for redirect logic
if (strpos($content, 'strpos($redirect, \'/\') === 0') !== false) {
    echo "<span style='color: green;'>✓ Contains redirect logic</span><br>";
} else {
    echo "<span style='color: red;'>✗ Missing redirect logic</span><br>";
}

echo "<h3>Test redirect logic directly:</h3>";
$testRedirect = "/write";
$check1 = strpos($testRedirect, '/') === 0;
$check2 = strpos($testRedirect, '//') !== 0;
$passes = $check1 && $check2;

echo "Test value: '$testRedirect'<br>";
echo "Security check: " . ($passes ? '<span style="color: green;">PASS</span>' : '<span style="color: red;">FAIL</span>') . "<br>";

echo "<h3>Final test with real login:</h3>";
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div style="border: 3px solid #28a745; padding: 20px; margin: 20px 0; background: #f8fff8;">
    <h4>🎯 FINAL PRODUCTION TEST</h4>
    <p><strong>This uses the actual deployed login process file</strong></p>
    
    <form method="post" action="/pages/login/login_process_simple.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="redirect" value="/write">
        
        <p>Redirect parameter: <code>/write</code></p>
        
        <label>Email:</label><br>
        <input type="email" name="email" required style="width: 300px; padding: 8px; margin: 5px 0;"><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required style="width: 300px; padding: 8px; margin: 5px 0;"><br><br>
        
        <button type="submit" style="padding: 12px 25px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px;">
            🚀 FINAL TEST - SHOULD REDIRECT TO /write
        </button>
    </form>
</div>

<?php
echo "<h3>File content check:</h3>";
echo "<div style='max-height: 300px; overflow-y: auto; background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
echo "<pre>" . htmlspecialchars(substr($content, 0, 2000)) . "</pre>";
if (strlen($content) > 2000) {
    echo "<p><em>... (truncated)</em></p>";
}
echo "</div>";
?>
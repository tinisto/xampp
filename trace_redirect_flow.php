<?php
session_start();

echo "<h2>Trace Complete Redirect Flow</h2>";

echo "<h3>Step 1: Simulate Write Page</h3>";
echo "Current URI: /write<br>";
$loginUrl = "/login?redirect=" . urlencode("/write");
echo "Login URL: <a href='$loginUrl' target='_blank'>$loginUrl</a><br><br>";

echo "<h3>Step 2: Check Login Page with Redirect</h3>";
if (isset($_GET['redirect'])) {
    echo "✓ Redirect parameter received: " . htmlspecialchars($_GET['redirect']) . "<br>";
} else {
    echo "✗ No redirect parameter received<br>";
    echo "Try clicking this link: <a href='?redirect=/write'>Add redirect parameter</a><br>";
}
echo "<br>";

echo "<h3>Step 3: Real Login Form (with actual CSRF)</h3>";
// Generate real CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$redirectValue = $_GET['redirect'] ?? '/write';
?>

<form method="post" action="/pages/login/login_process_simple.php" style="border: 2px solid #28a745; padding: 20px; margin: 20px 0; background: #f8fff8;">
    <h4>🔥 REAL LOGIN FORM - Use Your Actual Credentials</h4>
    
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectValue) ?>">
    
    <p><strong>Redirect will be:</strong> <?= htmlspecialchars($redirectValue) ?></p>
    
    <label>Email:</label><br>
    <input type="email" name="email" required style="width: 300px; padding: 8px; margin: 5px 0;"><br>
    
    <label>Password:</label><br>
    <input type="password" name="password" required style="width: 300px; padding: 8px; margin: 5px 0;"><br><br>
    
    <button type="submit" style="padding: 12px 25px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px;">
        🚀 LOGIN AND TEST REDIRECT
    </button>
</form>

<?php
echo "<h3>Step 4: Debug Info</h3>";
echo "Session CSRF token: " . ($_SESSION['csrf_token'] ?? 'not set') . "<br>";
echo "Current page: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Server time: " . date('Y-m-d H:i:s') . "<br>";

if ($redirectValue) {
    echo "<br><strong>Testing redirect validation:</strong><br>";
    echo "Value: '$redirectValue'<br>";
    echo "Starts with /: " . (strpos($redirectValue, '/') === 0 ? 'YES' : 'NO') . "<br>";
    echo "Starts with //: " . (strpos($redirectValue, '//') === 0 ? 'YES' : 'NO') . "<br>";
    echo "Security check: " . ((strpos($redirectValue, '/') === 0 && strpos($redirectValue, '//') !== 0) ? 'PASS' : 'FAIL') . "<br>";
}
?>
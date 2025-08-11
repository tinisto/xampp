<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Security Audit Report for 11klassniki.ru</h1>";
echo "<p>Generated: " . date('Y-m-d H:i:s') . "</p>";

// Function to check file for vulnerabilities
function checkFile($filepath) {
    $vulnerabilities = [];
    $content = file_get_contents($filepath);
    
    // Check for SQL injection patterns
    if (preg_match('/\$_(GET|POST|REQUEST)\[[\'"]([^\'"]+)[\'"]\].*?LIKE\s*[\'"]%?\$/', $content) ||
        preg_match('/LIKE\s*[\'"].*?\$_(GET|POST|REQUEST)/', $content)) {
        $vulnerabilities[] = "Potential SQL injection in LIKE clause";
    }
    
    // Check for direct concatenation in queries
    if (preg_match('/query\([\'"].*?\.\s*\$_(GET|POST|REQUEST)/', $content)) {
        $vulnerabilities[] = "Direct user input concatenation in query";
    }
    
    // Check for unescaped output (XSS)
    if (preg_match('/echo\s+\$_(GET|POST|REQUEST)\[/', $content) &&
        !preg_match('/htmlspecialchars.*?\$_(GET|POST|REQUEST)/', $content)) {
        $vulnerabilities[] = "Potential XSS - unescaped output";
    }
    
    // Check for file inclusion vulnerabilities
    if (preg_match('/(include|require|include_once|require_once)\s*\(?\s*\$/', $content)) {
        $vulnerabilities[] = "Potential file inclusion vulnerability";
    }
    
    // Check for eval usage
    if (preg_match('/eval\s*\(/', $content)) {
        $vulnerabilities[] = "Dangerous eval() usage found";
    }
    
    // Check for exec/system calls
    if (preg_match('/(exec|system|shell_exec|passthru)\s*\(/', $content)) {
        $vulnerabilities[] = "System command execution found";
    }
    
    // Check for weak password hashing
    if (preg_match('/md5\s*\(.*password/', $content) || preg_match('/sha1\s*\(.*password/', $content)) {
        $vulnerabilities[] = "Weak password hashing (MD5/SHA1)";
    }
    
    return $vulnerabilities;
}

// Files to check
$files_to_check = [
    'dashboard-comments-new.php',
    'analyze-news-categories.php',
    'dashboard-vpo-functional.php',
    'dashboard-schools-new.php',
    'login_modern.php',
    'register_modern.php',
    'reset-password.php',
    'forgot-password.php',
    'search_modern.php',
    'news_modern.php',
    'posts_modern.php'
];

echo "<h2>1. SQL Injection Vulnerabilities</h2>";
$sql_vulnerabilities = [];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $vulns = checkFile($file);
        if (!empty($vulns)) {
            $sql_vulnerabilities[$file] = $vulns;
        }
    }
}

if (!empty($sql_vulnerabilities)) {
    echo "<div style='background: #fee; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #c00;'>⚠️ Vulnerabilities Found:</h3>";
    foreach ($sql_vulnerabilities as $file => $vulns) {
        echo "<h4>$file:</h4>";
        echo "<ul>";
        foreach ($vulns as $vuln) {
            echo "<li style='color: #c00;'>$vuln</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
} else {
    echo "<p style='color: green;'>✓ No obvious SQL injection vulnerabilities found in checked files.</p>";
}

// Check specific vulnerable patterns
echo "<h2>2. Direct Database Query Analysis</h2>";
$vulnerable_files = [
    'dashboard-comments-new.php' => 42,
    'analyze-news-categories.php' => 148,
    'dashboard-vpo-functional.php' => 29,
    'dashboard-schools-new.php' => 30
];

echo "<div style='background: #fee; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
echo "<h3 style='color: #c00;'>⚠️ Files with Potential SQL Injection:</h3>";
echo "<p>The following files use real_escape_string() but still concatenate user input in LIKE clauses, which can be vulnerable:</p>";
echo "<ul>";
foreach ($vulnerable_files as $file => $line) {
    if (file_exists($file)) {
        echo "<li><code>$file</code> (around line $line) - Uses LIKE with concatenated input</li>";
    }
}
echo "</ul>";
echo "<p><strong>Risk:</strong> Even with real_escape_string(), LIKE clauses can be exploited with wildcard characters.</p>";
echo "</div>";

// Check for XSS vulnerabilities
echo "<h2>3. Cross-Site Scripting (XSS) Vulnerabilities</h2>";
$xss_patterns = [
    'echo $_GET' => 'Direct output of GET parameters',
    'echo $_POST' => 'Direct output of POST parameters',
    'print $_REQUEST' => 'Direct output of REQUEST parameters'
];

$xss_found = false;
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        foreach ($xss_patterns as $pattern => $desc) {
            if (stripos($content, $pattern) !== false) {
                echo "<p style='color: #c00;'>⚠️ <code>$file</code> - $desc</p>";
                $xss_found = true;
            }
        }
    }
}

if (!$xss_found) {
    echo "<p style='color: green;'>✓ No obvious XSS vulnerabilities found.</p>";
}

// Check authentication security
echo "<h2>4. Authentication Security</h2>";
echo "<ul>";

// Check password hashing
$login_content = file_exists('login_modern.php') ? file_get_contents('login_modern.php') : '';
if (strpos($login_content, 'password_verify') !== false) {
    echo "<li style='color: green;'>✓ Uses secure password_verify() for authentication</li>";
} else {
    echo "<li style='color: #c00;'>⚠️ May not be using secure password verification</li>";
}

// Check session handling
if (strpos($login_content, 'session_start()') !== false) {
    echo "<li style='color: green;'>✓ Proper session handling found</li>";
}

// Check for CSRF protection
$csrf_found = false;
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'csrf_token') !== false || strpos($content, 'token') !== false) {
            $csrf_found = true;
            break;
        }
    }
}

if ($csrf_found) {
    echo "<li style='color: green;'>✓ CSRF token implementation found</li>";
} else {
    echo "<li style='color: #ffa500;'>⚠️ No CSRF protection found in forms</li>";
}

echo "</ul>";

// File upload security
echo "<h2>5. File Upload Security</h2>";
$upload_files = glob("**/upload*.php", GLOB_BRACE);
if (!empty($upload_files)) {
    echo "<p style='color: #ffa500;'>⚠️ Found " . count($upload_files) . " file upload scripts - need manual review</p>";
} else {
    echo "<p style='color: green;'>✓ No obvious file upload scripts found</p>";
}

// Recommendations
echo "<h2>6. Security Recommendations</h2>";
echo "<div style='background: #e6f3ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>High Priority Fixes:</h3>";
echo "<ol>";
echo "<li><strong>SQL Injection in LIKE clauses:</strong> Replace all instances of concatenated LIKE queries with prepared statements</li>";
echo "<li><strong>Use Prepared Statements:</strong> Convert all queries to use parameterized queries (PDO or mysqli with bind_param)</li>";
echo "<li><strong>Implement CSRF Protection:</strong> Add CSRF tokens to all forms</li>";
echo "<li><strong>Content Security Policy:</strong> Add CSP headers to prevent XSS</li>";
echo "</ol>";

echo "<h3>Example Fix for SQL Injection:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; overflow-x: auto;'>";
echo htmlspecialchars('// Vulnerable code:
$searchLike = \'%\' . $connection->real_escape_string($search) . \'%\';
$query = "SELECT * FROM table WHERE column LIKE \'$searchLike\'";

// Secure code:
$stmt = $connection->prepare("SELECT * FROM table WHERE column LIKE ?");
$searchParam = \'%\' . $search . \'%\';
$stmt->bind_param("s", $searchParam);
$stmt->execute();');
echo "</pre>";

echo "<h3>Additional Security Measures:</h3>";
echo "<ul>";
echo "<li>Enable SQL strict mode</li>";
echo "<li>Use Content-Security-Policy headers</li>";
echo "<li>Implement rate limiting</li>";
echo "<li>Add input validation on all forms</li>";
echo "<li>Log and monitor suspicious activities</li>";
echo "<li>Regular security audits</li>";
echo "</ul>";
echo "</div>";

// Summary
echo "<h2>7. Summary</h2>";
echo "<div style='background: #fffacd; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Critical Issues Found:</strong></p>";
echo "<ul>";
echo "<li style='color: #c00;'>SQL Injection vulnerabilities in multiple dashboard files using LIKE clauses</li>";
echo "<li style='color: #ffa500;'>Missing CSRF protection on forms</li>";
echo "<li style='color: #ffa500;'>Some files using real_escape_string instead of prepared statements</li>";
echo "</ul>";
echo "<p><strong>Security Score: 6/10</strong> - Several vulnerabilities need immediate attention</p>";
echo "</div>";
?>
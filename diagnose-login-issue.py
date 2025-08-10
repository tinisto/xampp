#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 Diagnosing login-process.php 404 issue...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check what login files exist on the server
        print("\n📁 Login-related files on server:")
        files = []
        ftp.retrlines('LIST', files.append)
        
        login_files = []
        for file_info in files:
            filename = file_info.split()[-1] if file_info.split() else ""
            if 'login' in filename.lower() and '.php' in filename:
                login_files.append(filename)
                # Get file size for verification
                try:
                    size = ftp.size(filename)
                    print(f"  📄 {filename} ({size} bytes)")
                except:
                    print(f"  📄 {filename}")
        
        # Download and check login-standalone.php content
        print("\n🔍 Checking login-standalone.php form action...")
        try:
            login_content = []
            ftp.retrlines('RETR login-standalone.php', login_content.append)
            
            # Find form action
            form_found = False
            for line in login_content:
                if 'action=' in line and 'form' in line.lower():
                    print(f"  Found: {line.strip()}")
                    form_found = True
                    if 'login-process.php' in line:
                        print("  ❌ ERROR: Form still points to /login-process.php!")
                    elif '/pages/login/login_process_simple.php' in line:
                        print("  ✅ CORRECT: Form points to /pages/login/login_process_simple.php")
            
            if not form_found:
                print("  ⚠️  Could not find form action in file")
                
        except Exception as e:
            print(f"  ❌ Could not read login-standalone.php: {e}")
        
        # Create a diagnostic page
        print("\n📝 Creating diagnostic page...")
        diagnostic_content = '''<?php
// Diagnostic page to check which login file is being served
$request_uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
$script_name = $_SERVER['SCRIPT_NAME'] ?? 'unknown';
$php_self = $_SERVER['PHP_SELF'] ?? 'unknown';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Diagnostic - 11klassniki.ru</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMwMDdiZmYiLz4KPHRleHQgeD0iMTYiIHk9IjIwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSJib2xkIiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+MTE8L3RleHQ+Cjwvc3ZnPgo=" type="image/svg+xml">
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 800px; margin: 0 auto; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e9; color: #2e7d32; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        .form-test { background: #fff3e0; padding: 20px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Login Diagnostic Page</h1>
        
        <div class="info">
            <h3>📋 Server Information:</h3>
            <p><strong>Request URI:</strong> <?= htmlspecialchars($request_uri) ?></p>
            <p><strong>Script Name:</strong> <?= htmlspecialchars($script_name) ?></p>
            <p><strong>PHP Self:</strong> <?= htmlspecialchars($php_self) ?></p>
            <p><strong>Current File:</strong> <?= __FILE__ ?></p>
        </div>
        
        <div class="form-test">
            <h3>🧪 Test Form (Current Behavior):</h3>
            <form method="POST" action="/login-process.php">
                <p>This form submits to: <code>/login-process.php</code></p>
                <button type="submit">Test Submit (Will 404)</button>
            </form>
        </div>
        
        <div class="form-test success">
            <h3>✅ Correct Form (Should Work):</h3>
            <form method="POST" action="/pages/login/login_process_simple.php">
                <p>This form submits to: <code>/pages/login/login_process_simple.php</code></p>
                <button type="submit">Test Submit (Should Work)</button>
            </form>
        </div>
        
        <div class="info">
            <h3>🔧 Diagnosis:</h3>
            <?php
            // Check if login-standalone.php exists
            if (file_exists(__DIR__ . '/login-standalone.php')) {
                echo '<p class="success">✅ login-standalone.php exists on server</p>';
                
                // Read and check form action
                $content = file_get_contents(__DIR__ . '/login-standalone.php');
                if (strpos($content, 'login-process.php') !== false) {
                    echo '<p class="error">❌ ERROR: login-standalone.php contains wrong form action!</p>';
                } elseif (strpos($content, '/pages/login/login_process_simple.php') !== false) {
                    echo '<p class="success">✅ login-standalone.php has correct form action</p>';
                } else {
                    echo '<p>⚠️ Could not determine form action in login-standalone.php</p>';
                }
            } else {
                echo '<p class="error">❌ login-standalone.php NOT FOUND on server!</p>';
            }
            
            // Check routing
            if (strpos($_SERVER['REQUEST_URI'] ?? '', 'login-diagnostic') !== false) {
                echo '<p class="success">✅ This diagnostic page is accessible</p>';
            }
            ?>
        </div>
        
        <div class="info">
            <h3>📝 Solution:</h3>
            <p>The login form needs to submit to: <code>/pages/login/login_process_simple.php</code></p>
            <p>If you're seeing 404 errors, the form is pointing to the wrong endpoint.</p>
        </div>
    </div>
</body>
</html>'''
        
        # Upload diagnostic page
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(diagnostic_content)
            tmp_path = tmp.name
        
        try:
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /login-diagnostic.php', file)
            print("✅ Created diagnostic page")
            os.unlink(tmp_path)
        except Exception as e:
            print(f"❌ Could not create diagnostic page: {e}")
        
        # The real fix - update login-template.php since that's what's being served
        print("\n🔧 The real issue: Server is serving login-template.php, not login-standalone.php")
        print("Need to check what login-template.php contains...")
        
        try:
            template_content = []
            ftp.retrlines('RETR login-template.php', template_content.append)
            
            for line in template_content:
                if 'action=' in line and 'form' in line.lower():
                    print(f"  login-template.php form: {line.strip()}")
                    break
        except:
            print("  Could not read login-template.php")
        
        ftp.quit()
        
        print("\n💡 Diagnosis Summary:")
        print("1. Server routing is serving login-template.php instead of login-standalone.php")
        print("2. Need to verify which file contains the wrong form action")
        print("3. Test page: https://11klassniki.ru/login-diagnostic.php")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()
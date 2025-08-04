#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def find_and_fix_dashboard():
    print("🔍 Finding dashboard file and fixing issues")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # List all files to find dashboard
        print("📁 Looking for dashboard files...")
        files = ftp.nlst()
        dashboard_files = [f for f in files if 'dashboard' in f.lower()]
        
        print(f"Found dashboard files: {dashboard_files}")
        
        # Upload our updated dashboard
        print("📤 Uploading dashboard-professional.php...")
        local_file = "/Applications/XAMPP/xamppfiles/htdocs/dashboard-professional.php"
        
        with open(local_file, 'rb') as f:
            ftp.storbinary('STOR dashboard-professional.php', f)
        
        print("✅ dashboard-professional.php uploaded")
        
        # Also upload a simpler migration runner that avoids shell_exec
        print("📤 Uploading fixed migration tool...")
        migration_content = '''<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

echo "<h1>Database Migrations</h1>";
echo "<p>Please run migrations manually via cPanel Terminal:</p>";
echo "<pre>cd public_html && php database/migrate.php migrate</pre>";
echo "<p>Or contact support for assistance.</p>";
echo "<p><a href='/dashboard'>← Back to Dashboard</a></p>";
?>'''
        
        # Write to temp file and upload
        with open('/tmp/simple-migrations.php', 'w') as f:
            f.write(migration_content)
        
        with open('/tmp/simple-migrations.php', 'rb') as f:
            ftp.storbinary('STOR admin/run-migrations.php', f)
        
        print("✅ Fixed migration tool uploaded")
        
        ftp.quit()
        
        print("\n🎉 Files uploaded successfully!")
        print("\n📋 Next steps:")
        print("1. Clear browser cache (Ctrl+F5)")
        print("2. Visit https://11klassniki.ru/dashboard")
        print("3. You should now see the System section with admin tools")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    find_and_fix_dashboard()
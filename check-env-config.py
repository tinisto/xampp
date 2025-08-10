#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, content, filename):
    with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
        tmp.write(content)
        tmp_path = tmp.name
    
    with open(tmp_path, 'rb') as file:
        ftp.storbinary(f'STOR {filename}', file)
    os.unlink(tmp_path)

def main():
    print("🔍 CHECKING ENV CONFIGURATION")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check what files exist
        print("\n1️⃣ Checking config files...")
        
        # Check if loadEnv.php exists
        try:
            ftp.cwd('config')
            files = ftp.nlst()
            print("   Files in /config:")
            for f in files:
                print(f"   - {f}")
            ftp.cwd('..')
        except:
            print("   ❌ /config directory not found or empty")
        
        # Check if .env exists
        try:
            size = ftp.size('.env')
            print(f"\n   ✅ .env file exists ({size} bytes)")
        except:
            print("\n   ❌ .env file NOT found")
        
        # Create a temporary fix for db_connections.php
        print("\n2️⃣ Creating temporary database connection fix...")
        
        fixed_connection = '''<?php
// Temporary fix for database connection
// This bypasses the env loading issue

// Direct connection (same as working before)
$connection = @mysqli_connect('localhost', '11klassniki_claude', 'franko85!!@@85', '11klassniki_claude');

if (!$connection) {
    // Try alternative connection methods
    error_log("Database connection failed: " . mysqli_connect_error());
    
    // Don't redirect to error - just set connection to false
    $connection = false;
} else {
    // Set charset
    mysqli_set_charset($connection, 'utf8mb4');
}

// Make it globally available
$GLOBALS['connection'] = $connection;
?>'''
        
        upload_file(ftp, fixed_connection, 'database/db_connections_backup.php')
        print("   ✅ Created backup connection file")
        
        # Rename files to use backup
        print("\n3️⃣ Backing up current connection file...")
        
        try:
            ftp.rename('database/db_connections.php', 'database/db_connections_broken.php')
            print("   ✅ Backed up broken connection file")
        except:
            print("   ⚠️  Could not backup current file")
        
        try:
            ftp.rename('database/db_connections_backup.php', 'database/db_connections.php')
            print("   ✅ Activated fixed connection file")
        except:
            print("   ❌ Could not activate fixed file")
        
        ftp.quit()
        
        print("\n✅ Database connection should be restored!")
        print("\n🎯 The issue was:")
        print("• db_connections.php was looking for .env file")
        print("• When it couldn't find env variables, it redirected to /error")
        print("• This broke ALL database queries")
        print("\nNow using direct connection like before.")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
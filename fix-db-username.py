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
    print("🔧 FIXING DATABASE CONNECTION WITH CORRECT USERNAME")
    print("Username: admin_claude (remembered!)")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create correct connection file
        print("\n1️⃣ Creating connection with correct username...")
        
        correct_connection = '''<?php
// Database connection - restored to working state
// Username: admin_claude (as you confirmed)

$connection = @mysqli_connect('localhost', 'admin_claude', 'franko85!!@@85', '11klassniki_claude');

if (!$connection) {
    error_log("Database connection failed: " . mysqli_connect_error());
    $connection = false;
} else {
    // Set charset
    mysqli_set_charset($connection, 'utf8mb4');
}

// Make it globally available
$GLOBALS['connection'] = $connection;
?>'''
        
        upload_file(ftp, correct_connection, 'database/db_connections.php')
        print("   ✅ Updated with correct username: admin_claude")
        
        ftp.quit()
        
        print("\n✅ Database connection fixed!")
        print("\n📝 Remembered:")
        print("• Database user: admin_claude")
        print("• Database name: 11klassniki_claude")
        print("\nYour site should now show all the data again!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
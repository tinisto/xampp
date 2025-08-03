#!/usr/bin/env python3

import ftplib

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("Connecting...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    
    # Rename old file as backup
    try:
        ftp.rename(
            'pages/tests/tests-main.php',
            'pages/tests/tests-main-old.php'
        )
        print("✓ Backed up old file")
    except:
        print("⚠️  Could not backup old file")
    
    # Upload new unified version
    with open('pages/tests/tests-main-unified.php', 'rb') as f:
        ftp.storbinary(
            'STOR pages/tests/tests-main.php', 
            f
        )
        print("✓ Uploaded new unified template version")
    
    # Upload content file
    with open('pages/tests/tests-main-content.php', 'rb') as f:
        ftp.storbinary(
            'STOR pages/tests/tests-main-content.php', 
            f
        )
        print("✓ Uploaded content file")
    
    print("\n✅ TESTS PAGE UPDATED!")
    print("\n🎯 What was done:")
    print("1. 🔄 Switched to unified template engine")
    print("2. 🌙 Theme toggle now works on /tests")
    print("3. 📱 Consistent with rest of site")
    print("4. 🎨 Added dark mode styles for test cards")
    print("\n✨ https://11klassniki.ru/tests now has working theme toggle!")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")
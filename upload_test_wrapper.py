#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading test wrapper...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('post_test_wrapper.php', 'rb') as f:
        ftp.storbinary('STOR post_test_wrapper.php', f)
    print("✓ Uploaded post_test_wrapper.php")
    
    # Also check what's in check_under_construction.php
    print("\nChecking check_under_construction.php...")
    try:
        # Download it to see what it contains
        with open('check_under_construction_from_server.php', 'wb') as f:
            ftp.retrbinary('RETR common-components/check_under_construction.php', f.write)
        print("✓ Downloaded check_under_construction.php")
        
        # Read first few lines
        with open('check_under_construction_from_server.php', 'r') as f:
            lines = f.readlines()[:20]
            print("\nFirst 20 lines:")
            for i, line in enumerate(lines):
                print(f"{i+1}: {line.rstrip()}")
    except Exception as e:
        print(f"Could not check check_under_construction.php: {e}")
    
    ftp.quit()
    print("\n\nCheck: https://11klassniki.ru/post_test_wrapper.php")
    
except Exception as e:
    print(f"Error: {e}")
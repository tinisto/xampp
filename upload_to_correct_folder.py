#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading to /11klassnikiru folder...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Change to the correct directory
    ftp.cwd('/11klassnikiru')
    print("✓ Changed to /11klassnikiru directory")
    
    # Upload emergency debug file
    with open('emergency_debug.php', 'rb') as f:
        ftp.storbinary('STOR emergency_debug.php', f)
    print("✓ Uploaded emergency_debug.php")
    
    # Upload test file
    with open('test_location.php', 'rb') as f:
        ftp.storbinary('STOR test_location.php', f)
    print("✓ Uploaded test_location.php")
    
    # Verify uploads
    files = []
    ftp.retrlines('NLST *.php', files.append)
    if 'emergency_debug.php' in files:
        print("✓ Confirmed: emergency_debug.php exists")
    if 'test_location.php' in files:
        print("✓ Confirmed: test_location.php exists")
    
    ftp.quit()
    
    print("\nNow try:")
    print("- https://11klassniki.ru/test_location.php")
    print("- https://11klassniki.ru/emergency_debug.php")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Finding correct web root directory...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # The previous upload showed files are in root, not public_html
    # Let's check if pages directory exists in root
    print("\nChecking root directory structure:")
    ftp.cwd('/')
    
    dirs = []
    ftp.retrlines('NLST', dirs.append)
    
    # Look for our known directories
    known_dirs = ['pages', 'includes', 'api', 'common-components']
    found = []
    
    for d in dirs:
        if d in known_dirs:
            found.append(d)
    
    print(f"Found directories: {found}")
    
    if 'pages' in found:
        print("\n✓ This is the correct web root!")
        
        # Upload debug file to root
        with open('emergency_debug.php', 'rb') as f:
            ftp.storbinary('STOR emergency_debug.php', f)
        print("✓ Uploaded emergency_debug.php to root")
        
        # Also upload test file
        with open('test_location.php', 'rb') as f:
            ftp.storbinary('STOR test_location.php', f)
        print("✓ Uploaded test_location.php to root")
        
        print("\nTry these URLs:")
        print("- https://11klassniki.ru/emergency_debug.php")
        print("- https://11klassniki.ru/test_location.php")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
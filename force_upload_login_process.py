#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Force uploading login_process_simple.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to login directory
    ftp.cwd('/11klassnikiru/pages/login')
    
    # Delete the old file first
    try:
        ftp.delete('login_process_simple.php')
        print("✓ Deleted old login_process_simple.php")
    except:
        print("No old file to delete")
    
    # Upload new version
    with open('pages/login/login_process_simple.php', 'rb') as f:
        ftp.storbinary('STOR login_process_simple.php', f)
    print("✓ Uploaded new login_process_simple.php")
    
    # Verify file size
    size = ftp.size('login_process_simple.php')
    print(f"✓ File size: {size} bytes")
    
    ftp.quit()
    print("\n✅ Force upload completed!")
    print("Now try logging in from: https://11klassniki.ru/write")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
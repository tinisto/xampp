#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Force uploading simple error page...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Upload verifier first
    ftp.cwd('/11klassnikiru')
    with open('verify_error_page.php', 'rb') as f:
        ftp.storbinary('STOR verify_error_page.php', f)
    print("✓ Uploaded verify_error_page.php")
    
    # Navigate to error directory
    ftp.cwd('/11klassnikiru/pages/error')
    
    # Delete old error.php if exists
    try:
        ftp.delete('error.php')
        print("✓ Deleted old error.php")
    except:
        print("No old error.php to delete")
    
    # Upload simple error page
    with open('pages/error/error-simple.php', 'rb') as f:
        ftp.storbinary('STOR error.php', f)
    print("✓ Uploaded new simple error.php")
    
    # Check file size to confirm
    size = ftp.size('error.php')
    print(f"✓ File size: {size} bytes")
    
    ftp.quit()
    print("\n✅ Force upload completed!")
    print("\nCheck: https://11klassniki.ru/verify_error_page.php")
    print("Then test: https://11klassniki.ru/error")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
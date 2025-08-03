#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading fixed pages with reusable logo...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Upload logo component
    ftp.cwd('/11klassnikiru/common-components')
    with open('common-components/logo.php', 'rb') as f:
        ftp.storbinary('STOR logo.php', f)
    print("✓ Uploaded reusable logo.php")
    
    # Upload fixed 404 page
    ftp.cwd('/11klassnikiru/pages/404')
    with open('pages/404/404-simple.php', 'rb') as f:
        ftp.storbinary('STOR 404.php', f)
    print("✓ Uploaded fixed 404.php")
    
    # Upload fixed error page
    ftp.cwd('/11klassnikiru/pages/error')
    with open('pages/error/error-simple.php', 'rb') as f:
        ftp.storbinary('STOR error.php', f)
    print("✓ Uploaded fixed error.php")
    
    ftp.quit()
    print("\n✅ All pages updated!")
    print("- Fixed <!DOCTYPE html> syntax")
    print("- Removed EOF footer")
    print("- Created reusable logo component")
    print("- Both pages now use same logo (replaceable)")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
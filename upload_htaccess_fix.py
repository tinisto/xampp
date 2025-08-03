#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading updated .htaccess with /error route...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to root
    ftp.cwd('/11klassnikiru')
    
    # Upload .htaccess
    with open('.htaccess', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    print("✓ Uploaded .htaccess with /error route")
    
    ftp.quit()
    print("\n✅ Upload completed!")
    print("The /error URL should now load the simple error page without header/footer")
    
except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
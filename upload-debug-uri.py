#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-request-uri.php", 'rb') as f:
            ftp.storbinary('STOR debug-request-uri.php', f)
        
        print("✓ Uploaded debug-request-uri.php")
        print("🔍 Test: https://11klassniki.ru/debug-request-uri.php")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
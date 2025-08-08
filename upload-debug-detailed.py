#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-navigation-detailed.php", 'rb') as f:
            ftp.storbinary('STOR debug-navigation-detailed.php', f)
        
        print("✓ Uploaded debug-navigation-detailed.php")
        print("🔍 Test URL: https://11klassniki.ru/debug-navigation-detailed.php?url_news=novosti-shkol")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
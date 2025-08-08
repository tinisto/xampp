#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-page.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-page.php', f)
        
        print("✓ Uploaded debug-news-page.php")
        print("🔍 Test: https://11klassniki.ru/debug-news-page.php")
        print("📝 This will show which file is handling the request and why navigation is wrong")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
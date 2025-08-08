#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news.php", 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news.php', f)
        
        print("✓ Uploaded pages/common/news/news.php")
        print("🎯 FIXED: News navigation now passes correct current path")
        print("📝 Instead of passing REQUEST_URI (/news), now constructs /news/novosti-spo from GET params")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload enhanced debug script
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-complete.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-complete.php', f)
        
        print("✅ Uploaded enhanced debug script")
        print("\n🔍 ENHANCED DEBUG:")
        print("  Visit: https://11klassniki.ru/debug-news-complete.php")
        print("  This will show:")
        print("    - All news articles in database")
        print("    - Table structure")
        print("    - URL patterns")
        print("    - Status field values")
        print("    - RGGU-related articles (if any)")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
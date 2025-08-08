#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-categories-db.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-categories-db.php', f)
        
        print("✓ Uploaded debug-news-categories-db.php")
        print("🔍 Check: https://11klassniki.ru/debug-news-categories-db.php")
        print("📊 This will show:")
        print("  - News table structure")
        print("  - All news_type values and counts") 
        print("  - All category_news values and counts")
        print("  - All category_id values and counts")
        print("  - Sample news entries")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
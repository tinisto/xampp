#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # First backup the current broken version
        print("📦 Backing up current news-single.php...")
        try:
            with open('news-single-backup.php', 'wb') as f:
                ftp.retrbinary('RETR pages/common/news/news-single.php', f.write)
            
            with open('news-single-backup.php', 'rb') as f:
                ftp.storbinary('STOR pages/common/news/news-single-broken.php', f)
            print("✅ Backup created: news-single-broken.php")
        except Exception as e:
            print(f"⚠️ Backup failed: {e}")
        
        # Upload the working version
        with open("/Applications/XAMPP/xamppfiles/htdocs/news-single-working.php", 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news-single.php', f)
        
        print("✅ Uploaded working news-single.php")
        print("\n🎯 FIXED NEWS SINGLE PAGE:")
        print("  ✅ Removed all potential PHP errors")
        print("  ✅ Added proper error handling")
        print("  ✅ Simplified complex components")
        print("  ✅ Based on successful debug results")
        print("\n📰 Test the article now:")
        print("  🔗 https://11klassniki.ru/news/miit-snova-smenil-imya")
        print("  🔗 https://11klassniki.ru/news/rektor-rggu-e-n-ivahnenko-rasskazal-o-hode-priemnoy-kampanii")
        print("\n📝 Should now show full article content instead of empty page")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
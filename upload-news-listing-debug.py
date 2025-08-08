#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the news listing debug script
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-listing-mismatch.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-listing-mismatch.php', f)
        
        print("✅ Uploaded news listing mismatch debug script")
        print("\n🔍 NEWS LISTING vs SINGLE ARTICLE DEBUG:")
        print("  📊 Visit: https://11klassniki.ru/debug-news-listing-mismatch.php")
        print("\n🎯 This will show:")
        print("  ✅ What query the news listing uses")
        print("  ✅ What query single news pages use")
        print("  ✅ Why articles show in listing but not individually")
        print("  ✅ Field mismatches between listing and single pages")
        print("  ✅ Whether articles exist but are not approved")
        print("\n📝 This will help fix the mismatch between /news and /news/article-slug")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the comprehensive categories debug
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-news-categories-complete.php", 'rb') as f:
            ftp.storbinary('STOR debug-news-categories-complete.php', f)
        
        print("✅ Uploaded comprehensive news categories debug")
        print("\n🔍 NEWS CATEGORIES MISMATCH DEBUG:")
        print("  📊 Visit: https://11klassniki.ru/debug-news-categories-complete.php")
        print("\n🎯 This will show:")
        print("  ✅ All actual category_news values in database")
        print("  ✅ Article counts for each category")
        print("  ✅ What navigation expects vs what exists")
        print("  ✅ Articles with unexpected category values")
        print("  ✅ Why some articles show but aren't clickable")
        print("\n📝 Current issue:")
        print("  - Navigation shows 4 categories (1,2,3,4)")
        print("  - But database might have more categories")
        print("  - Articles like '/news/11' suggest numeric URL issues")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
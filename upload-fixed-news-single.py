#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the fixed news-single.php
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news-single.php", 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news-single.php', f)
        
        print("✅ Uploaded fixed news-single.php")
        print("\n🎯 FIXED DATABASE FIELD MISMATCH:")
        print("  ✅ id_news → id")
        print("  ✅ url_news → url_slug") 
        print("  ✅ content_news → text_news")
        print("  ✅ created_at → date_news")
        print("  ✅ views → view_news")
        print("  ✅ status → approved")
        print("  ✅ category_id → category_news")
        print("  ✅ author_id → user_id")
        print("\n📰 Single news articles should now work!")
        print("  Test: https://11klassniki.ru/news/[article-url-slug]")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
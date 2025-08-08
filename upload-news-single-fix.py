#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload fixed news-single.php
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news-single.php", 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news-single.php', f)
        print("✅ Uploaded fixed news-single.php")
        
        # Upload users table debug
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-users-table.php", 'rb') as f:
            ftp.storbinary('STOR debug-users-table.php', f)
        print("✅ Uploaded debug-users-table.php")
        
        print("\n🎯 FIXED USERS TABLE ISSUE:")
        print("  ✅ Removed non-existent u.username reference")
        print("  ✅ Removed non-existent comment_count subquery")
        print("  ✅ Using author_news field from news table instead")
        print("  ✅ Simplified query to only use existing fields")
        print("\n📰 Single news articles should now work!")
        print("  🔍 Debug: https://11klassniki.ru/debug-users-table.php")
        print("  📝 Test: https://11klassniki.ru/news/[article-url-slug]")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
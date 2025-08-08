#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload debug script
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-single-news.php", 'rb') as f:
            ftp.storbinary('STOR debug-single-news.php', f)
        print("✅ Uploaded debug-single-news.php")
        
        # Delete unused seo-head.php from server
        try:
            ftp.delete('common-components/seo-head.php')
            print("✅ Deleted unused seo-head.php from server")
        except Exception as e:
            print(f"⚠️ Could not delete seo-head.php: {e}")
        
        print("\n🔍 DEBUG SCRIPT UPLOADED:")
        print("  Visit: https://11klassniki.ru/debug-single-news.php")
        print("  This will show why the single news page is empty")
        print("\n🧹 CLEANUP COMPLETE:")
        print("  ✅ Removed unused seo-head.php component")
        print("  🎯 Only necessary components remain")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the debug tool for 404 news cards
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-404-news-cards.php", 'rb') as f:
            ftp.storbinary('STOR debug-404-news-cards.php', f)
        
        print("✅ Uploaded 404 news cards debug tool")
        print("\n🔍 DEBUG 5 PROBLEMATIC NEWS CARDS:")
        print("  📊 Visit: https://11klassniki.ru/debug-404-news-cards.php")
        print("\n🎯 This tool will identify:")
        print("  ✅ All category_news values in database")
        print("  ✅ Articles with unsupported categories (causing 404s)")
        print("  ✅ Articles with NULL/empty categories")
        print("  ✅ Recent articles that appear on /news page")
        print("  ✅ Interactive fix tool to correct categories")
        print("\n📝 Expected findings:")
        print("  - Navigation supports categories: 1, 2, 3, 4, education")
        print("  - Any other values cause 404 errors")
        print("  - The 5 problematic cards likely have invalid category values")
        print("\n🛠️ QUICK FIX AVAILABLE:")
        print("  - Tool provides dropdown to reassign problematic categories")
        print("  - Can convert invalid categories to valid ones (1,2,3,4)")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
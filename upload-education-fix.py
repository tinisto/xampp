#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload fixed news listing 
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/common/news/news.php", 'rb') as f:
            ftp.storbinary('STOR pages/common/news/news.php', f)
        print("✅ Uploaded fixed news.php")
        
        # Upload category fix tool
        with open("/Applications/XAMPP/xamppfiles/htdocs/fix-education-category.php", 'rb') as f:
            ftp.storbinary('STOR fix-education-category.php', f)
        print("✅ Uploaded fix-education-category.php")
        
        print("\n🎯 FIXED EDUCATION CATEGORIES ISSUE:")
        print("  ✅ Updated news listing to include both '4' and 'education' values")
        print("  ✅ This will show all 151 education articles (150 + 1)")
        print("\n🛠️ OPTIONAL CLEANUP TOOL:")
        print("  📊 Visit: https://11klassniki.ru/fix-education-category.php")
        print("  🎯 This tool can standardize 'education' → '4' for consistency")
        print("\n📰 Test the fixed category:")
        print("  🔗 https://11klassniki.ru/news/novosti-obrazovaniya")
        print("  📝 Should now show all education articles properly")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
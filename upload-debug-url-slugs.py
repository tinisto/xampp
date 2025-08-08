#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the URL slug debug tool
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-url-slug-issues.php", 'rb') as f:
            ftp.storbinary('STOR debug-url-slug-issues.php', f)
        
        print("✅ Uploaded URL slug debug tool")
        print("\n🔍 DEBUG URL SLUG ISSUES:")
        print("  📊 Visit: https://11klassniki.ru/debug-url-slug-issues.php")
        print("\n🎯 This will test the specific problematic articles:")
        print("  📰 ID 621: dasdasdada--adadad-a-dasdasda (double dashes)")
        print("  📰 ID 620: sdfdsfd")  
        print("  📰 ID 617: 11 (numeric, conflicts with pagination)")
        print("\n🔍 Will check for:")
        print("  ✅ If articles exist in database")
        print("  ✅ Approval status (must be approved=1)")
        print("  ✅ URL slug formatting issues")
        print("  ✅ Routing logic problems")
        print("  ✅ Single article query results")
        print("\n🛠️ QUICK FIXES AVAILABLE:")
        print("  - Fix approval status if needed")
        print("  - Identify slug formatting issues")
        print("  - Test actual database queries")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
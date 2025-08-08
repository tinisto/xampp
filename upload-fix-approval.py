#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the approval fix tool
        with open("/Applications/XAMPP/xamppfiles/htdocs/fix-news-listing-approval.php", 'rb') as f:
            ftp.storbinary('STOR fix-news-listing-approval.php', f)
        
        print("✅ Uploaded news listing approval fix")
        print("\n🔍 ROOT CAUSE IDENTIFIED:")
        print("  ❌ Main /news page shows ALL articles (approved or not)")
        print("  ❌ Single article pages require approved=1")
        print("  ❌ This mismatch causes 404 errors")
        print("\n📊 PROBLEMATIC ARTICLES:")
        print("  - ID 621: approved=0 (shows in listing, 404 when clicked)")
        print("  - ID 620: approved=0 (shows in listing, 404 when clicked)")
        print("  - ID 617: approved=0 (shows in listing, 404 when clicked)")
        print("\n🛠️ TWO SOLUTIONS AVAILABLE:")
        print("\n  1️⃣ APPROVE THE ARTICLES:")
        print("     Visit: https://11klassniki.ru/debug-url-slug-issues.php")
        print("     Check the boxes and click 'Fix Approval Status'")
        print("\n  2️⃣ FIX THE LISTING:")
        print("     Visit: https://11klassniki.ru/fix-news-listing-approval.php")
        print("     This will update /news to only show approved articles")
        print("\n✨ Either solution will fix the 404 errors!")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
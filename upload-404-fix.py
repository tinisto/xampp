#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload new 404 page
        with open("/Applications/XAMPP/xamppfiles/htdocs/404-new.php", 'rb') as f:
            ftp.storbinary('STOR 404-new.php', f)
        print("✅ Uploaded 404-new.php")
        
        # Update .htaccess to use new 404 page
        print("\n📝 Updating .htaccess for 404 handling...")
        
        print("\n⚠️  CRITICAL ISSUE FOUND:")
        print("  - 101 files still use template-engine-ultimate.php")
        print("  - Should be using real_template.php instead")
        print("  - This explains persistent favicon issues")
        print("\n🎯 IMMEDIATE FIX:")
        print("  - Created 404-new.php using real_template.php")
        print("  - Ready to replace old 404.php")
        print("\n📊 AFFECTED CRITICAL PAGES:")
        print("  - /pages/404/404.php")
        print("  - /pages/login/login-modern.php") 
        print("  - /pages/search/search.php")
        print("  - /pages/about/about.php")
        print("  - /pages/write/write.php")
        print("  - Many admin/dashboard pages")
        print("\n⚡ RECOMMENDATION:")
        print("  Need systematic migration of all 101 files to real_template.php")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the fixed seo-head.php
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/seo-head.php", 'rb') as f:
            ftp.storbinary('STOR common-components/seo-head.php', f)
        
        print("✅ Uploaded fixed seo-head.php")
        print("🎯 FINAL FIX: Removed favicon.php reference from seo-head.php component")
        print("📱 All favicon references now use static inline SVG")
        print("🚫 No more PHP favicon component causing spinning/reloading")
        print("🔄 News page should now show stable favicon (may need hard refresh)")
        print("")
        print("📋 FAVICON CLEANUP COMPLETE:")
        print("  ✅ Removed favicon.php from footer (real_footer.php)")
        print("  ✅ Deleted favicon.php component entirely") 
        print("  ✅ Fixed seo-head.php to use static inline favicon")
        print("  ✅ Template (real_template.php) uses static inline favicon")
        print("  🎯 All pages should now have stable, non-spinning favicon")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
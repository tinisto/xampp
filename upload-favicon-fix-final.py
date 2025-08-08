#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the fixed template
        with open("/Applications/XAMPP/xamppfiles/htdocs/real_template.php", 'rb') as f:
            ftp.storbinary('STOR real_template.php', f)
        
        print("✅ Uploaded fixed real_template.php")
        print("\n🎯 FAVICON FIX:")
        print("  ❌ REMOVED: Second favicon link with timestamp")
        print("  ✅ KEPT: Only inline SVG favicon (no external requests)")
        print("\n📊 ROOT CAUSE:")
        print("  - Template had duplicate favicon links")
        print("  - Second link: href='/favicon.ico?v=1754636985'")
        print("  - This caused browser to continuously try loading favicon.ico")
        print("\n✨ Result: Favicon should now be stable on all pages!")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
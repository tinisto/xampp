#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the fixed footer file
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/real_footer.php", 'rb') as f:
            ftp.storbinary('STOR common-components/real_footer.php', f)
        
        print("✅ Uploaded fixed real_footer.php")
        print("🎯 FIXED: Removed duplicate favicon from footer component")
        print("🚫 Footer no longer includes favicon.php which was causing infinite reload")
        print("📱 Favicon is now handled only in template head section (real_template.php)")
        print("🔄 Hard refresh (Ctrl+F5) should stop the infinite spinning favicon")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
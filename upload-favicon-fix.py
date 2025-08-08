#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/favicon.php", 'rb') as f:
            ftp.storbinary('STOR common-components/favicon.php', f)
        
        print("✓ Uploaded common-components/favicon.php")
        print("🎯 FIXED: Favicon infinite loading loop")
        print("📝 Changed time() to static timestamp to prevent constant reload")
        print("🔄 Browser may need hard refresh (Ctrl+F5) to stop spinning")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
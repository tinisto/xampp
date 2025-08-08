#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Delete favicon.php from server
        try:
            ftp.delete('common-components/favicon.php')
            print("✅ Deleted common-components/favicon.php from server")
        except Exception as e:
            print(f"⚠️ Could not delete favicon.php from server: {e}")
        
        print("🎯 CLEANUP: Removed unused favicon.php component")
        print("📱 Favicon is now handled directly in template head section")
        print("🚫 No more separate favicon.php component to cause issues")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
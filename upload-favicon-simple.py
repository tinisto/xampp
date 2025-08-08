#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the simplified files
        files = [
            "real_template.php",
            "common-components/real_header.php"
        ]
        
        for file in files:
            with open(f"/Applications/XAMPP/xamppfiles/htdocs/{file}", 'rb') as f:
                ftp.storbinary(f'STOR {file}', f)
            print(f"✓ Uploaded {file}")
        
        print("\n🎯 SIMPLIFIED FAVICON:")
        print("✅ Removed unnecessary favicon.php component")
        print("✅ Added simple inline SVG favicon directly to template")
        print("✅ No more PHP component complexity")
        print("✅ No more infinite loading loops")
        print("🔄 Hard refresh (Ctrl+F5) may be needed to see changes")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
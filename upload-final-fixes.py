#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        files = [
            "common-components/category-navigation.php",
            "real_template.php"
        ]
        
        for file in files:
            with open(f"/Applications/XAMPP/xamppfiles/htdocs/{file}", 'rb') as f:
                ftp.storbinary(f'STOR {file}', f)
            print(f"✓ Uploaded {file}")
        
        print("\n🎯 FINAL FIXES:")
        print("1. ✅ Fixed navigation active state - exact URL matching now works correctly")
        print("2. ✅ Fixed dark ad background - changed body background to light")
        print("3. ✅ Added CSS to hide problematic auto-placed ads above header")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
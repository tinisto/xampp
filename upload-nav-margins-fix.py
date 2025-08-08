#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload both files
        files = [
            ("/Applications/XAMPP/xamppfiles/htdocs/real_template.php", "real_template.php"),
            ("/Applications/XAMPP/xamppfiles/htdocs/common-components/category-navigation.php", "common-components/category-navigation.php")
        ]
        
        for local_path, remote_path in files:
            with open(local_path, 'rb') as f:
                ftp.storbinary(f'STOR {remote_path}', f)
                print(f"✅ Uploaded {remote_path}")
        
        print("\n🎯 NAVIGATION MARGINS FIXED:")
        print("  ✅ Removed left/right padding from category-navigation component")
        print("  ✅ Added margin-top: 30px when title is hidden")
        print("  ✅ Navigation now has proper spacing without extra padding")
        print("\n📊 CHANGES:")
        print("  - Navigation: Removed 'padding: 0 20px' (desktop)")
        print("  - Navigation: Removed 'padding: 0 15px' (mobile)")
        print("  - Template: Adds margin-top when navigation is first visible element")
        print("\n✨ Result: Clean navigation layout with proper spacing!")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload the updated template
        with open("/Applications/XAMPP/xamppfiles/htdocs/real_template.php", 'rb') as f:
            ftp.storbinary('STOR real_template.php', f)
        
        print("✅ Uploaded updated real_template.php")
        print("\n🎯 TEMPLATE UPDATE:")
        print("  ✅ Title/subtitle section now hidden when navigation is present")
        print("  ✅ Checks if greyContent2 contains 'category-navigation'")
        print("  ✅ If navigation exists, title section is skipped")
        print("\n📊 AFFECTED PAGES:")
        print("  - /news (with Все новости, Новости ВПО, etc.)")
        print("  - Any page using category navigation")
        print("  - Title will still show on pages without navigation")
        print("\n✨ Result: Cleaner layout without redundant title when navigation tabs are present!")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
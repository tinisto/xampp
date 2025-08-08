#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload migrated pages
        files = [
            ("404-new.php", "404-new.php"),
            ("search-new.php", "search-new.php"),
            ("login-template.php", "login-template.php"),
            ("registration-new.php", "registration-new.php"),
            (".htaccess", ".htaccess")
        ]
        
        for local_file, remote_file in files:
            with open(f"/Applications/XAMPP/xamppfiles/htdocs/{local_file}", 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
                print(f"✅ Uploaded {remote_file}")
        
        print("\n🎯 MIGRATION BATCH 1 COMPLETE:")
        print("\n✅ MIGRATED PAGES:")
        print("  - 404 Error Page: /404-new.php")
        print("  - Search: /search-new.php") 
        print("  - Login: /login-template.php")
        print("  - Registration: /registration-new.php")
        print("\n✅ UPDATED ROUTES:")
        print("  - /404 → 404-new.php")
        print("  - /search → search-new.php")
        print("  - /login → login-template.php")
        print("  - /registration → registration-new.php")
        print("\n📊 PROGRESS:")
        print("  - Migrated: 4 pages")
        print("  - Remaining: 97 pages")
        print("\n🔗 TEST URLS:")
        print("  - https://11klassniki.ru/thispagedoesnotexist (404)")
        print("  - https://11klassniki.ru/search")
        print("  - https://11klassniki.ru/login")
        print("  - https://11klassniki.ru/registration")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
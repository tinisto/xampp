#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload migrated pages
        files = [
            ("error-new.php", "error-new.php"),
            ("forgot-password-new.php", "forgot-password-new.php"),
            (".htaccess", ".htaccess")
        ]
        
        for local_file, remote_file in files:
            with open(f"/Applications/XAMPP/xamppfiles/htdocs/{local_file}", 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
                print(f"✅ Uploaded {remote_file}")
        
        print("\n🎯 MIGRATION BATCH 2 COMPLETE:")
        print("\n✅ NEW MIGRATED PAGES:")
        print("  - Error Page: /error-new.php")
        print("  - Forgot Password: /forgot-password-new.php")
        print("\n✅ UPDATED ROUTES:")
        print("  - ErrorDocument 500 → error-new.php")
        print("  - /error → error-new.php")
        print("  - /forgot-password → forgot-password-new.php")
        print("\n📊 MIGRATION PROGRESS:")
        print("  - Previously migrated: 20 pages")
        print("  - Batch 1 migrated: 4 pages")
        print("  - Batch 2 migrated: 2 pages")
        print("  - Total migrated: 26 pages")
        print("  - Remaining: ~75 pages")
        print("\n✅ PAGES ALREADY USING real_template.php:")
        print("  - /about (about-new.php)")
        print("  - /write (write-new.php)")
        print("  - /news (news-new.php)")
        print("  - /post/* (post-new.php)")
        print("  - /category/* (category-new.php)")
        print("  - /tests (tests-new.php)")
        print("  - All VPO/SPO/School pages (*-new.php)")
        print("\n🔗 TEST URLS:")
        print("  - https://11klassniki.ru/error?code=500")
        print("  - https://11klassniki.ru/forgot-password")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
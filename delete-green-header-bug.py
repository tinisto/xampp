#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🗑️ DELETING GREEN HEADER BUG...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Delete the green header that's causing conflicts
        try:
            ftp.delete('common-components/header.php')
            print("✅ DELETED common-components/header.php (GREEN HEADER)")
        except:
            print("ℹ️  header.php not found or already deleted")
        
        # Also delete any other conflicting files
        files_to_delete = [
            'header.php',
            'common-components/footer.php', 
            'template.php',
            'navigation.php'
        ]
        
        for file_name in files_to_delete:
            try:
                ftp.delete(file_name)
                print(f"✅ DELETED {file_name}")
            except:
                print(f"ℹ️  {file_name} not found")
        
        ftp.quit()
        
        print("\n🎯 GREEN HEADER BUG ELIMINATED!")
        print("Now only BLUE header system remains")
        print("\nBoth pages should show identical headers:")
        print("• https://11klassniki.ru/news")
        print("• https://11klassniki.ru/category/abiturientam")
        print("\nClear browser cache (Ctrl+Shift+R) to see the fix!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
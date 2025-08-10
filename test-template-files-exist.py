#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 TESTING IF TEMPLATE FILES ACTUALLY EXIST")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Test files
        test_files = [
            'real_template.php',
            'common-components/real_header.php',
            'common-components/real_footer.php'
        ]
        
        for file_path in test_files:
            try:
                # Try to get file size
                size = ftp.size(file_path)
                print(f"   ✅ FOUND: {file_path} ({size} bytes)")
            except Exception as e:
                print(f"   ❌ MISSING: {file_path} - {str(e)}")
        
        # Also try to list root directory
        print(f"\n📂 Root directory contents:")
        try:
            files = ftp.nlst()
            template_files = [f for f in files if 'template' in f.lower()]
            print(f"   Template files in root: {template_files}")
        except Exception as e:
            print(f"   Error listing root: {str(e)}")
        
        # Try to list common-components
        print(f"\n📂 common-components directory contents:")
        try:
            files = ftp.nlst('common-components')
            template_files = [f for f in files if 'header' in f.lower() or 'footer' in f.lower()]
            print(f"   Header/footer files: {template_files}")
        except Exception as e:
            print(f"   Error listing common-components: {str(e)}")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Connection error: {str(e)}")

if __name__ == "__main__":
    main()
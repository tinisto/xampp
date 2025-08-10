#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🗑️ DELETING KNOWN TEMPLATE FILES")
    
    # Known template files to delete (from previous globbing)
    files_to_delete = [
        'dashboard-force-new-template.php',
        'dashboard-template.php', 
        'login-template.php',
        'temp_template.php',
        'template-debug-colors.php',
        'real_template_broken.php',
        'real_template_current.php',
        'real_template_fixed.php',
        'common-components/unified-template.php',
        'common-components/template-engine-ultimate.php',
        'spo-vpo-template.php',
        'spo-vpo-working-template.php',
        'school-working-template.php',
        'test-comment-system-with-template.php',
        'pages/registration/registration_template.php',
        'pages/registration/registration_template_fast.php'
    ]
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        deleted_count = 0
        for file_path in files_to_delete:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
                deleted_count += 1
            except:
                print(f"   ⚪ Not found: {file_path}")
        
        ftp.quit()
        
        print(f"\n📊 Deleted {deleted_count} template files")
        print(f"\n✅ Template cleanup completed!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
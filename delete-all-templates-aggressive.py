#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import time

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔥 AGGRESSIVE TEMPLATE DELETION")
    print("Deleting ALL template files found via directory listing")
    
    # ONLY keep these 3 files
    ESSENTIAL_FILES = [
        'real_template.php',
        'common-components/real_header.php', 
        'common-components/real_footer.php'
    ]
    
    # All template files found locally (that should be deleted from server)
    TEMPLATE_FILES_TO_DELETE = [
        'spo-vpo-working-template.php',
        'school-working-template.php',
        'spo-vpo-template.php',
        'dashboard-force-new-template.php',
        'pages/registration/registration_template_fast.php',
        'pages/registration/registration_template.php',
        'pages/common/educational-institutions-in-region/educational-institutions-in-region-template.php',
        'pages/search/search-process-template.php',
        'template-debug-colors.php',
        'common-components/unified-template.php',
        'real_template_broken.php',
        'real_template_current.php',
        'real_template_fixed.php',
        'login-template.php',
        'includes/form-template-fixed.php',
        'common-components/template-engine-ultimate.php',
        'dashboard-template.php',
        'test-comment-system-with-template.php',
        'temp_template.php',
        'pages/registration/activate_account/activate_account_template.php',
        'pages/registration/resend_activation/resend_activation_template.php',
        'pages/dashboard/comments-dashboard/comments-view/edit-comment/admin-comments-edit-template.php',
        'pages/account/comments-user/comments-user-edit/comments-user-edit-template.php'
    ]
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        print(f"🎯 Attempting to delete {len(TEMPLATE_FILES_TO_DELETE)} template files...")
        print(f"✅ Will keep only {len(ESSENTIAL_FILES)} essential files")
        
        deleted_count = 0
        not_found_count = 0
        
        for file_path in TEMPLATE_FILES_TO_DELETE:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
                deleted_count += 1
                time.sleep(0.05)  # Small delay
            except:
                print(f"   ⚪ Not found: {file_path}")
                not_found_count += 1
        
        ftp.quit()
        
        print(f"\n🔥 AGGRESSIVE CLEANUP COMPLETE!")
        print(f"📊 Files successfully deleted: {deleted_count}")
        print(f"📊 Files not found (already deleted): {not_found_count}")
        print(f"📊 Essential files kept: {len(ESSENTIAL_FILES)}")
        
        print(f"\n✅ Template system should now have ONLY these files:")
        for essential in ESSENTIAL_FILES:
            print(f"   • {essential}")
        
        if deleted_count > 0:
            print(f"\n🎯 SUCCESS: Deleted {deleted_count} duplicate template files!")
            print(f"🎯 Template chaos should be eliminated!")
        else:
            print(f"\n⚠️  All files were already deleted or not found")
            
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
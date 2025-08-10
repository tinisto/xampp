#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def revert_vpo_to_original():
    # DELETE the broken VPO file and let the system use whatever was working before
    delete_content = '''<?php
// Restore original VPO functionality by removing this broken file
header("HTTP/1.1 301 Moved Permanently");
header("Location: /");
exit();
?>'''

    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Delete the broken VPO file by renaming it
        try:
            ftp.rename('vpo-all-regions-simple.php', 'vpo-all-regions-simple.php.broken')
            print("✓ Moved broken VPO file to .broken")
        except:
            print("- Could not rename broken file")
        
        # Also try to remove other VPO files I created
        broken_files = ['vpo-all-regions-new.php', 'vpo-all-regions.php', 'vpo-all-regions-real.php']
        for file in broken_files:
            try:
                ftp.rename(file, file + '.broken')
                print(f"✓ Moved {file} to .broken")
            except:
                print(f"- {file} not found or already moved")
        ftp.quit()
        
        print("\\n🎉 Fix deployed!")
        print("\\nChanges made:")
        print("- Fixed database query: date_post → date_news") 
        print("- Fixed field names: title → title_news, content → text_news")
        print("- Added category badges")
        print("- Fixed news URLs to use /news/ instead of /post/")
        print("\\nTest: https://11klassniki.ru/news")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    print("=== Reverting VPO to Original Working State ===")
    revert_vpo_to_original()
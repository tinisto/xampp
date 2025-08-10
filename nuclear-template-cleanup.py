#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import time

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("💥 NUCLEAR TEMPLATE CLEANUP - DELETE EVERYTHING")
    print("Removing ALL template files except the 3 essential ones")
    
    # ONLY keep these 3 files
    KEEP_FILES = {
        'real_template.php',
        'common-components/real_header.php',
        'common-components/real_footer.php'
    }
    
    # All possible template file patterns
    TEMPLATE_PATTERNS = [
        'template', 'header', 'footer', 'Template', 'Header', 'Footer'
    ]
    
    # Directories to scan
    SCAN_DIRS = [
        '',  # root
        'common-components',
        'pages',
        'includes',
        'dashboard'
    ]
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        all_files_to_check = []
        
        print("🔍 Scanning directories for template files...")
        
        # Get all PHP files that might be templates
        def scan_directory(dir_path=""):
            try:
                files = []
                ftp.nlst(dir_path)
                for filename in ftp.nlst(dir_path):
                    if filename.endswith('.php'):
                        # Check if filename contains template patterns
                        filename_lower = filename.lower()
                        for pattern in TEMPLATE_PATTERNS:
                            if pattern in filename_lower:
                                full_path = f"{dir_path}/{filename}" if dir_path else filename
                                all_files_to_check.append(full_path)
                                break
            except:
                pass
        
        # Scan root directory
        try:
            for item in ftp.nlst():
                if item.endswith('.php'):
                    item_lower = item.lower()
                    for pattern in TEMPLATE_PATTERNS:
                        if pattern in item_lower:
                            all_files_to_check.append(item)
                            break
        except:
            pass
        
        # Scan subdirectories
        for subdir in ['common-components', 'pages', 'includes']:
            try:
                for item in ftp.nlst(subdir):
                    if '/' in item and item.endswith('.php'):
                        item_lower = item.lower()
                        for pattern in TEMPLATE_PATTERNS:
                            if pattern in item_lower:
                                all_files_to_check.append(item)
                                break
            except:
                pass
        
        print(f"📊 Found {len(all_files_to_check)} potential template files")
        
        # Filter out the files we want to keep
        files_to_delete = []
        files_to_keep = []
        
        for file_path in all_files_to_check:
            if file_path in KEEP_FILES:
                files_to_keep.append(file_path)
            else:
                files_to_delete.append(file_path)
        
        print(f"\n✅ KEEPING these {len(files_to_keep)} essential files:")
        for keep_file in files_to_keep:
            print(f"   • {keep_file}")
        
        print(f"\n🗑️ DELETING these {len(files_to_delete)} files:")
        
        deleted_count = 0
        for file_path in files_to_delete:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
                deleted_count += 1
                time.sleep(0.1)  # Small delay to avoid overwhelming server
            except Exception as e:
                print(f"   ❌ Failed to delete {file_path}: {str(e)}")
        
        ftp.quit()
        
        print(f"\n💥 NUCLEAR CLEANUP COMPLETE!")
        print(f"📊 Files deleted: {deleted_count}")
        print(f"📊 Files kept: {len(files_to_keep)}")
        
        print(f"\n🎯 NOW we should have ONLY {len(KEEP_FILES)} template files:")
        for keep_file in KEEP_FILES:
            print(f"   • {keep_file}")
            
        if deleted_count > 0:
            print(f"\n✅ Successfully cleaned up {deleted_count} duplicate template files!")
        else:
            print(f"\n⚠️  No files were deleted - they may have been cleaned up already")
            
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
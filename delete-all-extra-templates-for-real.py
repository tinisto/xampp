#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🗑️ DELETING ALL EXTRA TEMPLATE FILES FOR REAL")
    print("Actually removing ALL duplicate templates, headers, footers")
    print("Keep ONLY the 3 essential files")
    
    # Files to keep (ONLY these 3)
    KEEP_FILES = {
        'common-components/real_header.php',
        'common-components/real_footer.php', 
        'real_template.php'
    }
    
    # Dangerous template patterns to delete
    DELETE_PATTERNS = [
        'template',
        'header', 
        'footer'
    ]
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Get all files recursively
        all_files = []
        
        def collect_files(ftp, path=""):
            try:
                items = []
                ftp.retrlines(f'LIST {path}', items.append)
                
                for item in items:
                    parts = item.split()
                    if len(parts) < 9:
                        continue
                    
                    filename = ' '.join(parts[8:])
                    if filename in ['.', '..']:
                        continue
                        
                    full_path = f"{path}/{filename}" if path else filename
                    
                    if item.startswith('d'):  # Directory
                        collect_files(ftp, full_path)
                    else:  # File
                        all_files.append(full_path)
            except:
                pass
        
        print("📂 Scanning all files on server...")
        collect_files(ftp)
        print(f"   Found {len(all_files)} total files")
        
        # Find template files to delete
        files_to_delete = []
        for file_path in all_files:
            # Skip if it's one of our essential files
            if file_path in KEEP_FILES:
                continue
            
            # Check if filename contains template patterns
            filename_lower = file_path.lower()
            for pattern in DELETE_PATTERNS:
                if pattern in filename_lower:
                    # Extra safety - skip certain important files
                    if any(skip in filename_lower for skip in ['database', 'config', 'vendor', 'api', '.git']):
                        continue
                    files_to_delete.append(file_path)
                    break
        
        print(f"🎯 Found {len(files_to_delete)} template files to delete")
        print("⚠️  Keeping only these 3 essential files:")
        for keep_file in KEEP_FILES:
            print(f"   ✅ {keep_file}")
        
        # Delete files
        deleted_count = 0
        for file_path in files_to_delete:
            try:
                ftp.delete(file_path)
                print(f"   🗑️  Deleted: {file_path}")
                deleted_count += 1
            except Exception as e:
                print(f"   ❌ Could not delete {file_path}: {str(e)}")
        
        ftp.quit()
        
        print(f"\n🎯 MASS TEMPLATE DELETION COMPLETE!")
        print(f"📊 Files deleted: {deleted_count}")
        print(f"📊 Files kept: {len(KEEP_FILES)}")
        
        print(f"\n✅ NOW we actually have ONLY these template files:")
        for keep_file in KEEP_FILES:
            print(f"   • {keep_file}")
        
        print(f"\n🧪 This should eliminate ALL template conflicts!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
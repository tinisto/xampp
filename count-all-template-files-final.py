#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 COUNTING ALL TEMPLATE/HEADER/FOOTER FILES ON SERVER")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Collect all files from server
        all_files = []
        
        def scan_directory(path=""):
            try:
                items = ftp.nlst(path) if path else ftp.nlst()
                for item in items:
                    if item.startswith('.'):
                        continue
                    
                    full_path = f"{path}/{item}" if path else item
                    
                    # Check if it's a directory by trying to list it
                    try:
                        ftp.nlst(full_path)
                        # It's a directory, scan it recursively
                        if '/' not in item:  # Avoid infinite recursion
                            scan_directory(full_path)
                    except:
                        # It's a file
                        all_files.append(full_path)
            except Exception as e:
                pass
        
        print("📂 Scanning server directories...")
        scan_directory()
        
        print(f"📊 Total files found: {len(all_files)}")
        
        # Count template files
        template_files = []
        header_files = []
        footer_files = []
        
        for file_path in all_files:
            filename_lower = file_path.lower()
            
            if 'template' in filename_lower and file_path.endswith('.php'):
                template_files.append(file_path)
            
            if 'header' in filename_lower and file_path.endswith('.php'):
                header_files.append(file_path)
                
            if 'footer' in filename_lower and file_path.endswith('.php'):
                footer_files.append(file_path)
        
        print(f"\n🎯 FINAL COUNT:")
        print(f"📄 TEMPLATE files: {len(template_files)}")
        for f in template_files:
            print(f"   • {f}")
            
        print(f"\n🔝 HEADER files: {len(header_files)}")
        for f in header_files:
            print(f"   • {f}")
            
        print(f"\n🔻 FOOTER files: {len(footer_files)}")
        for f in footer_files:
            print(f"   • {f}")
        
        total_template_related = len(template_files) + len(header_files) + len(footer_files)
        print(f"\n📊 TOTAL template-related files: {total_template_related}")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
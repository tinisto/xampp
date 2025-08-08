#!/usr/bin/env python3
"""Final fix for homepage - rename conflicting files and check configuration"""

import ftplib
from datetime import datetime

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

def main():
    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected")
        
        # Upload the identify script first
        print("\nUploading diagnostic script...")
        with open('identify-homepage-file.php', 'rb') as f:
            ftp.storbinary('STOR identify-homepage-file.php', f)
        print("✓ Uploaded identify-homepage-file.php")
        
        # Get list of files
        print("\nChecking current files...")
        files = []
        ftp.retrlines('LIST', files.append)
        
        # Look for potential conflicting files
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        files_to_rename = []
        
        for file_line in files:
            parts = file_line.split()
            if len(parts) >= 9:
                filename = parts[-1]
                
                # Check for files that might interfere
                if filename in ['index-new.php', 'index-modern.php', 'index-fresh.php', 'index-real-test.php']:
                    files_to_rename.append(filename)
        
        # Rename conflicting files
        for filename in files_to_rename:
            new_name = f"{filename}.backup_{timestamp}"
            try:
                ftp.rename(filename, new_name)
                print(f"✓ Renamed {filename} to {new_name}")
            except Exception as e:
                print(f"✗ Could not rename {filename}: {e}")
        
        # Check if there's a default.php or home.php
        for alt_file in ['default.php', 'home.php', 'main.php']:
            if any(alt_file in line for line in files):
                try:
                    ftp.rename(alt_file, f"{alt_file}.backup_{timestamp}")
                    print(f"✓ Renamed {alt_file}")
                except:
                    pass
        
        # Upload our correct index.php again to ensure it's the latest
        print("\nRe-uploading index.php...")
        with open('index.php', 'rb') as f:
            ftp.storbinary('STOR index.php', f)
        print("✓ index.php uploaded")
        
        ftp.quit()
        
        print("\n" + "="*50)
        print("NEXT STEPS:")
        print("1. Visit https://11klassniki.ru/identify-homepage-file.php")
        print("2. Clear browser cache")
        print("3. Visit https://11klassniki.ru/")
        print("4. View page source and look for MARKER comments")
        print("="*50)
        
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())
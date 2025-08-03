#!/usr/bin/env python3
"""Find the correct web root path"""

import ftplib

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def find_web_root(ftp, current_dir="/"):
    """Recursively search for the web root containing index.php"""
    print(f"\n🔍 Checking directory: {current_dir}")
    
    try:
        ftp.cwd(current_dir)
        
        # Get list of files and directories
        items = []
        ftp.retrlines('LIST', items.append)
        
        # Check if index.php exists here
        for item in items:
            if 'index.php' in item:
                print(f"✅ Found index.php in {current_dir}")
                return current_dir
        
        # Check subdirectories
        for item in items:
            parts = item.split()
            if len(parts) >= 9 and item.startswith('d'):
                dir_name = ' '.join(parts[8:])
                if dir_name not in ['.', '..', '.Archived', 'cgi-bin']:
                    new_path = f"{current_dir}/{dir_name}" if current_dir != "/" else f"/{dir_name}"
                    result = find_web_root(ftp, new_path)
                    if result:
                        return result
                        
    except Exception as e:
        print(f"  ⚠️  Error checking {current_dir}: {e}")
    
    return None

def main():
    print("🚀 Finding correct web root...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Start from root
        web_root = find_web_root(ftp, "/")
        
        if web_root:
            print(f"\n✅ Web root found at: {web_root}")
            
            # Upload test file
            ftp.cwd(web_root)
            
            # Create a simple test file
            test_content = b"<?php echo 'Test file uploaded successfully!'; ?>"
            from io import BytesIO
            test_file = BytesIO(test_content)
            
            try:
                ftp.storbinary('STOR ftp_test_upload.php', test_file)
                print(f"✅ Test file uploaded to {web_root}/ftp_test_upload.php")
                print(f"🌐 Try accessing: https://11klassniki.ru/ftp_test_upload.php")
            except Exception as e:
                print(f"❌ Failed to upload test file: {e}")
        else:
            print("❌ Could not find web root")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())
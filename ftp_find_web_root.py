#!/usr/bin/env python3

import ftplib
import sys

# Correct FTP server details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def list_directory(ftp, path="/"):
    """List directory contents"""
    try:
        ftp.cwd(path)
        print(f"\n📂 Contents of {path}:")
        files = ftp.nlst()
        for file in files:
            print(f"  - {file}")
        return files
    except Exception as e:
        print(f"✗ Could not access {path}: {e}")
        return []

def upload_test_file(ftp, directory, filename="test.php"):
    """Upload a test file to check if it's the web root"""
    test_content = f"""<?php echo "TEST FILE - This is the web root directory: {directory}"; ?>"""
    
    try:
        ftp.cwd(directory)
        
        # Create test file locally
        with open(filename, 'w') as f:
            f.write(test_content)
        
        # Upload test file
        with open(filename, 'rb') as file:
            ftp.storbinary(f'STOR {filename}', file)
        
        print(f"✓ Uploaded test file to {directory}")
        
        # Clean up local file
        import os
        os.remove(filename)
        
        return True
    except Exception as e:
        print(f"✗ Failed to upload to {directory}: {e}")
        return False

def main():
    try:
        # Connect to FTP server
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected to FTP server")
        
        # Check root directory
        root_files = list_directory(ftp, "/")
        
        # I noticed there's a '11klassnikiru' directory - that might be the web root!
        if "11klassnikiru" in root_files:
            print("\n🎯 Found '11klassnikiru' directory - this might be the actual web root!")
            klassniki_files = list_directory(ftp, "/11klassnikiru")
            
            # Upload test file to 11klassnikiru directory
            if upload_test_file(ftp, "/11klassnikiru", "test-klassniki.php"):
                print("\n🔗 Test URL: https://11klassniki.ru/test-klassniki.php")
        
        # Also test public_html just in case
        if upload_test_file(ftp, "/public_html", "test-public.php"):
            print("\n🔗 Test URL: https://11klassniki.ru/test-public.php")
        
        # Check if there are existing PHP files in 11klassnikiru that we recognize
        try:
            ftp.cwd("/11klassnikiru")
            files = ftp.nlst()
            
            print(f"\n📄 Looking for familiar files in /11klassnikiru:")
            familiar_files = ["index.php", ".htaccess", "common-components", "pages", "includes"]
            
            for file in familiar_files:
                if file in files:
                    print(f"  ✓ Found: {file}")
                else:
                    print(f"  ✗ Missing: {file}")
                    
        except Exception as e:
            print(f"Could not check /11klassnikiru: {e}")
        
        print(f"\n💡 Theory:")
        print(f"The website 11klassniki.ru is probably served from /11klassnikiru/ not /public_html/")
        print(f"We need to upload files to the correct directory!")
        
    except Exception as e:
        print(f"✗ FTP error: {str(e)}")
        sys.exit(1)
    
    finally:
        try:
            ftp.quit()
            print("\n✓ FTP connection closed")
        except:
            pass

if __name__ == "__main__":
    main()
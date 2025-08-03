#!/usr/bin/env python3

import ftplib
import sys

# Correct FTP server details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f'✓ Uploaded: {local_path} -> {remote_path}')
        return True
    except Exception as e:
        print(f'✗ Failed to upload {local_path}: {str(e)}')
        return False

def main():
    try:
        # Connect to FTP server
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected to FTP server")
        
        # Navigate to the correct public_html directory
        ftp.cwd('/public_html')
        print("✓ Navigated to /public_html")
        
        # Check where .htaccess currently is
        print("\n🔍 Looking for existing .htaccess...")
        try:
            ftp.cwd('/')
            root_files = ftp.nlst()
            if ".htaccess" in root_files:
                print("✓ Found .htaccess in root directory")
        except:
            pass
            
        # Upload .htaccess to the correct location
        ftp.cwd('/public_html')
        print("\n📤 Uploading .htaccess to /public_html...")
        
        if upload_file(ftp, '.htaccess', '.htaccess'):
            print("✅ .htaccess uploaded successfully!")
            
            # Verify it's there
            files = ftp.nlst()
            if ".htaccess" in files:
                print("✓ Confirmed: .htaccess is now in /public_html")
            else:
                print("✗ .htaccess still not found in /public_html")
        
        print("\n🔗 Now test:")
        print("1. https://11klassniki.ru/news-test.php")
        print("2. https://11klassniki.ru/pages/news/news-main.php")
        print("3. https://11klassniki.ru/news")
        
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
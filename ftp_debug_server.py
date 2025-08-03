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

def main():
    try:
        # Connect to FTP server
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected to FTP server")
        
        # Check current directory
        current_dir = ftp.pwd()
        print(f"📍 Current directory: {current_dir}")
        
        # List root directory
        root_files = list_directory(ftp, "/")
        
        # Check if public_html exists
        if "public_html" in root_files:
            print("\n✓ Found public_html directory")
            public_files = list_directory(ftp, "/public_html")
            
            # Check for common-components
            if "common-components" in public_files:
                print("\n✓ Found common-components directory")
                list_directory(ftp, "/public_html/common-components")
            
            # Check for pages
            if "pages" in public_files:
                print("\n✓ Found pages directory")
                pages_files = list_directory(ftp, "/public_html/pages")
                
                # Check for news subdirectory
                if "news" in pages_files:
                    print("\n✓ Found pages/news directory")
                    list_directory(ftp, "/public_html/pages/news")
                else:
                    print("\n✗ pages/news directory NOT found")
            else:
                print("\n✗ pages directory NOT found")
                
        else:
            print("\n✗ public_html directory NOT found")
            # Maybe files should go in root?
            print("Checking if files should be in root directory...")
            
        # Check for .htaccess in public_html
        try:
            ftp.cwd("/public_html")
            files = ftp.nlst()
            if ".htaccess" in files:
                print("\n✓ .htaccess found in public_html")
            else:
                print("\n✗ .htaccess NOT found in public_html")
        except:
            print("\nCould not check for .htaccess")
            
        # Check what domain this FTP account serves
        print(f"\n🌐 This FTP account serves: 11klassniki.ru")
        print(f"📁 Files should be accessible at: https://11klassniki.ru/")
        
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
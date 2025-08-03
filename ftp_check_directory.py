#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def main():
    print("🔍 Checking FTP directory structure...")
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
        # Get current directory
        current_dir = ftp.pwd()
        print(f"\n📁 Current directory: {current_dir}")
        
        # List files in current directory
        print("\n📄 Files in current directory:")
        files = []
        ftp.retrlines('LIST', files.append)
        for file in files[:20]:  # Show first 20 files
            print(f"   {file}")
        
        # Check for common web directories
        print("\n🔍 Looking for web root directories...")
        possible_dirs = ['www', 'public_html', 'htdocs', 'html', 'public', 'web']
        
        for dir_name in possible_dirs:
            try:
                ftp.cwd(dir_name)
                print(f"✅ Found directory: {dir_name}")
                ftp.cwd('..')  # Go back
            except:
                pass
        
        # Try to find index.php
        print("\n🔍 Looking for index.php...")
        try:
            size = ftp.size('index.php')
            print(f"✅ Found index.php in current directory (size: {size} bytes)")
        except:
            print("❌ index.php not found in current directory")
            
        # Check if we need to go to a subdirectory
        print("\n🔍 Checking subdirectories...")
        try:
            ftp.cwd('www')
            print("✅ Changed to www directory")
            current_dir = ftp.pwd()
            print(f"📁 Now in: {current_dir}")
            
            # Check for index.php here
            try:
                size = ftp.size('index.php')
                print(f"✅ Found index.php in www directory (size: {size} bytes)")
            except:
                print("❌ index.php not found in www directory")
        except:
            print("❌ No www directory found")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
"""Test FTP connection to 11klassniki.ru"""

import ftplib
import socket

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

print(f"Testing FTP connection to {FTP_HOST}...")
print(f"User: {FTP_USER}")

try:
    # Set timeout
    socket.setdefaulttimeout(10)
    
    # Connect
    print("\n1. Connecting...")
    ftp = ftplib.FTP(FTP_HOST)
    print("✅ Connected to server")
    
    # Login
    print("\n2. Logging in...")
    ftp.login(FTP_USER, FTP_PASS)
    print("✅ Login successful")
    
    # Change directory
    print(f"\n3. Changing to directory: {FTP_DIR}")
    ftp.cwd(FTP_DIR)
    print("✅ Directory changed")
    
    # List files
    print("\n4. Listing files...")
    files = []
    ftp.retrlines('LIST', files.append)
    print(f"✅ Found {len(files)} items in directory")
    
    # Show first 5 files
    print("\nFirst 5 items:")
    for file in files[:5]:
        print(f"  {file}")
    
    # Close connection
    ftp.quit()
    print("\n✅ Connection test successful!")
    
except socket.timeout:
    print("❌ Connection timed out")
except ftplib.error_perm as e:
    print(f"❌ Permission error: {e}")
except Exception as e:
    print(f"❌ Error: {type(e).__name__}: {e}")
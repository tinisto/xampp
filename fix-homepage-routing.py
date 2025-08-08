#!/usr/bin/env python3
"""Fix homepage routing"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # First, let's check what files exist
    print("Files in root directory:")
    files = []
    ftp.retrlines('LIST', files.append)
    
    index_files = [f for f in files if 'index' in f.lower()]
    print("\nIndex files found:")
    for f in index_files:
        print(f)
    
    # Check if there's an index.html
    if any('index.html' in f for f in files):
        print("\n⚠️  Found index.html - this might be taking precedence over index.php")
        try:
            ftp.rename('index.html', 'index.html.backup')
            print("✓ Renamed index.html to index.html.backup")
        except:
            print("✗ Could not rename index.html")
    
    # Check for other variations
    for variant in ['index.htm', 'default.php', 'default.html']:
        if any(variant in f for f in files):
            print(f"\n⚠️  Found {variant}")
            try:
                ftp.rename(variant, f'{variant}.backup')
                print(f"✓ Renamed {variant} to {variant}.backup")
            except:
                print(f"✗ Could not rename {variant}")
    
    ftp.quit()
    
    print("\n✅ Done! Try visiting https://11klassniki.ru/ again")
    
except Exception as e:
    print(f"Error: {e}")
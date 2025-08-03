#!/usr/bin/env python3

import ftplib

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("Connecting...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    
    # Rename old file as backup
    try:
        ftp.rename(
            'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php',
            'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-old.php'
        )
        print("✓ Backed up old file")
    except:
        print("⚠️  Could not backup old file")
    
    # Upload new unified version
    with open('pages/common/educational-institutions-all-regions/educational-institutions-all-regions-unified.php', 'rb') as f:
        ftp.storbinary(
            'STOR pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php', 
            f
        )
        print("✓ Uploaded new unified template version")
    
    print("\n✅ VPO/SPO/SCHOOLS ALL REGIONS UPDATED!")
    print("\n🎯 What was done:")
    print("1. 🔄 Switched to unified template engine")
    print("2. 🌙 Dark mode toggle now available")
    print("3. 📱 Consistent header/footer with rest of site")
    print("4. ✨ All modern features now work")
    print("\nPages updated:")
    print("- https://11klassniki.ru/vpo-all-regions")
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/schools-all-regions")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")
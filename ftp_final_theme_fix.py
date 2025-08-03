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
    
    # Upload files
    with open('common-components/template-engine-unified.php', 'rb') as f:
        ftp.storbinary('STOR common-components/template-engine.php', f)
        print("✓ Template uploaded")
    
    with open('common-components/header.php', 'rb') as f:
        ftp.storbinary('STOR common-components/header.php', f)
        print("✓ Header uploaded")
    
    print("\n✅ FINAL THEME TOGGLE FIX DEPLOYED!")
    print("\n🎯 What was fixed:")
    print("1. 🖱️ Made button fully clickable (z-index: 1000)")
    print("2. 🎨 Added inline styles to override any conflicts")
    print("3. 🔍 Added console.log debugging")
    print("4. 🌙 Added emoji fallbacks if icons don't load")
    print("5. ✨ Simplified icon class updates")
    print("\n🧪 Check browser console (F12) for debug messages")
    print("📱 Button should now be clickable!")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")
#!/usr/bin/env python3

import ftplib

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("Connecting to FTP server...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    print("✓ Connected")
    
    # First backup the current template-engine.php
    try:
        with open('template-engine-backup.php', 'wb') as f:
            ftp.retrbinary('RETR common-components/template-engine.php', f.write)
        print("✓ Backed up current template-engine.php")
    except Exception as e:
        print(f"⚠️  Could not backup current template: {e}")
    
    # Deploy the unified template as the new main template
    files = [
        ('common-components/template-engine-unified.php', 'common-components/template-engine.php'),
    ]
    
    for local_file, remote_file in files:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
            print(f"✓ Deployed: {local_file} -> {remote_file}")
    
    print("\n✅ UNIFIED TEMPLATE WITH DARK MODE DEPLOYED!")
    print("\n🎯 Features added:")
    print("1. 🌙 Dark mode toggle (saves preference)")
    print("2. 🎨 Automatic theme switching")
    print("3. 📱 Responsive dark mode design")
    print("4. 🔄 Consolidated all template types:")
    print("   - Default layout (with header/footer)")
    print("   - Auth layout (centered, no header/footer)")
    print("   - Dashboard layout (with special styling)")
    print("   - Minimal layout (container only)")
    print("5. ⚡ Smooth transitions between themes")
    print("6. 💾 LocalStorage theme persistence")
    print("\n🌟 All pages now use ONE unified template!")
    print("🔘 Dark mode toggle appears in top-right corner")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")
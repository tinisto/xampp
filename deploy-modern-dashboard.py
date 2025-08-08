#!/usr/bin/env python3
import ftplib
import os
from datetime import datetime

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def backup_and_deploy():
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # First, rename the old dashboard as backup
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        old_file = 'dashboard-professional-new.php'
        backup_file = f'dashboard-professional-new.backup_{timestamp}.php'
        
        try:
            print(f"Creating backup: {backup_file}")
            ftp.rename(old_file, backup_file)
        except:
            print("Old dashboard not found or already backed up")
        
        # Upload the new modern dashboard
        print("Uploading modern dashboard...")
        with open('dashboard-modern-redesign.php', 'rb') as f:
            ftp.storbinary(f'STOR dashboard-professional-new.php', f)
        
        ftp.quit()
        
        print("✅ Modern Dashboard Deployed!")
        print("\n🎨 New Features:")
        print("   - Modern card-based design")
        print("   - Responsive grid layouts")
        print("   - Tablet-optimized (768px breakpoint)")
        print("   - Mobile-friendly (480px breakpoint)")
        print("   - Enhanced statistics visualization")
        print("   - Quick action cards")
        print("   - Activity feed with inline actions")
        print("   - Dark mode support")
        print("   - Improved edit/delete functionality")
        print("\n📱 Test at:")
        print("   https://11klassniki.ru/dashboard")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    response = input("Deploy modern dashboard? This will backup the old one. (yes/no): ")
    if response.lower() in ['yes', 'y']:
        backup_and_deploy()
    else:
        print("Deployment cancelled.")
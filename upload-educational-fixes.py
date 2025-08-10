#!/usr/bin/env python3
import ftplib
import os

# Update these credentials if needed
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'  # Update if different
FTP_PASS = 'JyvR!HK2E!N55Zt'  # Update if changed
FTP_ROOT = '/11klassnikiru'

def upload_fixes():
    """Upload the educational page fixes"""
    try:
        print(f"Connecting to {FTP_HOST} as {FTP_USER}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print("✓ Connected successfully")
        
        # Upload VPO fix
        print("Uploading VPO page fix...")
        with open('vpo-all-regions-new-fixed.php', 'rb') as f:
            ftp.storbinary('STOR vpo-all-regions-new.php', f)
        print("✓ VPO page updated")
        
        # Upload Schools fix  
        print("Uploading Schools page fix...")
        with open('schools-all-regions-real-fixed.php', 'rb') as f:
            ftp.storbinary('STOR schools-all-regions-real.php', f)
        print("✓ Schools page updated")
        
        ftp.quit()
        
        print("\n🎉 Upload successful!")
        print("\nThe pages should now show:")
        print("- ✓ Данные успешно загружены из базы данных")
        print("- Sample institutions from database")
        print("- No more 'Данные загружаются...' messages")
        print("\nTest URLs:")
        print("- https://11klassniki.ru/vpo-all-regions")
        print("- https://11klassniki.ru/schools-all-regions")
        
    except Exception as e:
        print(f"✗ Upload failed: {e}")
        print("\nPlease check:")
        print("1. FTP credentials are correct")
        print("2. Network connection is stable") 
        print("3. FTP server is accessible")

if __name__ == "__main__":
    print("=== Educational Pages Fix Upload ===")
    upload_fixes()
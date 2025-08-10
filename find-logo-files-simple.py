#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 FINDING ALL LOGO/FAVICON FILES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        all_logo_files = []
        
        # Check root directory
        root_files = ftp.nlst()
        for f in root_files:
            if any(ext in f for ext in ['.svg', '.png', '.ico', '.jpg']):
                if any(word in f.lower() for word in ['favicon', 'logo', 'icon']):
                    all_logo_files.append(f)
        
        # Check images directory
        try:
            img_files = ftp.nlst('images')
            for f in img_files:
                if 'logo' in f.lower() or 'icon' in f.lower():
                    all_logo_files.append(f)
        except:
            pass
        
        # Check common-components for logo.php
        try:
            cc_files = ftp.nlst('common-components')
            for f in cc_files:
                if 'logo' in f.lower():
                    all_logo_files.append(f)
        except:
            pass
        
        print(f"\n📊 ALL LOGO/FAVICON FILES FOUND:")
        for f in all_logo_files:
            print(f"   • {f}")
        
        print(f"\n📊 TOTAL: {len(all_logo_files)} logo files")
        
        if len(all_logo_files) > 1:
            print(f"\n⚠️ We have {len(all_logo_files)} logo files but should have only 1!")
            print(f"\n🗑️ Files to delete (keep only favicon.svg):")
            for f in all_logo_files:
                if f != 'favicon.svg':
                    print(f"   • {f}")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
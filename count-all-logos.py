#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 COUNTING ALL LOGO/FAVICON FILES ON SERVER")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        logo_files = []
        favicon_files = []
        image_logos = []
        
        # Check root directory
        try:
            root_files = ftp.nlst()
            for f in root_files:
                f_lower = f.lower()
                if 'favicon' in f_lower:
                    favicon_files.append(f)
                elif 'logo' in f_lower:
                    logo_files.append(f)
                elif any(ext in f_lower for ext in ['.ico', '.png', '.svg']) and any(word in f_lower for word in ['icon', 'logo']):
                    image_logos.append(f)
        except:
            pass
        
        # Check common directories
        dirs_to_check = ['images', 'img', 'assets', 'common-components', 'static']
        for dir_name in dirs_to_check:
            try:
                files = ftp.nlst(dir_name)
                for f in files:
                    if '/' in f:
                        f_lower = f.lower()
                        if 'favicon' in f_lower:
                            favicon_files.append(f)
                        elif 'logo' in f_lower:
                            logo_files.append(f)
            except:
                pass
        
        print(f"\n📊 LOGO/FAVICON COUNT:")
        
        print(f"\n🎨 FAVICON files: {len(favicon_files)}")
        for f in favicon_files:
            print(f"   • {f}")
        
        print(f"\n🏷️ LOGO files: {len(logo_files)}")
        for f in logo_files:
            print(f"   • {f}")
        
        print(f"\n🖼️ Other icon/logo images: {len(image_logos)}")
        for f in image_logos:
            print(f"   • {f}")
        
        total = len(favicon_files) + len(logo_files) + len(image_logos)
        print(f"\n📊 TOTAL logo-related files: {total}")
        
        # Check what's actually being used in header
        print(f"\n🔍 Checking what's in header file...")
        try:
            header_content = []
            ftp.retrlines('RETR common-components/real_header.php', lambda x: header_content.append(x))
            
            favicon_refs = []
            for line in header_content:
                if 'favicon' in line.lower() or 'icon' in line.lower():
                    favicon_refs.append(line.strip())
            
            if favicon_refs:
                print(f"\n📄 Favicon references in header:")
                for ref in favicon_refs[:5]:  # First 5 references
                    print(f"   • {ref}")
        except:
            pass
        
        ftp.quit()
        
        print(f"\n✅ We should have ONLY 1 logo/favicon file!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
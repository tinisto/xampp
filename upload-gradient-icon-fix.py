#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def main():
    print("GRADIENT ICON FIX")
    print("=" * 30)
    print("🔧 Changed form template to use gradient version only")
    print("   • Login page: will show green '11-классники' box")
    print("   • Registration: will show green '11-классники' box")
    print("   • Consistent with header/footer")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload updated form template
        with open("/Applications/XAMPP/xamppfiles/htdocs/includes/form-template-fixed.php", 'rb') as f:
            ftp.storbinary('STOR includes/form-template-fixed.php', f)
        print("✓ Uploaded: form-template-fixed.php")
        
        ftp.quit()
        
        print("\n🎉 GRADIENT ICON DEPLOYED!")
        print("✅ Login/registration now use gradient version")
        print("✅ Consistent across all pages")
        
        print("\n" + "=" * 30)
        print("TEST:")
        print("🔐 https://11klassniki.ru/login")
        print("📝 https://11klassniki.ru/registration")
        print("Should show green '11-классники' box!")
        print("=" * 30)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())
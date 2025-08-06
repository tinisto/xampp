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
    print("CACHE-BUSTING FIX")
    print("=" * 30)
    print("🔧 Adding cache-busting headers to form template")
    print("   • Forces browser to reload the page")
    print("   • Should show gradient icon immediately")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload updated form template with cache-busting
        with open("/Applications/XAMPP/xamppfiles/htdocs/includes/form-template-fixed.php", 'rb') as f:
            ftp.storbinary('STOR includes/form-template-fixed.php', f)
        print("✓ Uploaded: form-template-fixed.php (with cache-busting)")
        
        ftp.quit()
        
        print("\n🎉 CACHE-BUSTING DEPLOYED!")
        print("✅ Form template forces fresh reload")
        
        print("\n" + "=" * 30)
        print("TEST (should now show gradient icon):")
        print("🔐 https://11klassniki.ru/login")
        print("📝 https://11klassniki.ru/registration")
        print("\nTry hard refresh: Ctrl+F5 or Cmd+Shift+R")
        print("=" * 30)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())
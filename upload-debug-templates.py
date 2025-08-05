#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_debug():
    print("🚀 Uploading template debug tools")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload debug files
        with open('/Applications/XAMPP/xamppfiles/htdocs/debug-templates.php', 'rb') as f:
            ftp.storbinary('STOR debug-templates.php', f)
        print("✅ Template debug uploaded")
        
        with open('/Applications/XAMPP/xamppfiles/htdocs/test-minimal-school.php', 'rb') as f:
            ftp.storbinary('STOR test-minimal-school.php', f)
        print("✅ Minimal test template uploaded")
        
        ftp.quit()
        
        print("\n🎉 Debug tools ready:")
        print("🌐 Template debug: https://11klassniki.ru/debug-templates.php")
        print("🌐 Minimal test: https://11klassniki.ru/test-minimal-school.php?id=2718")
        print("\nThis will help identify if the issue is:")
        print("- Database connection problems")
        print("- Template file errors") 
        print("- Field name mismatches")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_debug()
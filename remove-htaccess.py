#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def remove_htaccess():
    print("🚀 Removing .htaccess to fix 500 error")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to dashboard directory
        ftp.cwd('dashboard')
        
        # Remove .htaccess
        try:
            ftp.delete('.htaccess')
            print("✅ .htaccess removed")
        except:
            print("⚠️  .htaccess not found or already removed")
        
        ftp.quit()
        
        print("\n🎉 .htaccess removed. Use the full URL:")
        print("   - https://11klassniki.ru/dashboard/comments.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Operation failed: {e}")
        return False

if __name__ == "__main__":
    remove_htaccess()
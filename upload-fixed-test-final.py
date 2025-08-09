#!/usr/bin/env python3
"""
Upload the fixed test file to replace the broken one
"""

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Uploading fixed test file...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Delete old broken test file
        try:
            ftp.delete("/test-comment-system.php")
            print("✅ Deleted old broken test file")
        except:
            print("ℹ️  Old test file already gone")
        
        # Upload the fixed version
        local_size = os.path.getsize("test-comment-system-fixed-final.php")
        print(f"📤 Uploading fixed version ({local_size} bytes)...")
        
        with open("test-comment-system-fixed-final.php", 'rb') as file:
            ftp.storbinary("STOR /test-comment-system.php", file)
        
        # Verify upload
        try:
            remote_size = ftp.size("/test-comment-system.php")
            print(f"✅ Upload successful! Remote size: {remote_size} bytes")
            
            if remote_size == local_size:
                print("✅ Size matches - upload verified")
            else:
                print("⚠️  Size mismatch - may need retry")
                
        except:
            print("ℹ️  Upload completed (size check not available)")
        
        # Close connection
        ftp.quit()
        
        print("\n🎉 Test file fixed and uploaded!")
        print("\n🧪 Test now at: https://11klassniki.ru/test-comment-system.php")
        print("\n✅ Fixed issues:")
        print("- ❌ Removed database query error on line 57")
        print("- ✅ Added proper error handling for all database queries") 
        print("- ✅ Included new blue favicon directly in HTML")
        print("- ✅ Added fallback data if posts table is missing")
        print("- ✅ Made all tests more robust")
        print("\n🎯 The page should now:")
        print("1. Load without PHP errors")
        print("2. Show the new blue '11' favicon")
        print("3. Display database status properly")
        print("4. Allow running comment system tests")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())
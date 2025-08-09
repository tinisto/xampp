#!/usr/bin/env python3
"""
Debug what's actually on the server and why favicon isn't working
"""

import ftplib
import os

# FTP Configuration  
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko" 
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 Debugging server structure and favicon issue...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # List all files in root
        print("\n📁 Files in root directory:")
        files = ftp.nlst()
        template_files = [f for f in files if 'template' in f.lower()]
        favicon_files = [f for f in files if 'favicon' in f.lower() or f.endswith('.ico')]
        test_files = [f for f in files if 'test' in f.lower()]
        
        print("Template files:")
        for f in template_files:
            print(f"  - {f}")
            
        print("Favicon files:")  
        for f in favicon_files:
            print(f"  - {f}")
            
        print("Test files:")
        for f in test_files:
            print(f"  - {f}")
        
        # Check if test-comment-system.php exists and what size
        try:
            size = ftp.size("/test-comment-system.php")
            print(f"\n✅ test-comment-system.php exists: {size} bytes")
            
            # Download first part to check content
            temp_file = "temp_test_file.php"
            with open(temp_file, 'wb') as f:
                def callback(data):
                    f.write(data)
                    if f.tell() > 1000:  # Stop after 1KB
                        raise Exception("Stop")
                try:
                    ftp.retrbinary('RETR /test-comment-system.php', callback)
                except:
                    pass
                    
            # Check first 500 chars
            with open(temp_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read(500)
                print("\\nFirst 500 characters of test file:")
                print(content)
                
            os.remove(temp_file)
            
        except Exception as e:
            print(f"❌ test-comment-system.php: {e}")
            
        # Check if dashboard monitoring exists
        try:
            size = ftp.size("/dashboard-monitoring.php")
            print(f"\\n✅ dashboard-monitoring.php exists: {size} bytes")
        except Exception as e:
            print(f"❌ dashboard-monitoring.php: {e}")
            
        # Check index.php to see what template it uses
        print("\\n📋 Checking index.php template:")
        try:
            temp_file = "temp_index.php"
            with open(temp_file, 'wb') as f:
                def callback(data):
                    f.write(data)
                    if f.tell() > 2000:  # Stop after 2KB
                        raise Exception("Stop")
                try:
                    ftp.retrbinary('RETR /index.php', callback)
                except:
                    pass
                    
            # Look for template includes and favicon
            with open(temp_file, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                
            if 'include' in content:
                print("Template includes found:")
                lines = content.split('\\n')
                for line in lines:
                    if 'include' in line and ('.php' in line or 'template' in line):
                        print(f"  {line.strip()}")
                        
            if 'favicon' in content.lower() or 'icon' in content.lower():
                print("\\nFavicon references found:")
                lines = content.split('\\n')
                for line in lines:
                    if 'favicon' in line.lower() or ('link' in line and 'icon' in line):
                        print(f"  {line.strip()}")
            else:
                print("❌ No favicon references found in index.php")
                
            os.remove(temp_file)
            
        except Exception as e:
            print(f"Error checking index.php: {e}")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())
#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        print("🔍 Searching for login processing files...")
        
        # Get all files
        files = []
        ftp.retrlines('LIST', files.append)
        
        # Look for login-related processing files
        login_files = []
        for file_info in files:
            filename = file_info.split()[-1] if file_info.split() else ""
            if 'login' in filename.lower() and ('process' in filename.lower() or '.php' in filename.lower()):
                login_files.append((filename, file_info))
        
        print(f"\n📁 Found {len(login_files)} login-related files:")
        for filename, file_info in login_files:
            print(f"  📄 {filename}")
        
        # Check pages directory structure
        print("\n🔍 Checking pages directory...")
        try:
            ftp.cwd(FTP_ROOT + '/pages')
            pages_files = []
            ftp.retrlines('LIST', pages_files.append)
            
            login_dirs = []
            for file_info in pages_files:
                filename = file_info.split()[-1] if file_info.split() else ""
                if 'login' in filename.lower() or 'auth' in filename.lower():
                    login_dirs.append(filename)
            
            print(f"📁 Login-related directories in /pages/:")
            for dirname in login_dirs:
                print(f"  📂 {dirname}")
                
                # Check contents of login directories
                try:
                    ftp.cwd(FTP_ROOT + f'/pages/{dirname}')
                    subfiles = []
                    ftp.retrlines('LIST', subfiles.append)
                    for subfile_info in subfiles:
                        subfilename = subfile_info.split()[-1] if subfile_info.split() else ""
                        if 'process' in subfilename.lower():
                            print(f"    📄 {subfilename}")
                    ftp.cwd(FTP_ROOT + '/pages')
                except:
                    pass
                    
        except Exception as e:
            print(f"❌ Could not access /pages directory: {e}")
        
        # Check what the current .htaccess is routing /login to
        print("\n🔍 Checking current .htaccess routing...")
        ftp.cwd(FTP_ROOT)
        htaccess_lines = []
        try:
            ftp.retrlines('RETR .htaccess', htaccess_lines.append)
            
            print("📋 Current login-related routes:")
            for i, line in enumerate(htaccess_lines):
                if 'login' in line.lower() and ('rewrite' in line.lower() or '=>' in line):
                    print(f"  {line.strip()}")
        except:
            print("❌ Could not read .htaccess")
        
        ftp.quit()
        
        print("\n💡 Next steps:")
        print("1. Verify login processing endpoint exists")
        print("2. Check if forms are pointing to correct processing URLs")
        print("3. Update form actions if needed")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()
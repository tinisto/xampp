#!/usr/bin/env python3
"""Check what files are already on the server"""

import ftplib

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

print("Checking server files...\n")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    
    # Get list of PHP files
    files = []
    ftp.retrlines('NLST *.php', files.append)
    
    print(f"Found {len(files)} PHP files in /{FTP_DIR}:\n")
    
    # Check for critical files
    critical_files = [
        'index.php',
        'index_modern.php',
        'router.php',
        '.htaccess',
        'home_modern.php'
    ]
    
    for cf in critical_files:
        if cf in files or cf == '.htaccess':
            try:
                size = ftp.size(cf)
                print(f"✅ {cf} - {size} bytes")
            except:
                print(f"❌ {cf} - NOT FOUND")
        else:
            print(f"❌ {cf} - NOT FOUND")
    
    # Check directories
    print("\nChecking directories:")
    dirs_to_check = ['database', 'config', 'includes', 'api']
    
    for d in dirs_to_check:
        try:
            ftp.cwd(d)
            ftp.cwd('..')
            print(f"✅ /{d}/ exists")
        except:
            print(f"❌ /{d}/ NOT FOUND")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")
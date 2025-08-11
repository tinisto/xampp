#!/usr/bin/env python3
"""
Deploy all PHP files to make the site work
Focused on getting the site functional quickly
"""

import ftplib
import os
import glob

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

print("""
╔══════════════════════════════════════════════════════╗
║    Deploying PHP files to /11klassnikiru folder      ║
╚══════════════════════════════════════════════════════╝
""")

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    print("✅ Connected to /11klassnikiru\n")
    
    uploaded = 0
    
    # 1. Upload all root PHP files
    print("📁 Uploading root PHP files...")
    for php_file in glob.glob("*.php"):
        if not php_file.startswith(('test-', 'import_', 'fill_', 'populate_', 'clean_')) and not php_file.endswith('_local.php'):
            try:
                with open(php_file, 'rb') as f:
                    ftp.storbinary(f'STOR {php_file}', f)
                uploaded += 1
                if uploaded % 5 == 0:
                    print(f"  ✅ {uploaded} files uploaded...")
            except Exception as e:
                print(f"  ❌ {php_file}: {e}")
    
    # 2. Create and upload critical directories
    critical_dirs = {
        'database': ['*.php'],
        'includes': ['*.php'],
        'config': ['*.php'],
        'api/v1': ['*.php'],
        'admin': ['*.php'],
        'tests': ['automated-tests.php']
    }
    
    for dir_path, patterns in critical_dirs.items():
        print(f"\n📁 Uploading {dir_path}/...")
        
        # Create directory
        try:
            if '/' in dir_path:
                parts = dir_path.split('/')
                for i in range(len(parts)):
                    partial = '/'.join(parts[:i+1])
                    try:
                        ftp.mkd(partial)
                    except:
                        pass
            else:
                ftp.mkd(dir_path)
        except:
            pass
        
        # Upload files
        for pattern in patterns:
            for file in glob.glob(os.path.join(dir_path, pattern)):
                if os.path.isfile(file) and not file.endswith('_local.php'):
                    try:
                        with open(file, 'rb') as f:
                            remote_path = file.replace('\\', '/')
                            ftp.storbinary(f'STOR {remote_path}', f)
                        uploaded += 1
                    except Exception as e:
                        print(f"  ❌ {file}: {e}")
    
    # 3. Upload .htaccess (critical for routing)
    print("\n📁 Uploading .htaccess...")
    if os.path.exists('.htaccess'):
        try:
            with open('.htaccess', 'rb') as f:
                ftp.storbinary('STOR .htaccess', f)
            print("  ✅ .htaccess uploaded")
            uploaded += 1
        except Exception as e:
            print(f"  ❌ .htaccess: {e}")
    
    # Close connection
    ftp.quit()
    
    print(f"""
╔══════════════════════════════════════════════════════╗
║                    Success!                          ║
╠══════════════════════════════════════════════════════╣
║  ✅ Uploaded {uploaded:>3} essential files                    ║
║  📁 To folder: /{FTP_DIR}                    ║
╚══════════════════════════════════════════════════════╝

🎉 Core PHP files deployed!
🌐 Your site should now work at: https://11klassniki.ru

If still seeing empty page, check:
1. Database connection in /config/
2. Error logs on server
3. File permissions
    """)
    
except Exception as e:
    print(f"❌ Error: {e}")
    print("\nTry using FTP client like FileZilla for manual upload.")
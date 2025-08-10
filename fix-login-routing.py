#!/usr/bin/env python3
"""
Fix login routing issue on 11klassniki.ru
"""

import ftplib
import os
import sys
from datetime import datetime
import tempfile

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def connect_ftp():
    """Connect to FTP server"""
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print(f"✓ Connected to FTP server")
        return ftp
    except Exception as e:
        print(f"✗ Failed to connect to FTP: {e}")
        sys.exit(1)

def check_login_files(ftp):
    """Check which login files exist"""
    print("\n=== Checking login files ===")
    
    login_files_to_check = [
        'login-standalone.php',  # What .htaccess is looking for
        'login.php',
        'login-new.php',
        'login-simple.php',
        'login-modern.php',
        'login-template.php'
    ]
    
    existing_file = None
    
    for file in login_files_to_check:
        try:
            size = ftp.size(file)
            if size is not None:
                print(f"  ✓ {file} exists (size: {size} bytes)")
                if not existing_file and file != 'login-standalone.php':
                    existing_file = file
        except:
            print(f"  ✗ {file} not found")
            if file == 'login-standalone.php':
                print("    ⚠️  THIS IS THE FILE .htaccess IS LOOKING FOR!")
    
    return existing_file

def fix_htaccess(ftp, login_file):
    """Fix the .htaccess to point to existing login file"""
    print(f"\n=== Fixing .htaccess to use {login_file} ===")
    
    # Download current .htaccess
    try:
        htaccess_lines = []
        ftp.retrlines('RETR .htaccess', htaccess_lines.append)
        print("✓ Downloaded current .htaccess")
    except Exception as e:
        print(f"✗ Failed to download .htaccess: {e}")
        return False
    
    # Fix the login routing line
    fixed_lines = []
    changes_made = False
    
    for line in htaccess_lines:
        if 'RewriteRule ^login/?$ login-standalone.php' in line:
            # Replace with correct file
            fixed_line = f'    RewriteRule ^login/?$ {login_file} [QSA,NC,L]'
            fixed_lines.append(fixed_line)
            changes_made = True
            print(f"  Changed: {line}")
            print(f"  To:      {fixed_line}")
        else:
            fixed_lines.append(line)
    
    if not changes_made:
        print("✗ Could not find login routing rule to fix!")
        return False
    
    # Write fixed .htaccess to temporary file
    with tempfile.NamedTemporaryFile(mode='w', delete=False) as f:
        f.write('\n'.join(fixed_lines))
        temp_file = f.name
    
    # Backup current .htaccess
    backup_name = f'.htaccess.backup-{datetime.now().strftime("%Y%m%d%H%M%S")}'
    try:
        ftp.rename('.htaccess', backup_name)
        print(f"✓ Backed up current .htaccess to {backup_name}")
    except Exception as e:
        print(f"✗ Failed to backup .htaccess: {e}")
        return False
    
    # Upload fixed .htaccess
    try:
        with open(temp_file, 'rb') as f:
            ftp.storbinary('STOR .htaccess', f)
        print("✓ Uploaded fixed .htaccess")
        
        # Clean up temp file
        os.unlink(temp_file)
        return True
    except Exception as e:
        print(f"✗ Failed to upload fixed .htaccess: {e}")
        # Try to restore backup
        try:
            ftp.rename(backup_name, '.htaccess')
            print("✓ Restored backup .htaccess")
        except:
            print("✗ Could not restore backup!")
        return False

def create_login_standalone(ftp):
    """Create login-standalone.php that redirects to existing login file"""
    print("\n=== Creating login-standalone.php redirect ===")
    
    redirect_content = """<?php
// Redirect to actual login file
header('Location: /login.php');
exit();
?>"""
    
    # Write to temporary file
    with tempfile.NamedTemporaryFile(mode='w', delete=False) as f:
        f.write(redirect_content)
        temp_file = f.name
    
    # Upload to server
    try:
        with open(temp_file, 'rb') as f:
            ftp.storbinary('STOR login-standalone.php', f)
        print("✓ Created login-standalone.php redirect")
        
        # Clean up temp file
        os.unlink(temp_file)
        return True
    except Exception as e:
        print(f"✗ Failed to create login-standalone.php: {e}")
        return False

def verify_fix(ftp):
    """Verify the fix worked"""
    print("\n=== Verifying fix ===")
    
    # Check login-standalone.php exists
    try:
        size = ftp.size('login-standalone.php')
        if size is not None:
            print(f"✓ login-standalone.php exists (size: {size} bytes)")
            return True
    except:
        pass
    
    print("✗ login-standalone.php still not found")
    return False

def main():
    print("=== Fixing 11klassniki.ru Login Routing ===")
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    ftp = connect_ftp()
    
    try:
        # Check which login files exist
        existing_login_file = check_login_files(ftp)
        
        if not existing_login_file:
            print("\n✗ ERROR: No login files found on server!")
            print("  You need to upload a login.php file first")
            return
        
        print(f"\n✓ Found existing login file: {existing_login_file}")
        
        # Option 1: Fix .htaccess to point to existing file
        print("\n=== Option 1: Fix .htaccess ===")
        if fix_htaccess(ftp, existing_login_file):
            print("✓ Successfully fixed .htaccess")
        else:
            # Option 2: Create login-standalone.php as redirect
            print("\n=== Option 2: Create redirect file ===")
            if create_login_standalone(ftp):
                print("✓ Successfully created redirect file")
                verify_fix(ftp)
            else:
                print("✗ Both fixes failed!")
        
        print("\n=== SUMMARY ===")
        print("The login routing should now work.")
        print("Test by visiting: https://11klassniki.ru/login/")
        
    finally:
        ftp.quit()
        print("\n✓ FTP connection closed")

if __name__ == "__main__":
    main()
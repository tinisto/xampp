#!/usr/bin/env python3
"""
Diagnose login routing issues on 11klassniki.ru
"""

import ftplib
import os
import sys
from datetime import datetime
import re

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

def download_htaccess(ftp):
    """Download current .htaccess file"""
    print("\n=== Downloading current .htaccess ===")
    try:
        # Download to memory
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        content = '\n'.join(htaccess_content)
        
        # Save locally for reference
        with open('server_htaccess_current.txt', 'w') as f:
            f.write(content)
        
        print(f"✓ Downloaded .htaccess ({len(htaccess_content)} lines)")
        return content
    except Exception as e:
        print(f"✗ Failed to download .htaccess: {e}")
        return None

def find_login_files(ftp):
    """Find all login-related files on server"""
    print("\n=== Searching for login-related files ===")
    login_files = []
    
    def search_directory(path=''):
        try:
            current_dir = FTP_ROOT + path
            ftp.cwd(current_dir)
            
            # Get file list
            files = []
            ftp.retrlines('LIST', lambda x: files.append(x))
            
            for file_info in files:
                parts = file_info.split()
                if len(parts) >= 9:
                    filename = ' '.join(parts[8:])
                    full_path = path + '/' + filename if path else filename
                    
                    # Skip . and ..
                    if filename in ['.', '..']:
                        continue
                    
                    # Check if it's a directory
                    if file_info.startswith('d'):
                        # Recursively search subdirectories (limit depth)
                        if path.count('/') < 3:  # Limit depth to avoid too deep recursion
                            search_directory(full_path)
                    else:
                        # Check if filename contains 'login'
                        if 'login' in filename.lower():
                            login_files.append(full_path)
                            print(f"  Found: {full_path}")
        except Exception as e:
            # Silently skip directories we can't access
            pass
    
    # Search from root
    search_directory()
    
    # Also check specific locations
    specific_checks = [
        'login.php',
        'login/index.php',
        'pages/login.php',
        'pages/login/index.php',
        'auth/login.php'
    ]
    
    print("\n=== Checking specific login file locations ===")
    for file_path in specific_checks:
        try:
            ftp.cwd(FTP_ROOT)
            # Try to get file size (will fail if doesn't exist)
            size = ftp.size(file_path)
            if size is not None:
                print(f"  ✓ {file_path} exists (size: {size} bytes)")
                if file_path not in login_files:
                    login_files.append(file_path)
        except:
            print(f"  ✗ {file_path} not found")
    
    return login_files

def analyze_htaccess(content):
    """Analyze .htaccess content for login routing"""
    print("\n=== Analyzing .htaccess login routing ===")
    
    if not content:
        print("✗ No .htaccess content to analyze")
        return
    
    lines = content.split('\n')
    login_rules = []
    rewrite_on = False
    
    for i, line in enumerate(lines):
        line = line.strip()
        
        # Check if RewriteEngine is on
        if line.lower().startswith('rewriteengine'):
            rewrite_on = 'on' in line.lower()
            print(f"  RewriteEngine: {'ON' if rewrite_on else 'OFF'}")
        
        # Find login-related rules
        if 'login' in line.lower():
            login_rules.append((i + 1, line))
    
    if login_rules:
        print(f"\n  Found {len(login_rules)} login-related rules:")
        for line_num, rule in login_rules:
            print(f"    Line {line_num}: {rule}")
    else:
        print("\n  ✗ No login-related rules found!")
    
    # Check for general rewrite rules that might affect login
    print("\n=== General rewrite rules that might affect /login/ ===")
    for i, line in enumerate(lines):
        line = line.strip()
        if line.startswith('RewriteRule') and not line.startswith('RewriteRule ^index\\.php'):
            print(f"  Line {i + 1}: {line}")
    
    # Check for conditions that might block login
    print("\n=== Conditions that might affect routing ===")
    current_conditions = []
    for i, line in enumerate(lines):
        line = line.strip()
        if line.startswith('RewriteCond'):
            current_conditions.append(line)
        elif line.startswith('RewriteRule') and current_conditions:
            print(f"  Conditions for rule at line {i + 1}:")
            for cond in current_conditions:
                print(f"    {cond}")
            print(f"    Rule: {line}")
            current_conditions = []

def check_server_config(ftp):
    """Check for other server configuration files"""
    print("\n=== Checking for other configuration files ===")
    
    config_files = [
        '.htaccess',
        'web.config',
        'nginx.conf',
        '.user.ini',
        'php.ini'
    ]
    
    for config_file in config_files:
        try:
            ftp.cwd(FTP_ROOT)
            size = ftp.size(config_file)
            if size is not None:
                print(f"  ✓ {config_file} exists (size: {size} bytes)")
        except:
            pass

def propose_fix(htaccess_content, login_files):
    """Propose a fix based on findings"""
    print("\n=== PROPOSED FIX ===")
    
    # Determine the correct login file path
    login_file = None
    if 'login.php' in login_files:
        login_file = 'login.php'
    elif 'pages/login.php' in login_files:
        login_file = 'pages/login.php'
    
    if not login_file:
        print("✗ ERROR: No login.php file found on server!")
        print("  You need to create login.php first")
        return
    
    print(f"\nDetected login file: {login_file}")
    print("\nAdd these rules to .htaccess (after RewriteEngine On):")
    print("```")
    print("# Login page routing - MUST be before other rules")
    print("RewriteRule ^login/?$ " + login_file + " [L,QSA]")
    print("RewriteRule ^login/(.*)$ " + login_file + "?action=$1 [L,QSA]")
    print("```")
    
    print("\nFull recommended .htaccess structure:")
    print("```")
    print("RewriteEngine On")
    print("")
    print("# Login page routing - MUST be first")
    print("RewriteRule ^login/?$ " + login_file + " [L,QSA]")
    print("RewriteRule ^login/(.*)$ " + login_file + "?action=$1 [L,QSA]")
    print("")
    print("# Existing rules should go here...")
    print("```")

def main():
    print("=== 11klassniki.ru Login Routing Diagnosis ===")
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    ftp = connect_ftp()
    
    try:
        # Download and analyze .htaccess
        htaccess_content = download_htaccess(ftp)
        
        # Find login files
        login_files = find_login_files(ftp)
        
        # Analyze htaccess
        if htaccess_content:
            analyze_htaccess(htaccess_content)
        
        # Check other configs
        check_server_config(ftp)
        
        # Propose fix
        propose_fix(htaccess_content, login_files)
        
        print("\n=== Summary ===")
        print(f"• .htaccess downloaded: {'Yes' if htaccess_content else 'No'}")
        print(f"• Login files found: {len(login_files)}")
        if login_files:
            for f in login_files:
                print(f"  - {f}")
        
    finally:
        ftp.quit()
        print("\n✓ FTP connection closed")

if __name__ == "__main__":
    main()
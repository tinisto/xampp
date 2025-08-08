#!/usr/bin/env python3
"""Fix PHP syntax error in real_template.php"""

import ftplib
import os

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Fixing real_template.php syntax error...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Download current real_template.php
    print("Downloading current real_template.php...")
    with open('real_template_broken.php', 'wb') as f:
        ftp.retrbinary('RETR real_template.php', f.write)
    
    # Read and fix the file
    with open('real_template_broken.php', 'r') as f:
        content = f.read()
    
    # Remove the HTML comment markers that are breaking PHP
    content = content.replace('<!-- REAL_TEMPLATE_MARKER: This is real_template.php being served -->', '')
    content = content.replace('<!-- REAL_COMPONENTS_MARKER: This is real_components.php being served -->', '')
    
    # Make sure it starts with <?php properly
    if not content.strip().startswith('<?php'):
        # Find where <?php should be
        php_start = content.find('<?php')
        if php_start > 0:
            content = content[php_start:]
    
    # Save fixed version
    with open('real_template_fixed.php', 'w') as f:
        f.write(content)
    
    # Upload fixed version
    print("Uploading fixed real_template.php...")
    with open('real_template_fixed.php', 'rb') as f:
        ftp.storbinary('STOR real_template.php', f)
    
    # Also fix real_components.php
    print("Downloading real_components.php...")
    with open('real_components_broken.php', 'wb') as f:
        ftp.retrbinary('RETR real_components.php', f.write)
    
    with open('real_components_broken.php', 'r') as f:
        comp_content = f.read()
    
    comp_content = comp_content.replace('<!-- REAL_COMPONENTS_MARKER: This is real_components.php being served -->', '')
    
    if not comp_content.strip().startswith('<?php'):
        php_start = comp_content.find('<?php')
        if php_start > 0:
            comp_content = comp_content[php_start:]
    
    with open('real_components_fixed.php', 'w') as f:
        f.write(comp_content)
    
    print("Uploading fixed real_components.php...")
    with open('real_components_fixed.php', 'rb') as f:
        ftp.storbinary('STOR real_components.php', f)
    
    # Upload our clean local versions as backup
    if os.path.exists('real_template.php'):
        print("Uploading clean local real_template.php...")
        with open('real_template.php', 'rb') as f:
            ftp.storbinary('STOR real_template.php', f)
    
    if os.path.exists('real_components.php'):
        print("Uploading clean local real_components.php...")
        with open('real_components.php', 'rb') as f:
            ftp.storbinary('STOR real_components.php', f)
    
    ftp.quit()
    
    print("\n✅ Fixed PHP syntax errors!")
    print("Now try https://11klassniki.ru/ again")
    
except Exception as e:
    print(f"Error: {e}")
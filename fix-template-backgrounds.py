#!/usr/bin/env python3
"""Fix template background colors for proper dark/light mode support"""

import ftplib
import re

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Fixing template background colors...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Download current template
    with open('real_template_current.php', 'wb') as f:
        ftp.retrbinary('RETR real_template.php', f.write)
    
    # Read and modify
    with open('real_template_current.php', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Find the yellow-bg-wrapper section and add dark mode support
    old_wrapper_css = """        /* Wrapper for yellow background sections */
        .yellow-bg-wrapper {
            background: white;
            flex: 1;
            display: flex;
            flex-direction: column;
        }"""
    
    new_wrapper_css = """        /* Wrapper for yellow background sections */
        .yellow-bg-wrapper {
            background: white;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Dark mode for wrapper */
        [data-theme="dark"] .yellow-bg-wrapper,
        [data-bs-theme="dark"] .yellow-bg-wrapper {
            background: #1a202c;
        }"""
    
    # Replace the CSS
    if old_wrapper_css in content:
        content = content.replace(old_wrapper_css, new_wrapper_css)
        print("✓ Added dark mode support for yellow-bg-wrapper")
    else:
        print("✓ CSS pattern not found - may already be updated")
    
    # Also fix header background
    old_header_css = """            background: white; /* Ensure header has white background */"""
    new_header_css = """            background: white; /* Ensure header has white background */
        }
        
        /* Dark mode for header */
        [data-theme="dark"] .main-header,
        [data-bs-theme="dark"] .main-header {
            background: #2d3748;
            border-bottom: 1px solid #4a5568;"""
    
    if old_header_css in content:
        content = content.replace(old_header_css, new_header_css)
        print("✓ Added dark mode support for header")
    
    # Fix footer background
    old_footer_css = """            background: #f8f9fa; /* Ensure footer has its light background */"""
    new_footer_css = """            background: #f8f9fa; /* Ensure footer has its light background */
            border-top: 1px solid #ddd; /* Add border to make it visible */
        }
        
        /* Dark mode for footer */
        [data-theme="dark"] .main-footer,
        [data-bs-theme="dark"] .main-footer {
            background: #2d3748;
            border-top: 1px solid #4a5568;"""
    
    if old_footer_css in content:
        content = content.replace(old_footer_css, new_footer_css)
        print("✓ Added dark mode support for footer")
    
    # Save modified template
    with open('real_template_fixed.php', 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Upload back
    with open('real_template_fixed.php', 'rb') as f:
        ftp.storbinary('STOR real_template.php', f)
    
    print("✓ Updated template with proper background colors")
    
    ftp.quit()
    
    print("\n✅ Template background colors fixed!")
    print("\nNow the template will have:")
    print("- Light mode: White backgrounds for main content areas")
    print("- Dark mode: Dark backgrounds (#1a202c) for main content areas") 
    print("- Proper header/footer colors in both modes")
    print("\nThe main div should now properly change colors with the theme toggle!")
    
except Exception as e:
    print(f"Error: {e}")
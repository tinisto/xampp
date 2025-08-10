#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 ANALYZING HEADER FOR CONDITIONAL LOGIC CAUSING DIFFERENT MENUS")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Read the single header file
        header_content = []
        ftp.retrlines('RETR common-components/real_header.php', header_content.append)
        
        print(f"📄 Header file: {len(header_content)} lines")
        
        # Look for suspicious conditional logic
        print("\n🔍 SCANNING FOR CONDITIONAL MENU LOGIC:")
        
        conditionals_found = []
        for i, line in enumerate(header_content):
            line_lower = line.lower()
            if any(keyword in line_lower for keyword in ['if (', 'isset(', '$_GET', '$_POST', 'dropdown', 'navigation']):
                conditionals_found.append((i+1, line.strip()))
        
        if conditionals_found:
            print("   ⚠️  CONDITIONAL LOGIC FOUND:")
            for line_num, line_text in conditionals_found[:10]:  # First 10
                print(f"     Line {line_num}: {line_text}")
        else:
            print("   ❌ No conditional logic found")
        
        # Look for the dropdown menu section specifically
        print("\n🔍 SEARCHING FOR DROPDOWN/EDUCATION MENU:")
        
        dropdown_lines = []
        for i, line in enumerate(header_content):
            if any(keyword in line.lower() for keyword in ['dropdown', 'образование', 'education', 'nav-item']):
                dropdown_lines.append((i+1, line.strip()))
        
        if dropdown_lines:
            print("   📋 DROPDOWN MENU CODE:")
            for line_num, line_text in dropdown_lines:
                print(f"     Line {line_num}: {line_text}")
        else:
            print("   ❌ No dropdown menu found")
        
        # Check if there are different versions or includes
        print("\n🔍 CHECKING FOR INCLUDES OR DYNAMIC CONTENT:")
        
        includes_found = []
        for i, line in enumerate(header_content):
            if any(keyword in line.lower() for keyword in ['include', 'require', 'file_get_contents']):
                includes_found.append((i+1, line.strip()))
        
        if includes_found:
            print("   📂 INCLUDES FOUND:")
            for line_num, line_text in includes_found:
                print(f"     Line {line_num}: {line_text}")
        
        # Final diagnosis
        print(f"\n🎯 DIAGNOSIS:")
        if conditionals_found:
            print("✅ FOUND THE PROBLEM: Header contains conditional logic")
            print("   → Different menu items show based on page context")
            print("   → This explains different headers on different pages")
        elif dropdown_lines:
            print("⚠️  POSSIBLE ISSUE: Found dropdown menu code")
            print("   → May have CSS or JavaScript issues hiding/showing menu")
        else:
            print("❌ MYSTERY: Header appears static but shows different content")
            print("   → Possible server-side caching or routing issue")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 AUDITING TEMPLATE SYSTEM - ENSURING SINGLE FILES ONLY")
    print("Goal: Verify only ONE header, ONE footer, ONE favicon system exists")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Find ALL files on server related to templates
        print("\n1. 🔍 SCANNING ENTIRE SERVER FOR TEMPLATE FILES...")
        
        all_files = []
        
        # Scan root directory
        root_files = []
        ftp.retrlines('LIST', root_files.append)
        for file_line in root_files:
            filename = file_line.split()[-1] if file_line.split() else ""
            if filename and filename.endswith('.php'):
                all_files.append(('/', filename))
        
        # Scan common-components directory
        try:
            ftp.cwd('common-components')
            component_files = []
            ftp.retrlines('LIST', component_files.append)
            for file_line in component_files:
                filename = file_line.split()[-1] if file_line.split() else ""
                if filename and filename.endswith('.php'):
                    all_files.append(('/common-components/', filename))
            ftp.cwd('..')
        except:
            print("   ℹ️  common-components directory checked")
        
        # Filter for template-related files
        template_keywords = ['header', 'footer', 'template', 'nav', 'menu', 'favicon']
        template_files = []
        favicon_files = []
        
        for directory, filename in all_files:
            if any(keyword in filename.lower() for keyword in template_keywords):
                if 'favicon' in filename.lower():
                    favicon_files.append(directory + filename)
                else:
                    template_files.append(directory + filename)
        
        # Also scan for favicon files (svg, ico, png)
        for directory, filename in all_files:
            if any(ext in filename.lower() for ext in ['favicon.ico', 'favicon.png', 'favicon.svg']):
                if filename not in [f.split('/')[-1] for f in favicon_files]:
                    favicon_files.append(directory + filename)
        
        print(f"\\n📊 AUDIT RESULTS:")
        print(f"   Total template files found: {len(template_files)}")
        print(f"   Total favicon files found: {len(favicon_files)}")
        
        # 2. Categorize files
        print("\\n2. 📋 CATEGORIZING FILES...")
        
        headers = [f for f in template_files if 'header' in f.lower()]
        footers = [f for f in template_files if 'footer' in f.lower()]
        templates = [f for f in template_files if 'template' in f.lower() and 'header' not in f.lower() and 'footer' not in f.lower()]
        navs = [f for f in template_files if any(nav in f.lower() for nav in ['nav', 'menu']) and 'header' not in f.lower()]
        
        print(f"\\n📁 HEADER FILES ({len(headers)}):")
        for f in headers:
            print(f"   📄 {f}")
        
        print(f"\\n📁 FOOTER FILES ({len(footers)}):")
        for f in footers:
            print(f"   📄 {f}")
        
        print(f"\\n📁 TEMPLATE FILES ({len(templates)}):")
        for f in templates:
            print(f"   📄 {f}")
        
        print(f"\\n📁 NAVIGATION FILES ({len(navs)}):")
        for f in navs:
            print(f"   📄 {f}")
        
        print(f"\\n📁 FAVICON FILES ({len(favicon_files)}):")
        for f in favicon_files:
            print(f"   📄 {f}")
        
        # 3. Identify what should be kept vs deleted
        print("\\n3. 🎯 IDENTIFYING REQUIRED FILES...")
        
        keep_files = [
            '/common-components/real_header.php',
            '/common-components/real_footer.php', 
            '/real_template.php',
            '/favicon.svg'
        ]
        
        print("\\n✅ FILES TO KEEP (REQUIRED):")
        for f in keep_files:
            exists = f in template_files or f in favicon_files
            status = "✅ EXISTS" if exists else "❌ MISSING"
            print(f"   {status} {f}")
        
        # 4. Identify files to delete
        delete_files = []
        
        # Headers to delete (keep only real_header.php)
        for f in headers:
            if f != '/common-components/real_header.php':
                delete_files.append(f)
        
        # Footers to delete (keep only real_footer.php)  
        for f in footers:
            if f != '/common-components/real_footer.php':
                delete_files.append(f)
        
        # Favicon files to delete (keep only favicon.svg)
        for f in favicon_files:
            if f != '/favicon.svg':
                delete_files.append(f)
        
        # Extra templates and nav files
        for f in templates + navs:
            if f not in keep_files:
                delete_files.append(f)
        
        print(f"\\n❌ FILES TO DELETE ({len(delete_files)}):")
        for f in delete_files[:10]:  # Show first 10
            print(f"   🗑️  {f}")
        if len(delete_files) > 10:
            print(f"   ... and {len(delete_files) - 10} more files")
        
        # 5. DELETE THE EXTRA FILES
        print(f"\\n5. 🗑️  DELETING DUPLICATE FILES...")
        
        deleted_count = 0
        failed_count = 0
        
        for file_path in delete_files:
            try:
                # Handle file path (remove leading slash for FTP)
                ftp_path = file_path.lstrip('/')
                ftp.delete(ftp_path)
                print(f"   ✅ Deleted {file_path}")
                deleted_count += 1
            except Exception as e:
                print(f"   ❌ Failed to delete {file_path}: {str(e)}")
                failed_count += 1
        
        ftp.quit()
        
        print(f"\\n✅ TEMPLATE SYSTEM AUDIT COMPLETE!")
        
        print(f"\\n📊 FINAL RESULTS:")
        print(f"   Files analyzed: {len(template_files) + len(favicon_files)}")
        print(f"   Files deleted: {deleted_count}")
        print(f"   Failed deletions: {failed_count}")
        print(f"   Files remaining: {len(keep_files)}")
        
        print(f"\\n🎯 SINGLE TEMPLATE SYSTEM:")
        print(f"   ✅ ONE header: /common-components/real_header.php")
        print(f"   ✅ ONE footer: /common-components/real_footer.php") 
        print(f"   ✅ ONE template: /real_template.php")
        print(f"   ✅ ONE favicon: /favicon.svg")
        
        print(f"\\n🧪 TEST CONSISTENCY NOW:")
        print(f"   • https://11klassniki.ru/news")
        print(f"   • https://11klassniki.ru/category/abiturientam")
        print(f"   (Should have IDENTICAL headers, footers, favicons)")
        
        print(f"\\n💡 Clear browser cache (Ctrl+Shift+R) to see unified design!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 SIMPLE COUNT OF TEMPLATE FILES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check specific known locations
        template_files = []
        header_files = []
        footer_files = []
        
        # Check root directory for templates
        try:
            root_files = ftp.nlst()
            for f in root_files:
                if 'template' in f.lower() and f.endswith('.php'):
                    template_files.append(f)
                if 'header' in f.lower() and f.endswith('.php'):
                    header_files.append(f)
                if 'footer' in f.lower() and f.endswith('.php'):
                    footer_files.append(f)
        except:
            pass
        
        # Check common-components directory
        try:
            cc_files = ftp.nlst('common-components')
            for f in cc_files:
                if 'template' in f.lower() and f.endswith('.php'):
                    template_files.append(f)
                if 'header' in f.lower() and f.endswith('.php'):
                    header_files.append(f)
                if 'footer' in f.lower() and f.endswith('.php'):
                    footer_files.append(f)
        except:
            pass
        
        print(f"📄 TEMPLATE files: {len(template_files)}")
        for f in template_files:
            print(f"   • {f}")
        
        print(f"\n🔝 HEADER files: {len(header_files)}")
        for f in header_files:
            print(f"   • {f}")
        
        print(f"\n🔻 FOOTER files: {len(footer_files)}")
        for f in footer_files:
            print(f"   • {f}")
        
        total = len(template_files) + len(header_files) + len(footer_files)
        print(f"\n📊 TOTAL: {total} template-related files")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
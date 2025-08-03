#!/usr/bin/env python3
"""
Rename old template files on production server to disable them
"""

import ftplib
from datetime import datetime

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

# Old templates to rename on server
old_templates = [
    'common-components/template-engine.php',
    'common-components/template-engine-no-header.php',
    'common-components/template-engine-modern.php',
    'common-components/template-engine-dashboard.php',
    'common-components/template-engine-no-bootstrap.php',
    'common-components/template-engine-unified.php',
    'common-components/template-engine-old.php',
    'common-components/template-engine-vpo-spo.php',
    'common-components/template-engine-search.php',
    'common-components/template-engine-nofollow.php',
]

def rename_remote_file(ftp, old_name, new_name):
    """Rename a file on FTP server"""
    try:
        ftp.rename(old_name, new_name)
        print(f"✅ Renamed: {old_name}")
        print(f"   → {new_name}")
        return True
    except Exception as e:
        if "550" in str(e):
            print(f"⚠️  File not found: {old_name}")
        else:
            print(f"❌ Failed to rename {old_name}: {e}")
        return False

def main():
    print("🔄 Disabling old templates on production server...")
    print("   Renaming with .old suffix\n")
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully\n")
        
        # Rename each template
        success_count = 0
        date_suffix = datetime.now().strftime("%Y%m%d")
        
        for template in old_templates:
            new_name = f"{template}.old-{date_suffix}"
            if rename_remote_file(ftp, template, new_name):
                success_count += 1
            print()
        
        print(f"📊 Production Server Results:")
        print(f"   ✅ Successfully renamed: {success_count}/{len(old_templates)}")
        print(f"   ❌ Failed/Not found: {len(old_templates) - success_count}/{len(old_templates)}")
        
        if success_count > 0:
            print("\n🎉 Old templates disabled on production!")
            print("   Only template-engine-ultimate.php is now active")
            print("   The ONE TEMPLATE system is fully enforced!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")
        print("\n📝 Manual steps:")
        print("1. Connect to FTP server")
        print("2. Navigate to common-components/")
        print("3. Rename all template-engine-*.php files")
        print("4. Add .old suffix to disable them")
        print("5. Keep only template-engine-ultimate.php active")

if __name__ == "__main__":
    main()
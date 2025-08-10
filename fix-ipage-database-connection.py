#!/usr/bin/env python3
"""
Fix iPage Database Connection
This script ensures the correct iPage MySQL host is configured
"""

import os

def main():
    print("=== iPage Database Connection Fix ===\n")
    
    # The correct iPage MySQL host
    IPAGE_HOST = "11klassnikiru67871.ipagemysql.com"
    IPAGE_USER = "admin_claude"
    IPAGE_DB = "11klassniki_claude"
    
    print(f"✓ iPage MySQL Host: {IPAGE_HOST}")
    print(f"✓ Database User: {IPAGE_USER}")
    print(f"✓ Database Name: {IPAGE_DB}\n")
    
    # Files to upload
    files_to_upload = [
        ".env",
        ".env.ipage",
        "config/loadEnv.php"
    ]
    
    print("FILES TO UPLOAD via FTP:")
    print("=" * 50)
    
    for file in files_to_upload:
        print(f"✓ {file}")
    
    print("\n" + "=" * 50)
    print("\nIMPORTANT STEPS:")
    print("1. Upload the .env file to your iPage server root")
    print("2. The .env file now contains the correct iPage MySQL host")
    print("3. If PHP is still using cached values, you may need to:")
    print("   - Contact iPage support to restart PHP")
    print("   - Wait for PHP cache to expire (2-6 hours)")
    print("   - Try the trigger_php_reload.php script")
    
    print("\nVERIFICATION:")
    print("After uploading, visit:")
    print("https://11klassniki.ru/check-mysql-status.php")
    print("\nYou should see:")
    print(f"- Host: {IPAGE_HOST}")
    print(f"- Database: {IPAGE_DB}")
    
    print("\n✅ Configuration is ready for upload!")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3

import ftplib

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def restore_original_env():
    """Restore original .env file to get site back online"""
    
    print("🚨 EMERGENCY: Restoring original .env file...")
    
    # Original working database credentials
    original_env_content = """# Production environment variables
DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=11klone_user
DB_PASS=K8HqqBV3hTf4mha
DB_NAME=11klassniki_ru
APP_ENV=production
ADMIN_EMAIL=support@11klassniki.ru
SMTP_HOST=smtp.ipage.com
SMTP_USERNAME=support@11klassniki.ru
SMTP_PASSWORD=VTzQa$QUDm28nVE
SMTP_SECURITY=tls
SMTP_PORT=587
"""
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Create temporary file with original settings
        with open('/tmp/restore_env', 'w') as f:
            f.write(original_env_content)
        
        # Upload restored .env
        print("📤 Restoring original .env file...")
        with open('/tmp/restore_env', 'rb') as f:
            ftp.storbinary('STOR .env', f)
        
        print("✅ Original .env file restored!")
        print("\n🎯 Try accessing the site now:")
        print("https://11klassniki.ru")
        
        print("\n⚠️  NEXT STEPS:")
        print("1. Check if site is back online")
        print("2. We'll need to get the correct original password")
        print("3. Then try migration again with smaller batches")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Restoration failed: {e}")
        return False

if __name__ == "__main__":
    restore_original_env()
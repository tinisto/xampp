#!/usr/bin/env python3

import ftplib
import os
from datetime import datetime

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_php_reload():
    """Upload PHP reload trigger files"""
    
    print("📤 Uploading PHP reload trigger files...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload .user.ini
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/.user.ini'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR .user.ini', f)
            print("✅ .user.ini uploaded!")
        
        # Upload trigger script
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/trigger_php_reload.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR trigger_php_reload.php', f)
            print("✅ trigger_php_reload.php uploaded!")
        
        # Also touch .htaccess to trigger reload
        try:
            # Download current .htaccess
            import tempfile
            with tempfile.NamedTemporaryFile(delete=False) as tmp:
                ftp.retrbinary('RETR .htaccess', tmp.write)
                tmp_path = tmp.name
            
            # Add a comment with timestamp and re-upload
            with open(tmp_path, 'r') as f:
                content = f.read()
            
            # Add or update timestamp comment
            timestamp = f"# Last modified: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
            if '# Last modified:' in content:
                lines = content.split('\n')
                for i, line in enumerate(lines):
                    if line.startswith('# Last modified:'):
                        lines[i] = timestamp.strip()
                        break
                content = '\n'.join(lines)
            else:
                content = timestamp + content
            
            with open(tmp_path, 'w') as f:
                f.write(content)
            
            with open(tmp_path, 'rb') as f:
                ftp.storbinary('STOR .htaccess', f)
            print("✅ .htaccess touched with new timestamp!")
            
            os.unlink(tmp_path)
        except Exception as e:
            print(f"⚠️  Could not touch .htaccess: {e}")
        
        ftp.quit()
        
        print("\n🔄 Trigger PHP reload: https://11klassniki.ru/trigger_php_reload.php")
        print("\n⏰ What happens next:")
        print("1. Visit the trigger URL above")
        print("2. Wait 1-2 minutes for PHP to pick up changes")
        print("3. Test again: https://11klassniki.ru/test_new_structure.php")
        print("\nIf this doesn't work within 5 minutes, contact iPage support.")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_php_reload()
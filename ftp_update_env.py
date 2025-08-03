#!/usr/bin/env python3

import ftplib
import tempfile
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def update_live_env():
    """Update live .env file with new database credentials"""
    
    print("🚀 Updating Live .env File...")
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # First, backup the current .env file
        print("💾 Backing up current .env file...")
        try:
            with open('env_backup.txt', 'wb') as backup_file:
                ftp.retrbinary('RETR .env', backup_file.write)
            print("✅ .env file backed up as env_backup.txt")
        except Exception as e:
            print(f"⚠️  Could not backup .env file: {e}")
        
        # Download current .env to modify it
        print("📥 Downloading current .env file...")
        with tempfile.NamedTemporaryFile(mode='w+b', delete=False) as temp_file:
            try:
                ftp.retrbinary('RETR .env', temp_file.write)
                temp_file_path = temp_file.name
            except Exception as e:
                print(f"⚠️  Could not download .env file: {e}")
                # Create new .env file with basic structure
                temp_file_path = temp_file.name
                temp_file.write(b"# Database Configuration\n")
        
        # Read and modify the .env content
        with open(temp_file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Update database credentials
        lines = content.split('\n')
        new_lines = []
        db_settings_added = {
            'DB_HOST': False,
            'DB_NAME': False, 
            'DB_USER': False,
            'DB_PASS': False
        }
        
        for line in lines:
            if line.startswith('DB_HOST='):
                new_lines.append('DB_HOST=localhost')
                db_settings_added['DB_HOST'] = True
            elif line.startswith('DB_NAME='):
                new_lines.append('DB_NAME=11klassniki_new')
                db_settings_added['DB_NAME'] = True
            elif line.startswith('DB_USER='):
                new_lines.append('DB_USER=admin_claude')
                db_settings_added['DB_USER'] = True
            elif line.startswith('DB_PASS='):
                new_lines.append('DB_PASS=Secure9#Klass')
                db_settings_added['DB_PASS'] = True
            else:
                new_lines.append(line)
        
        # Add missing database settings
        if not db_settings_added['DB_HOST']:
            new_lines.append('DB_HOST=localhost')
        if not db_settings_added['DB_NAME']:
            new_lines.append('DB_NAME=11klassniki_new')
        if not db_settings_added['DB_USER']:
            new_lines.append('DB_USER=admin_claude')
        if not db_settings_added['DB_PASS']:
            new_lines.append('DB_PASS=Secure9#Klass')
        
        # Write the updated content
        updated_content = '\n'.join(new_lines)
        with open(temp_file_path, 'w', encoding='utf-8') as f:
            f.write(updated_content)
        
        # Upload the updated .env file
        print("📤 Uploading updated .env file...")
        with open(temp_file_path, 'rb') as f:
            ftp.storbinary('STOR .env', f)
        
        print("✅ .env file updated successfully!")
        print("\n🎯 New database credentials:")
        print("   DB_NAME=11klassniki_new")
        print("   DB_USER=admin_claude")
        print("   DB_PASS=Secure9#Klass")
        
        print("\n🚀 Next step: Run the migration:")
        print("   https://11klassniki.ru/migrate-database-data.php")
        
        # Cleanup
        os.unlink(temp_file_path)
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Update failed: {e}")
        return False

if __name__ == "__main__":
    update_live_env()
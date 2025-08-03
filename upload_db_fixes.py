#!/usr/bin/env python3
import ftplib
from pathlib import Path

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
WEB_ROOT = '/11klassnikiru'

print("Uploading database connection fixes...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    print('✅ Connected to FTP')
    
    # Upload .env.production
    ftp.cwd(WEB_ROOT)
    with open('.env.production', 'rb') as f:
        ftp.storbinary('STOR .env.production', f)
    print('✅ Uploaded: .env.production')
    
    # Upload config files
    ftp.cwd(WEB_ROOT + '/config')
    
    config_files = ['loadEnv.php', 'loadEnv_production.php', 'loadEnv_unified.php']
    
    for file in config_files:
        file_path = f'config/{file}'
        if Path(file_path).exists():
            with open(file_path, 'rb') as f:
                ftp.storbinary(f'STOR {file}', f)
            print(f'✅ Uploaded: {file}')
    
    ftp.quit()
    print('\n✅ All database connection files updated!')
    print('\nThe site should now use the new database (11klassniki_claude) everywhere.')
    print('\nChanges made:')
    print('1. Updated .env.production to use new database')
    print('2. Updated loadEnv.php to work without Composer and use new database')
    print('3. Updated loadEnv_production.php fallbacks to use new database')
    print('4. Created loadEnv_unified.php as a backup')
    
except Exception as e:
    print(f'❌ Error: {e}')
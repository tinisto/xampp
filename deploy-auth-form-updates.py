#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

# Files to deploy
files_to_deploy = [
    'registration-modern.php',
    'forgot-password.php'
]

try:
    print("🚀 Deploying authentication form updates...")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    for file in files_to_deploy:
        if os.path.exists(file):
            with open(file, 'rb') as f:
                ftp.storbinary(f'STOR {file}', f)
                print(f'✅ Updated {file}')
        else:
            print(f'❌ File not found: {file}')
    
    ftp.quit()
    print('\n🎉 All authentication forms now use consistent site icons!')
    print('\nTest the updated forms at:')
    print('- https://11klassniki.ru/login')
    print('- https://11klassniki.ru/forgot-password') 
    print('- https://11klassniki.ru/registration')
    
except Exception as e:
    print(f"Error: {e}")
#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    # Upload the fixed file
    with open('forgot-password-standalone.php', 'rb') as f:
        ftp.storbinary('STOR forgot-password-standalone.php', f)
        print('✅ Updated forgot-password-standalone.php')
    
    # Upload debug script
    with open('debug-password-reset.php', 'rb') as f:
        ftp.storbinary('STOR debug-password-reset.php', f)
        print('✅ Uploaded debug script')
    
    # Upload table creation script
    with open('create-password-resets-table.php', 'rb') as f:
        ftp.storbinary('STOR create-password-resets-table.php', f)
        print('✅ Uploaded table creation script')
    
    # Upload simple table creation script
    with open('create-simple-password-resets-table.php', 'rb') as f:
        ftp.storbinary('STOR create-simple-password-resets-table.php', f)
        print('✅ Uploaded simple table creation script')
    
    # Upload user check script
    with open('check-user-exists.php', 'rb') as f:
        ftp.storbinary('STOR check-user-exists.php', f)
        print('✅ Uploaded user check script')
    
    # Upload database structure check
    with open('check-database-structure.php', 'rb') as f:
        ftp.storbinary('STOR check-database-structure.php', f)
        print('✅ Uploaded database structure check')
    
    # Upload final user lookup test
    with open('test-user-lookup.php', 'rb') as f:
        ftp.storbinary('STOR test-user-lookup.php', f)
        print('✅ Uploaded user lookup test')
    
    # Upload direct email test
    with open('test-reset-email-direct.php', 'rb') as f:
        ftp.storbinary('STOR test-reset-email-direct.php', f)
        print('✅ Uploaded direct email test')
    
    # Upload actual process test
    with open('test-actual-process.php', 'rb') as f:
        ftp.storbinary('STOR test-actual-process.php', f)
        print('✅ Uploaded actual process test')
    
    # Upload trace form issue test
    with open('trace-form-issue.php', 'rb') as f:
        ftp.storbinary('STOR trace-form-issue.php', f)
        print('✅ Uploaded trace form issue test')
    
    # Upload simple trace
    with open('simple-trace.php', 'rb') as f:
        ftp.storbinary('STOR simple-trace.php', f)
        print('✅ Uploaded simple trace')
    
    ftp.quit()
    print('🚀 Password reset fix deployed!')
    
except Exception as e:
    print(f"Error: {e}")
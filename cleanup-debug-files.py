#!/usr/bin/env python3
import ftplib

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

debug_files = [
    'debug-password-reset.php',
    'create-password-resets-table.php',
    'create-simple-password-resets-table.php',
    'check-user-exists.php',
    'check-database-structure.php',
    'test-user-lookup.php',
    'test-reset-email-direct.php',
    'test-actual-process.php',
    'trace-form-issue.php',
    'simple-trace.php'
]

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("🧹 Cleaning up debug files...")
    
    for file in debug_files:
        try:
            ftp.delete(file)
            print(f"✅ Deleted {file}")
        except ftplib.error_perm:
            print(f"⚠️  {file} not found (already removed)")
    
    ftp.quit()
    print("\n🎉 Cleanup complete! Password reset system is fully functional.")
    
except Exception as e:
    print(f"Error: {e}")
#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Updating .htaccess for proper standalone page routing...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download current .htaccess
        print("📥 Downloading .htaccess...")
        htaccess_content = []
        ftp.retrlines('RETR .htaccess', htaccess_content.append)
        
        # Update routes
        updated_htaccess = []
        changes = 0
        
        for line in htaccess_content:
            # Update login route to use login-template.php
            if 'RewriteRule ^login/?$ login-template.php' in line:
                # Already correct from previous fix
                updated_htaccess.append(line)
            # Update registration route
            elif 'RewriteRule ^registration/?$ registration-' in line and 'standalone' not in line:
                updated_htaccess.append('    RewriteRule ^registration/?$ registration-standalone.php [QSA,NC,L]')
                print(f"✅ Updated registration route")
                changes += 1
            # Update privacy route  
            elif 'RewriteRule ^privacy/?$ privacy-' in line and 'standalone' not in line:
                updated_htaccess.append('    RewriteRule ^privacy/?$ privacy-standalone.php [QSA,NC,L]')
                print(f"✅ Updated privacy route")
                changes += 1
            # Update forgot-password route
            elif 'RewriteRule ^forgot-password/?$ forgot-password-' in line and 'standalone' not in line:
                updated_htaccess.append('    RewriteRule ^forgot-password/?$ forgot-password-standalone.php [QSA,NC,L]')
                print(f"✅ Updated forgot-password route")
                changes += 1
            else:
                updated_htaccess.append(line)
        
        if changes > 0:
            # Save and upload
            with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                tmp.write('\n'.join(updated_htaccess))
                tmp_path = tmp.name
            
            print("📤 Uploading updated .htaccess...")
            with open(tmp_path, 'rb') as file:
                ftp.storbinary('STOR /.htaccess', file)
            
            os.unlink(tmp_path)
            print(f"✅ Updated {changes} routes in .htaccess")
        else:
            print("✅ All routes already correctly configured")
        
        ftp.quit()
        
        print("\n✅ Standalone page routing completed!")
        print("\n📋 Summary:")
        print("• /login/ → login-template.php (with correct form action)")
        print("• /registration/ → registration-standalone.php")
        print("• /privacy/ → privacy-standalone.php") 
        print("• /forgot-password/ → forgot-password-standalone.php")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()
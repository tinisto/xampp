#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

try:
    print("🏠 Deploying Homepage Cookie Banner")
    print("=================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    # Upload updated ultimate template
    with open('common-components/template-engine-ultimate.php', 'rb') as f:
        ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print('✅ Updated template-engine-ultimate.php')
    
    ftp.quit()
    
    print("\n🎉 Homepage cookie banner deployed!")
    print("\n🔗 Now test on the main homepage:")
    print("https://11klassniki.ru")
    print("\n📋 You should now see:")
    print("- Cookie consent banner in Russian")
    print("- 'Принять все' and 'Только необходимые' buttons")
    print("- Link to privacy policy")
    print("- Banner should persist until user chooses")
    
    print("\n🍪 After accepting cookies:")
    print("- Theme switching should work in incognito mode")
    print("- Cookies will have proper security attributes")
    print("- Banner won't show again for 1 year")
    
except Exception as e:
    print(f"❌ Error: {e}")
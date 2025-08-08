import ftplib
from datetime import datetime

# FTP credentials
ftp_host = "ftp.ipage.com"
ftp_user = "franko"
ftp_pass = "JyvR!HK2E!N55Zt"
ftp_path = "/11klassnikiru"

try:
    # Connect to FTP
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(ftp_path)
    
    print(f"Connected to FTP server")
    print("-" * 50)
    
    # Upload fixed real_title.php
    with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/real_title.php', 'rb') as f:
        ftp.cwd(ftp_path + '/common-components')
        ftp.storbinary('STOR real_title.php', f)
        print("✓ Uploaded common-components/real_title.php (with dark mode subtitle fix)")
    
    # Go back to root
    ftp.cwd(ftp_path)
    
    # Upload updated category-working.php with dark mode styles
    with open('/Applications/XAMPP/xamppfiles/htdocs/category-working.php', 'rb') as f:
        ftp.storbinary('STOR category-working.php', f)
        print("✓ Uploaded category-working.php (with dark mode styles)")
    
    print("-" * 50)
    print("✅ Dark mode fixes uploaded successfully!")
    print("\nThe following improvements have been made:")
    print("1. Title component now properly shows white text in dark mode")
    print("2. Subtitle text (like '3 статей') now visible in dark mode")
    print("3. Category pages have proper dark mode styles")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
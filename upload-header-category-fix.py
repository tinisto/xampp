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
    
    # Upload fixed header
    with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/real_header.php', 'rb') as f:
        ftp.cwd(ftp_path + '/common-components')
        ftp.storbinary('STOR real_header.php', f)
        print("✓ Uploaded common-components/real_header.php")
    
    print("-" * 50)
    print("✅ Header category fix uploaded successfully!")
    print("\nThe following improvements have been made:")
    print("1. Categories link now points to /categories-all instead of #")
    print("2. Dropdown still works on first click")
    print("3. Second click navigates to categories listing page")
    print("4. Mobile behavior remains unchanged (dropdown only)")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
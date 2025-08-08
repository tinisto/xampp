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
    
    # Go back to root
    ftp.cwd(ftp_path)
    
    # Upload fixed homepage
    with open('/Applications/XAMPP/xamppfiles/htdocs/index.php', 'rb') as f:
        ftp.storbinary('STOR index.php', f)
        print("✓ Uploaded index.php")
    
    print("-" * 50)
    print("✅ Hash prevention fix uploaded successfully!")
    print("\nThe following improvements have been made:")
    print("1. Categories dropdown click always prevents # navigation")
    print("2. All links with href='#' are prevented from navigating")
    print("3. Homepage automatically removes any # from URL")
    print("4. No more # appearing in URLs when clicking dropdowns")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
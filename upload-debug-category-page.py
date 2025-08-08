import ftplib
import os
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
    
    print(f"Connected to FTP server: {ftp_host}")
    
    # Upload debug file
    local_file = "/Applications/XAMPP/xamppfiles/htdocs/debug-category-page.php"
    with open(local_file, 'rb') as file:
        ftp.storbinary('STOR debug-category-page.php', file)
        file_size = os.path.getsize(local_file)
        print(f"✓ Uploaded: debug-category-page.php ({file_size} bytes)")
    
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
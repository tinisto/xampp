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
    
    # Download category.php from server
    remote_file = "/pages/category/category.php"
    local_file = "/Applications/XAMPP/xamppfiles/htdocs/category-from-server.php"
    
    with open(local_file, 'wb') as file:
        ftp.retrbinary(f'RETR {remote_file}', file.write)
        print(f"✓ Downloaded: {remote_file} to category-from-server.php")
    
    # Get file size
    file_size = os.path.getsize(local_file)
    print(f"File size: {file_size} bytes")
    
    print(f"Download completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
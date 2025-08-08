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
    print(f"Current directory: {ftp.pwd()}")
    print("-" * 50)
    
    # Upload fixed category.php
    local_file = "/Applications/XAMPP/xamppfiles/htdocs/pages/category/category.php"
    remote_file = "/pages/category/category.php"
    
    with open(local_file, 'rb') as file:
        response = ftp.storbinary(f'STOR {remote_file}', file)
        file_size = os.path.getsize(local_file)
        print(f"Upload response: {response}")
        print(f"✓ Uploaded: category.php -> {remote_file} ({file_size} bytes)")
    
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
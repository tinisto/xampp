import ftplib
import os
from datetime import datetime

# FTP credentials
ftp_host = "ftp.ipage.com"
ftp_user = "franko"
ftp_pass = "JyvR!HK2E!N55Zt"
ftp_path = "/11klassnikiru"

# Files to upload
files_to_upload = [
    (".htaccess", "/.htaccess"),
    ("debug-category.php", "/debug-category.php")
]

try:
    # Connect to FTP
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(ftp_path)
    
    print(f"Connected to FTP server: {ftp_host}")
    print(f"Current directory: {ftp.pwd()}")
    print("-" * 50)
    
    # Upload each file
    for local_file, remote_file in files_to_upload:
        try:
            local_path = os.path.join("/Applications/XAMPP/xamppfiles/htdocs", local_file)
            with open(local_path, 'rb') as file:
                ftp.storbinary(f'STOR {remote_file}', file)
                file_size = os.path.getsize(local_path)
                print(f"✓ Uploaded: {local_file} -> {remote_file} ({file_size} bytes)")
                
        except Exception as e:
            print(f"✗ Failed to upload {local_file}: {str(e)}")
    
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
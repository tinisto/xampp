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
    ("category-new.php", "/category-new.php"),
    ("pages/category/category.php", "/pages/category/category.php"),
    ("common-components/category-navigation.php", "/common-components/category-navigation.php"),
    ("categories-all.php", "/categories-all.php")
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
            # For files in subdirectories, ensure the directory exists
            if "/" in remote_file:
                remote_dir = os.path.dirname(remote_file)
                # Try to create directory structure
                dirs = remote_dir.split('/')
                current_path = ""
                for dir_name in dirs:
                    if dir_name:
                        current_path = current_path + "/" + dir_name if current_path else dir_name
                        try:
                            ftp.mkd(current_path)
                            print(f"Created directory: {current_path}")
                        except:
                            pass  # Directory might already exist
            
            # Upload the file
            local_path = os.path.join("/Applications/XAMPP/xamppfiles/htdocs", local_file)
            if os.path.exists(local_path):
                with open(local_path, 'rb') as file:
                    ftp.storbinary(f'STOR {remote_file}', file)
                    file_size = os.path.getsize(local_path)
                    print(f"✓ Uploaded: {local_file} -> {remote_file} ({file_size} bytes)")
            else:
                print(f"✗ File not found: {local_file}")
                
        except Exception as e:
            print(f"✗ Failed to upload {local_file}: {str(e)}")
    
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
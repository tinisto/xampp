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
    
    # First, try to delete the old file
    try:
        ftp.delete("/pages/category/category.php")
        print("✓ Deleted old category.php")
    except:
        print("⚠ Could not delete old file (may not exist)")
    
    # Upload the correct category.php
    local_file = "/Applications/XAMPP/xamppfiles/htdocs/pages/category/category.php"
    remote_file = "/pages/category/category.php"
    
    with open(local_file, 'rb') as file:
        response = ftp.storbinary(f'STOR {remote_file}', file)
        file_size = os.path.getsize(local_file)
        print(f"✓ Uploaded: category.php -> {remote_file} ({file_size} bytes)")
        print(f"  Response: {response}")
    
    # Verify the upload
    try:
        server_size = ftp.size(remote_file)
        print(f"✓ Verified: Server file size = {server_size} bytes")
        if server_size == file_size:
            print("✓ Size matches!")
        else:
            print(f"⚠ Size mismatch! Local: {file_size}, Server: {server_size}")
    except Exception as e:
        print(f"⚠ Could not verify: {e}")
    
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
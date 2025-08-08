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
    "rename-category-id.php",
    "debug-category-enhanced.php",
    "debug-category.php"  # Also re-upload the basic debug file
]

try:
    # Connect to FTP
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(ftp_path)
    
    print(f"Connected to FTP server: {ftp_host}")
    print(f"Current directory: {ftp.pwd()}")
    print("-" * 50)
    
    # List current files to check
    print("Checking for existing files:")
    files_list = []
    ftp.retrlines('LIST', files_list.append)
    
    for filename in files_to_upload:
        found = False
        for line in files_list:
            if filename in line:
                print(f"  Found on server: {filename}")
                found = True
                break
        if not found:
            print(f"  NOT found on server: {filename}")
    
    print("-" * 50)
    
    # Upload each file
    for filename in files_to_upload:
        try:
            local_path = os.path.join("/Applications/XAMPP/xamppfiles/htdocs", filename)
            
            if os.path.exists(local_path):
                with open(local_path, 'rb') as file:
                    response = ftp.storbinary(f'STOR {filename}', file)
                    file_size = os.path.getsize(local_path)
                    print(f"✓ Uploaded: {filename} ({file_size} bytes)")
                    print(f"  Response: {response}")
                    
                # Verify upload
                try:
                    server_size = ftp.size(filename)
                    if server_size == file_size:
                        print(f"  ✓ Verified: Size matches ({server_size} bytes)")
                    else:
                        print(f"  ⚠ Warning: Size mismatch - Local: {file_size}, Server: {server_size}")
                except:
                    print(f"  ⚠ Could not verify size")
                    
            else:
                print(f"✗ Local file not found: {filename}")
                
        except Exception as e:
            print(f"✗ Failed to upload {filename}: {str(e)}")
    
    print("-" * 50)
    
    # List files again to confirm
    print("Files in root directory after upload:")
    files_list = []
    ftp.retrlines('LIST *.php', files_list.append)
    for line in files_list[-5:]:  # Show last 5 PHP files
        print(f"  {line}")
    
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
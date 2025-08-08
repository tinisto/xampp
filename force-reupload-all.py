import ftplib
import os
from datetime import datetime

# FTP credentials
ftp_host = "ftp.ipage.com"
ftp_user = "franko"
ftp_pass = "JyvR!HK2E!N55Zt"
ftp_path = "/11klassnikiru"

# Files to force reupload
files_to_upload = [
    ("pages/category/category.php", "/pages/category/category.php"),
    ("common-components/cards-grid.php", "/common-components/cards-grid.php")
]

try:
    # Connect to FTP
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(ftp_path)
    
    # Set binary mode
    ftp.voidcmd('TYPE I')
    
    print(f"Connected to FTP server in binary mode")
    print("-" * 50)
    
    # First, try to delete and reupload
    for local_file, remote_file in files_to_upload:
        try:
            # Try to delete first
            try:
                ftp.delete(remote_file)
                print(f"✓ Deleted old {remote_file}")
            except:
                print(f"⚠ Could not delete {remote_file}")
            
            # Upload the file
            local_path = os.path.join("/Applications/XAMPP/xamppfiles/htdocs", local_file)
            if os.path.exists(local_path):
                file_size = os.path.getsize(local_path)
                
                with open(local_path, 'rb') as file:
                    response = ftp.storbinary(f'STOR {remote_file}', file)
                    print(f"✓ Uploaded: {local_file} ({file_size} bytes)")
                    print(f"  Response: {response}")
                    
                # Try to verify size
                try:
                    server_size = ftp.size(remote_file)
                    print(f"  Server size: {server_size} bytes")
                    if server_size == file_size:
                        print(f"  ✓ Size verified!")
                    else:
                        print(f"  ⚠ Size mismatch!")
                except:
                    pass
                    
            else:
                print(f"✗ Local file not found: {local_file}")
                
        except Exception as e:
            print(f"✗ Error with {local_file}: {str(e)}")
    
    print("-" * 50)
    print(f"Force reupload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
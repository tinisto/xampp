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
    print("-" * 50)
    
    # Upload the fixed cards-grid.php
    local_file = "/Applications/XAMPP/xamppfiles/htdocs/common-components/cards-grid.php"
    remote_file = "/common-components/cards-grid.php"
    
    with open(local_file, 'rb') as file:
        response = ftp.storbinary(f'STOR {remote_file}', file)
        file_size = os.path.getsize(local_file)
        print(f"✓ Uploaded: cards-grid.php -> {remote_file} ({file_size} bytes)")
        print(f"  Response: {response}")
    
    # Verify
    try:
        server_size = ftp.size(remote_file)
        print(f"✓ Verified: Server file size = {server_size} bytes")
    except:
        print("⚠ Could not verify size")
    
    # Also upload test-cards-component.php again
    test_file = "/Applications/XAMPP/xamppfiles/htdocs/test-cards-component.php"
    if os.path.exists(test_file):
        with open(test_file, 'rb') as f:
            ftp.storbinary('STOR test-cards-component.php', f)
            print("✓ Re-uploaded test-cards-component.php")
    
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
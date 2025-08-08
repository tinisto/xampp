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
    
    print(f"Connected to FTP server")
    print("-" * 50)
    
    # Check cards-grid.php
    remote_file = "/common-components/cards-grid.php"
    try:
        size = ftp.size(remote_file)
        print(f"Server cards-grid.php size: {size} bytes")
        
        # Download to compare
        local_file = "/Applications/XAMPP/xamppfiles/htdocs/cards-grid-from-server.php"
        with open(local_file, 'wb') as f:
            ftp.retrbinary(f'RETR {remote_file}', f.write)
        print(f"Downloaded to: cards-grid-from-server.php")
        
        # Check first few lines
        with open(local_file, 'r') as f:
            lines = f.readlines()[:10]
            print("\nFirst 10 lines:")
            for line in lines:
                print(line.rstrip())
                
    except Exception as e:
        print(f"Error checking cards-grid.php: {e}")
    
    print("-" * 50)
    
    # Check category.php
    remote_file = "/pages/category/category.php"
    try:
        size = ftp.size(remote_file)
        print(f"\nServer category.php size: {size} bytes")
        
        # Get modification time
        mdtm = ftp.sendcmd(f'MDTM {remote_file}')
        print(f"Last modified: {mdtm}")
        
    except Exception as e:
        print(f"Error checking category.php: {e}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
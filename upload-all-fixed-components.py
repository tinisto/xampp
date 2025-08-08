import ftplib
import os
from datetime import datetime

# FTP credentials
ftp_host = "ftp.ipage.com"
ftp_user = "franko"
ftp_pass = "JyvR!HK2E!N55Zt"
ftp_path = "/11klassnikiru"

# Component files to upload
component_files = [
    ("common-components/cards-grid.php", "/common-components/cards-grid.php"),
    ("common-components/search-inline.php", "/common-components/search-inline.php"),
    ("common-components/filters-dropdown.php", "/common-components/filters-dropdown.php"),
    ("common-components/real_title.php", "/common-components/real_title.php")
]

try:
    # Connect to FTP
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(ftp_path)
    
    print(f"Connected to FTP server: {ftp_host}")
    print(f"Current directory: {ftp.pwd()}")
    print("-" * 50)
    
    # Upload each component file
    for local_file, remote_file in component_files:
        try:
            local_path = os.path.join("/Applications/XAMPP/xamppfiles/htdocs", local_file)
            
            if os.path.exists(local_path):
                with open(local_path, 'rb') as file:
                    response = ftp.storbinary(f'STOR {remote_file}', file)
                    file_size = os.path.getsize(local_path)
                    print(f"✓ Uploaded: {local_file} ({file_size} bytes)")
                    print(f"  Response: {response}")
            else:
                print(f"✗ File not found: {local_file}")
                
        except Exception as e:
            print(f"✗ Failed to upload {local_file}: {str(e)}")
    
    print("-" * 50)
    print("Summary of fixed components:")
    print("1. cards-grid.php - Now has proper renderCardsGrid function")
    print("2. search-inline.php - Now has proper renderSearchInline function")
    print("3. filters-dropdown.php - Now has proper renderFiltersDropdown function")
    print("4. real_title.php - Updated to support subtitles")
    print("-" * 50)
    print(f"Upload completed at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
import ftplib
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
    
    print(f"Connected to: {ftp.pwd()}")
    
    # Upload test file
    with open('/Applications/XAMPP/xamppfiles/htdocs/test-upload.php', 'rb') as f:
        resp = ftp.storbinary('STOR test-upload.php', f)
        print(f"Upload test-upload.php: {resp}")
    
    # Check if our files exist by trying to get their size
    files_to_check = ['rename-category-id.php', 'debug-category-enhanced.php', 'debug-category.php', 'test-upload.php']
    
    print("\nChecking file sizes on server:")
    for fname in files_to_check:
        try:
            size = ftp.size(fname)
            print(f"✓ {fname}: {size} bytes")
        except Exception as e:
            print(f"✗ {fname}: Not found or error - {str(e)}")
    
    # Try to list files starting with 'debug' or 'rename'
    print("\nListing debug/rename files:")
    files = []
    try:
        ftp.retrlines('LIST debug*.php', files.append)
        ftp.retrlines('LIST rename*.php', files.append)
        for f in files:
            print(f"  {f}")
    except:
        print("  Could not list files")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {str(e)}")
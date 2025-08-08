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
    
    print(f"Connected to FTP server")
    print("-" * 50)
    
    # First, rename the old category-new.php
    try:
        old_name = "category-new.php"
        new_name = f"category-new.php.backup_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        ftp.rename(old_name, new_name)
        print(f"✓ Renamed {old_name} to {new_name}")
    except Exception as e:
        print(f"⚠ Could not rename old file: {e}")
    
    # Upload our working category-new.php
    local_file = "/Applications/XAMPP/xamppfiles/htdocs/category-new.php"
    with open(local_file, 'rb') as f:
        response = ftp.storbinary('STOR category-new.php', f)
        print(f"✓ Uploaded category-new.php")
        print(f"  Response: {response}")
    
    # Also upload the fixed version
    with open('/Applications/XAMPP/xamppfiles/htdocs/category-new-fixed.php', 'rb') as f:
        ftp.storbinary('STOR category-new-fixed.php', f)
        print("✓ Uploaded category-new-fixed.php")
    
    # List files to verify
    print("\nCategory-related files:")
    files = []
    ftp.retrlines('LIST category*', files.append)
    for f in files[-5:]:
        print(f"  {f}")
    
    print("-" * 50)
    print("Done!")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
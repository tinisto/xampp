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
    
    # Upload working version
    with open('/Applications/XAMPP/xamppfiles/htdocs/category-working.php', 'rb') as f:
        ftp.storbinary('STOR category-working.php', f)
        print("✓ Uploaded category-working.php")
    
    # Upload simple router
    with open('/Applications/XAMPP/xamppfiles/htdocs/category-new-simple.php', 'rb') as f:
        ftp.storbinary('STOR category-new-simple.php', f)
        print("✓ Uploaded category-new-simple.php")
    
    # Backup current category-new.php and replace with simple version
    try:
        backup_name = f"category-new.php.complex_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        ftp.rename('category-new.php', backup_name)
        print(f"✓ Backed up category-new.php to {backup_name}")
    except:
        pass
    
    # Copy simple version to category-new.php
    with open('/Applications/XAMPP/xamppfiles/htdocs/category-new-simple.php', 'rb') as f:
        ftp.storbinary('STOR category-new.php', f)
        print("✓ Replaced category-new.php with simple working version")
    
    print("-" * 50)
    print("✅ Working category system uploaded!")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
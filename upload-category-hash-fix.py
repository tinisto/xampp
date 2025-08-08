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
    
    # Upload fixed category page
    with open('/Applications/XAMPP/xamppfiles/htdocs/category-working.php', 'rb') as f:
        ftp.storbinary('STOR category-working.php', f)
        print("✓ Uploaded category-working.php")
    
    print("-" * 50)
    print("✅ Category hash fix uploaded successfully!")
    print("\nThe following improvements have been made:")
    print("1. Removes # from URL on page load")
    print("2. Prevents links ending with # from adding hash")
    print("3. No more page blinking when clicking categories")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
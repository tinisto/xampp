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
    
    # Upload template with Bootstrap
    with open('/Applications/XAMPP/xamppfiles/htdocs/real_template.php', 'rb') as f:
        ftp.storbinary('STOR real_template.php', f)
        print("✓ Uploaded real_template.php (with Bootstrap JS)")
    
    # Upload header with debugging
    with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/real_header.php', 'rb') as f:
        ftp.cwd(ftp_path + '/common-components')
        ftp.storbinary('STOR real_header.php', f)
        print("✓ Uploaded common-components/real_header.php (with console debugging)")
    
    print("-" * 50)
    print("✅ Bootstrap and debugging fixes uploaded successfully!")
    print("\nThe following improvements have been made:")
    print("1. Added Bootstrap JavaScript to enable dropdown functionality")
    print("2. Added console logging to debug dropdown issues")
    print("3. Manual Bootstrap dropdown initialization if needed")
    print("\nPlease check the browser console for debug messages when clicking Categories")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
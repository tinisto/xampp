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
    
    # Upload simplified header
    with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/real_header.php', 'rb') as f:
        ftp.cwd(ftp_path + '/common-components')
        ftp.storbinary('STOR real_header.php', f)
        print("✓ Uploaded common-components/real_header.php (simplified)")
    
    # Upload cleaned footer
    with open('/Applications/XAMPP/xamppfiles/htdocs/common-components/real_footer.php', 'rb') as f:
        ftp.storbinary('STOR real_footer.php', f)
        print("✓ Uploaded common-components/real_footer.php (cleaned)")
    
    print("-" * 50)
    print("✅ Simplified dropdown handlers uploaded!")
    print("\nChanges made:")
    print("1. Removed interference with Bootstrap on desktop")
    print("2. Removed duplicate click handlers")
    print("3. Let Bootstrap handle dropdowns naturally")
    print("4. Only manual handling for mobile")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
    import traceback
    traceback.print_exc()
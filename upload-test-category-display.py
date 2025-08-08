import ftplib
import os

# FTP credentials
ftp_host = "ftp.ipage.com"
ftp_user = "franko"
ftp_pass = "JyvR!HK2E!N55Zt"
ftp_path = "/11klassnikiru"

try:
    ftp = ftplib.FTP(ftp_host)
    ftp.login(ftp_user, ftp_pass)
    ftp.cwd(ftp_path)
    
    with open('/Applications/XAMPP/xamppfiles/htdocs/test-category-display.php', 'rb') as f:
        ftp.storbinary('STOR test-category-display.php', f)
        print("✓ Uploaded test-category-display.php")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {str(e)}")
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
    
    with open('/Applications/XAMPP/xamppfiles/htdocs/debug-category-final.php', 'rb') as f:
        ftp.storbinary('STOR debug-category-final.php', f)
        print("✓ Uploaded debug-category-final.php")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {str(e)}")
import ftplib

ftp = ftplib.FTP("ftp.ipage.com")
ftp.login("franko", "JyvR!HK2E!N55Zt")
ftp.cwd("/11klassnikiru")

with open('/Applications/XAMPP/xamppfiles/htdocs/test-category-step-by-step.php', 'rb') as f:
    ftp.storbinary('STOR test-category-step-by-step.php', f)
    print('✓ Uploaded test-category-step-by-step.php')

ftp.quit()
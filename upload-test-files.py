import ftplib
ftp = ftplib.FTP("ftp.ipage.com")
ftp.login("franko", "JyvR!HK2E!N55Zt")
ftp.cwd("/11klassnikiru")
files = ["test-direct-category.php", "category-test-new.php"]
for f in files:
    with open(f'/Applications/XAMPP/xamppfiles/htdocs/{f}', 'rb') as file:
        ftp.storbinary(f'STOR {f}', file)
        print(f"✓ Uploaded {f}")
ftp.quit()
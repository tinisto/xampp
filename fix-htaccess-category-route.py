import ftplib

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
    
    print("Fixing .htaccess category routing...")
    
    # Download current .htaccess
    with open('htaccess-temp.txt', 'wb') as f:
        ftp.retrbinary('RETR .htaccess', f.write)
    
    # Read and modify
    with open('htaccess-temp.txt', 'r') as f:
        content = f.read()
    
    # Fix the routing - change pages/category/category-new.php to category-new.php
    content = content.replace(
        'RewriteRule ^category/([^/]+)/?$ pages/category/category-new.php?category_en=$1',
        'RewriteRule ^category/([^/]+)/?$ category-new.php?category_en=$1'
    )
    
    # Write back
    with open('htaccess-fixed.txt', 'w') as f:
        f.write(content)
    
    # Upload
    with open('htaccess-fixed.txt', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    
    print("✓ Fixed .htaccess routing")
    print("  Changed: pages/category/category-new.php → category-new.php")
    
    # Verify the change
    with open('htaccess-verify.txt', 'wb') as f:
        ftp.retrbinary('RETR .htaccess', f.write)
    
    with open('htaccess-verify.txt', 'r') as f:
        lines = f.readlines()
        for i, line in enumerate(lines):
            if 'category' in line.lower() and 'RewriteRule' in line:
                print(f"  Line {i+1}: {line.strip()}")
    
    ftp.quit()
    print("\n✅ Category routing should now work properly!")
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
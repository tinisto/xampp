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
    
    print("Checking category routing files...")
    print("-" * 50)
    
    # Download and check category.php
    try:
        with open('category-root-check.php', 'wb') as f:
            ftp.retrbinary('RETR category.php', f.write)
        
        with open('category-root-check.php', 'r') as f:
            content = f.read()
            print("Content of root category.php:")
            print(content[:200] + "..." if len(content) > 200 else content)
            print("-" * 50)
    except Exception as e:
        print(f"Could not read category.php: {e}")
    
    # Check .htaccess routing
    print("\nChecking .htaccess for category routing...")
    with open('htaccess-check.txt', 'wb') as f:
        ftp.retrbinary('RETR .htaccess', f.write)
    
    with open('htaccess-check.txt', 'r') as f:
        lines = f.readlines()
        for i, line in enumerate(lines):
            if 'category' in line.lower() and 'RewriteRule' in line:
                print(f"Line {i+1}: {line.strip()}")
    
    # Close connection
    ftp.quit()
    
except Exception as e:
    print(f"FTP Error: {str(e)}")
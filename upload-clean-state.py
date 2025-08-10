#!/usr/bin/env python3
import ftplib

# FTP credentials  
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def upload_key_files():
    try:
        print("Uploading clean state without debug colors...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Upload key template files to remove debug colors
        files_to_upload = [
            'common-components/real_header.php',
            'common-components/real_footer.php',
            'real_template.php'
        ]
        
        for file_path in files_to_upload:
            try:
                with open(file_path, 'rb') as f:
                    ftp.storbinary(f'STOR {file_path}', f)
                print(f"✓ Uploaded: {file_path}")
            except Exception as e:
                print(f"- Could not upload {file_path}: {e}")
        
        ftp.quit()
        print("\n✓ Clean state uploaded - no debug colors")
        print("Test: https://11klassniki.ru")
        
    except Exception as e:
        print(f"✗ Error: {e}")

if __name__ == "__main__":
    upload_key_files()
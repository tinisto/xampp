#!/usr/bin/env python3
"""
FTP Upload Script with Multiple Methods
Uploads specific files to FTP server using various approaches
"""

import ftplib
import os
import subprocess
import time
from pathlib import Path

# FTP Configuration
FTP_HOST = "77.232.131.89"
FTP_USER = "8b6cdc76_sitearchive"
FTP_PASS = "jU9%mHr1"
FTP_DIR = "/domains/11klassniki.ru/public_html"

# Files to upload
FILES_TO_UPLOAD = [
    "common-components/test-card.php",
    "pages/tests/tests-main-content.php"
]

def upload_with_ftplib_passive(timeout=30):
    """Try uploading files using ftplib with passive mode"""
    print(f"\n[Method 1] Trying ftplib with passive mode (timeout={timeout}s)...")
    
    try:
        # Connect to FTP server
        ftp = ftplib.FTP()
        ftp.set_debuglevel(2)  # Enable debug output
        ftp.connect(FTP_HOST, 21, timeout=timeout)
        ftp.login(FTP_USER, FTP_PASS)
        
        # Set passive mode
        ftp.set_pasv(True)
        
        # Change to target directory
        ftp.cwd(FTP_DIR)
        
        # Upload each file
        for file_path in FILES_TO_UPLOAD:
            local_file = os.path.join(os.getcwd(), file_path)
            
            if not os.path.exists(local_file):
                print(f"❌ File not found: {local_file}")
                continue
                
            # Create directory structure if needed
            remote_dir = os.path.dirname(file_path)
            if remote_dir:
                try:
                    # Try to create nested directories
                    dirs = remote_dir.split('/')
                    for i in range(len(dirs)):
                        subdir = '/'.join(dirs[:i+1])
                        try:
                            ftp.mkd(subdir)
                            print(f"Created directory: {subdir}")
                        except:
                            pass  # Directory might already exist
                except Exception as e:
                    print(f"Directory creation note: {e}")
            
            # Upload file
            with open(local_file, 'rb') as f:
                remote_file = file_path
                print(f"Uploading {file_path}...")
                ftp.storbinary(f'STOR {remote_file}', f)
                print(f"✅ Successfully uploaded: {file_path}")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ ftplib passive mode failed: {e}")
        return False

def upload_with_ftplib_active(timeout=30):
    """Try uploading files using ftplib with active mode"""
    print(f"\n[Method 2] Trying ftplib with active mode (timeout={timeout}s)...")
    
    try:
        # Connect to FTP server
        ftp = ftplib.FTP()
        ftp.set_debuglevel(2)
        ftp.connect(FTP_HOST, 21, timeout=timeout)
        ftp.login(FTP_USER, FTP_PASS)
        
        # Set active mode
        ftp.set_pasv(False)
        
        # Change to target directory
        ftp.cwd(FTP_DIR)
        
        # Upload each file
        for file_path in FILES_TO_UPLOAD:
            local_file = os.path.join(os.getcwd(), file_path)
            
            if not os.path.exists(local_file):
                print(f"❌ File not found: {local_file}")
                continue
                
            # Create directory structure if needed
            remote_dir = os.path.dirname(file_path)
            if remote_dir:
                try:
                    dirs = remote_dir.split('/')
                    for i in range(len(dirs)):
                        subdir = '/'.join(dirs[:i+1])
                        try:
                            ftp.mkd(subdir)
                        except:
                            pass
                except:
                    pass
            
            # Upload file
            with open(local_file, 'rb') as f:
                remote_file = file_path
                print(f"Uploading {file_path}...")
                ftp.storbinary(f'STOR {remote_file}', f)
                print(f"✅ Successfully uploaded: {file_path}")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ ftplib active mode failed: {e}")
        return False

def upload_with_curl():
    """Try uploading files using curl command"""
    print("\n[Method 3] Trying curl command...")
    
    success = True
    for file_path in FILES_TO_UPLOAD:
        local_file = os.path.join(os.getcwd(), file_path)
        
        if not os.path.exists(local_file):
            print(f"❌ File not found: {local_file}")
            success = False
            continue
        
        # Create full remote path
        remote_path = f"{FTP_DIR}/{file_path}"
        
        # Build curl command
        curl_cmd = [
            'curl',
            '-T', local_file,
            '--ftp-create-dirs',
            '--user', f'{FTP_USER}:{FTP_PASS}',
            f'ftp://{FTP_HOST}{remote_path}'
        ]
        
        print(f"Uploading {file_path} with curl...")
        try:
            result = subprocess.run(curl_cmd, capture_output=True, text=True, timeout=60)
            if result.returncode == 0:
                print(f"✅ Successfully uploaded: {file_path}")
            else:
                print(f"❌ curl failed for {file_path}: {result.stderr}")
                success = False
        except subprocess.TimeoutExpired:
            print(f"❌ curl timed out for {file_path}")
            success = False
        except Exception as e:
            print(f"❌ curl error for {file_path}: {e}")
            success = False
    
    return success

def upload_with_lftp():
    """Try uploading files using lftp command"""
    print("\n[Method 4] Trying lftp command...")
    
    # Check if lftp is available
    try:
        subprocess.run(['which', 'lftp'], capture_output=True, check=True)
    except:
        print("❌ lftp not installed, skipping this method")
        return False
    
    # Create lftp script
    lftp_commands = f"""
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 3
open ftp://{FTP_USER}:{FTP_PASS}@{FTP_HOST}
cd {FTP_DIR}
"""
    
    for file_path in FILES_TO_UPLOAD:
        local_file = os.path.join(os.getcwd(), file_path)
        if os.path.exists(local_file):
            remote_dir = os.path.dirname(file_path)
            if remote_dir:
                lftp_commands += f"mkdir -p {remote_dir}\n"
            lftp_commands += f"put {local_file} -o {file_path}\n"
    
    lftp_commands += "bye\n"
    
    try:
        result = subprocess.run(
            ['lftp', '-c', lftp_commands],
            capture_output=True,
            text=True,
            timeout=120
        )
        
        if result.returncode == 0:
            print("✅ Successfully uploaded all files with lftp")
            return True
        else:
            print(f"❌ lftp failed: {result.stderr}")
            return False
            
    except Exception as e:
        print(f"❌ lftp error: {e}")
        return False

def main():
    """Main function to try all upload methods"""
    print("FTP Upload Script")
    print("=================")
    print(f"Host: {FTP_HOST}")
    print(f"User: {FTP_USER}")
    print(f"Target directory: {FTP_DIR}")
    print(f"Files to upload: {', '.join(FILES_TO_UPLOAD)}")
    
    # Check if files exist
    print("\nChecking local files...")
    all_exist = True
    for file_path in FILES_TO_UPLOAD:
        local_file = os.path.join(os.getcwd(), file_path)
        if os.path.exists(local_file):
            size = os.path.getsize(local_file)
            print(f"✅ {file_path} ({size} bytes)")
        else:
            print(f"❌ {file_path} - NOT FOUND")
            all_exist = False
    
    if not all_exist:
        print("\n❌ Some files are missing! Cannot proceed.")
        return
    
    # Try different methods
    methods = [
        (upload_with_ftplib_passive, [30]),
        (upload_with_ftplib_passive, [60]),
        (upload_with_ftplib_active, [30]),
        (upload_with_curl, []),
        (upload_with_lftp, [])
    ]
    
    for method, args in methods:
        if method(*args):
            print("\n✅ Upload successful!")
            return
        time.sleep(2)  # Brief pause between attempts
    
    print("\n❌ All upload methods failed. Please check:")
    print("1. FTP credentials are correct")
    print("2. FTP server is accessible")
    print("3. Network connection is stable")
    print("4. Firewall is not blocking FTP")

if __name__ == "__main__":
    main()
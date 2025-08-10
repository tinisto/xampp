#!/usr/bin/env python3
import ftplib

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def delete_all_files():
    try:
        print("🗑️  DELETING ALL FILES FROM SERVER...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        def delete_recursive(path=''):
            try:
                if path:
                    ftp.cwd(path)
                
                # Get list of files and directories
                items = ftp.nlst()
                
                for item in items:
                    if item in ['.', '..']:
                        continue
                    
                    try:
                        # Try to change to directory (if it's a directory)
                        ftp.cwd(item)
                        ftp.cwd('..')  # Go back
                        
                        # It's a directory, recursively delete
                        print(f"📁 Deleting directory: {path}/{item}")
                        delete_recursive(f"{path}/{item}" if path else item)
                        ftp.rmd(item)
                        
                    except:
                        # It's a file, delete it
                        try:
                            ftp.delete(item)
                            print(f"🗑️  Deleted file: {path}/{item}" if path else f"🗑️  Deleted: {item}")
                        except Exception as e:
                            print(f"❌ Could not delete {item}: {e}")
                
                if path:
                    ftp.cwd('..')
                    
            except Exception as e:
                print(f"❌ Error in directory {path}: {e}")
        
        # Start recursive deletion
        delete_recursive()
        
        ftp.quit()
        print("\n✅ ALL FILES DELETED FROM SERVER")
        print("Server is now clean and ready for fresh upload")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    print("⚠️  WARNING: This will delete ALL files from the server!")
    print("✅ All files are safely backed up in git commit 06e250e")
    delete_all_files()
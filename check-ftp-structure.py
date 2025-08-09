#!/usr/bin/env python3
import ftplib

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def list_directory(ftp, path="/"):
    """List contents of a directory"""
    try:
        ftp.cwd(path)
        items = []
        ftp.retrlines('LIST', items.append)
        return items
    except:
        return []

def check_structure():
    """Check FTP directory structure"""
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected\n")
        
        # Check root directory
        print(f"Contents of {FTP_ROOT}:")
        root_items = list_directory(ftp, FTP_ROOT)
        for item in root_items[:20]:  # Show first 20 items
            print(f"  {item}")
        
        # Check if api directory exists
        print(f"\nChecking {FTP_ROOT}/api:")
        api_items = list_directory(ftp, FTP_ROOT + "/api")
        if api_items:
            for item in api_items[:10]:
                print(f"  {item}")
        else:
            print("  Directory not found or empty")
        
        # Check if api/comments exists
        print(f"\nChecking {FTP_ROOT}/api/comments:")
        comments_items = list_directory(ftp, FTP_ROOT + "/api/comments")
        if comments_items:
            for item in comments_items:
                print(f"  {item}")
        else:
            print("  Directory not found or empty")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    check_structure()
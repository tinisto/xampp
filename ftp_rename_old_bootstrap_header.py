#!/usr/bin/env python3

import ftplib

# FTP Configuration
FTP_HOST = "11klassniki.ru"
FTP_USER = "franko"
FTP_PASS = """JyvR!HK2E!N55Zt"""

def rename_file(ftp, old_name, new_name):
    """Rename a file on FTP server"""
    try:
        ftp.rename(old_name, new_name)
        print(f"✅ Renamed: {old_name} -> {new_name}")
        return True
    except Exception as e:
        print(f"❌ Failed to rename {old_name}: {str(e)}")
        return False

def main():
    print("🔧 Renaming old Bootstrap header to prevent usage...")
    
    try:
        # Connect to FTP
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected successfully")
        
        # Change to 11klassnikiru directory
        try:
            ftp.cwd('11klassnikiru')
            print("✅ Changed to 11klassnikiru directory")
        except Exception as e:
            print(f"❌ Could not change to 11klassnikiru: {e}")
            return
        
        # Rename old header
        print("\n📝 Renaming old header file...")
        rename_file(ftp, 
                   'common-components/header-unified.php', 
                   'common-components/header-unified-bootstrap.old')
        
        print("\n✅ Old Bootstrap header renamed to prevent confusion")
        print("   Now ALL pages use the vanilla CSS/JS header!")
        
        ftp.quit()
        print("\n📡 Connection closed")
        
    except Exception as e:
        print(f"❌ FTP error: {e}")

if __name__ == "__main__":
    main()
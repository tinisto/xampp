#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        # Upload database mismatch scanner
        with open("/Applications/XAMPP/xamppfiles/htdocs/scan-database-mismatches.php", 'rb') as f:
            ftp.storbinary('STOR scan-database-mismatches.php', f)
        
        print("✅ Uploaded comprehensive database scanner")
        print("\n🔍 DATABASE FIELD MISMATCH SCANNER DEPLOYED:")
        print("  📊 Visit: https://11klassniki.ru/scan-database-mismatches.php")
        print("\n🎯 This scanner will:")
        print("  ✅ Get schema of all database tables")
        print("  ✅ Scan all PHP files for field references") 
        print("  ✅ Identify mismatched field names")
        print("  ✅ Suggest correct field names")
        print("  ✅ Show common field mappings")
        print("\n⚠️ Note: This scan may take a few minutes to complete")
        print("📝 Results will show exactly which files need field name fixes")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
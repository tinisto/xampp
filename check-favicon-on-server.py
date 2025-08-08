#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        print("🔍 Checking for any remaining favicon.php files...")
        
        # Check if favicon.php exists anywhere
        try:
            size = ftp.size('common-components/favicon.php')
            print(f"❌ FOUND: common-components/favicon.php ({size} bytes) - this should be deleted!")
        except:
            print("✅ GOOD: common-components/favicon.php does not exist")
        
        # Check for any other favicon.php files
        try:
            file_list = []
            ftp.retrlines('LIST', file_list.append)
            
            favicon_files = [line for line in file_list if 'favicon.php' in line.lower()]
            if favicon_files:
                print(f"❌ FOUND other favicon.php files:")
                for f in favicon_files:
                    print(f"  {f}")
            else:
                print("✅ GOOD: No favicon.php files found in root")
        except Exception as e:
            print(f"Could not list files: {e}")
        
        # Download current news-new.php to check what it includes
        print("\n🔍 Checking news-new.php content...")
        try:
            with open('temp_news-new.php', 'wb') as f:
                ftp.retrbinary('RETR news-new.php', f.write)
            
            with open('temp_news-new.php', 'r') as f:
                content = f.read()
                if 'favicon.php' in content:
                    print("❌ news-new.php contains favicon.php reference!")
                    lines = content.split('\n')
                    for i, line in enumerate(lines, 1):
                        if 'favicon.php' in line:
                            print(f"  Line {i}: {line.strip()}")
                else:
                    print("✅ GOOD: news-new.php does not contain favicon.php references")
        except Exception as e:
            print(f"Could not check news-new.php: {e}")
        
        # Check pages/common/news/news.php
        print("\n🔍 Checking pages/common/news/news.php content...")
        try:
            with open('temp_news.php', 'wb') as f:
                ftp.retrbinary('RETR pages/common/news/news.php', f.write)
            
            with open('temp_news.php', 'r') as f:
                content = f.read()
                if 'favicon.php' in content:
                    print("❌ pages/common/news/news.php contains favicon.php reference!")
                    lines = content.split('\n')
                    for i, line in enumerate(lines, 1):
                        if 'favicon.php' in line:
                            print(f"  Line {i}: {line.strip()}")
                else:
                    print("✅ GOOD: pages/common/news/news.php does not contain favicon.php references")
        except Exception as e:
            print(f"Could not check pages/common/news/news.php: {e}")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
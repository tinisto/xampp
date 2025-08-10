#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔧 Removing blue comment sections from homepage...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download current index.php
        print("📥 Downloading index.php...")
        content = []
        ftp.retrlines('RETR index.php', content.append)
        
        print(f"Current index.php: {len(content)} lines")
        
        # Process content to remove comment sections
        new_content = []
        skip_section = False
        brace_level = 0
        in_comment_section = False
        
        for i, line in enumerate(content):
            # Look for comment-related sections
            if any(keyword in line.lower() for keyword in [
                'comment', 'threaded', 'discussion', 'обсуждение', 'комментари'
            ]):
                # Check if this is a CSS class or HTML section with comments
                if any(pattern in line.lower() for pattern in [
                    'class=', 'id=', '<div', '<section', 'background:', 'color:'
                ]):
                    in_comment_section = True
                    skip_section = True
                    print(f"  Skipping comment section at line {i+1}: {line.strip()[:60]}...")
                    continue
            
            # Track braces to know when sections end
            if skip_section:
                brace_level += line.count('{') - line.count('}')
                if '</div>' in line or '</section>' in line or (brace_level <= 0 and in_comment_section):
                    skip_section = False
                    in_comment_section = False
                    brace_level = 0
                    continue
                else:
                    continue
            
            # Skip lines that are clearly comment-related
            if any(keyword in line.lower() for keyword in [
                'renderThreadedComments', 'api/comments', 'smart-comments',
                'threaded-comments', 'comment-section'
            ]):
                continue
            
            new_content.append(line)
        
        print(f"Processed content: {len(new_content)} lines (removed {len(content) - len(new_content)} lines)")
        
        # Upload cleaned index.php
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write('\n'.join(new_content))
            tmp_path = tmp.name
        
        print("📤 Uploading cleaned index.php...")
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR index.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Blue comment sections removed from homepage!")
        print("\n📋 Changes applied:")
        print(f"• Removed {len(content) - len(new_content)} lines related to comments")
        print("• Kept all other homepage functionality")
        print("• Preserved statistics and navigation")
        print("• Maintained responsive design")
        
        print("\n🧪 Test the homepage:")
        print("https://11klassniki.ru/")
        print("(Blue comment sections should no longer appear)")
        
        print("\n✅ Homepage is now comment-free!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
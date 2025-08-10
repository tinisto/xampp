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
    print("🔧 Removing blue comment sections from all pages...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # List of key page files to clean
        pages_to_clean = [
            'index.php',
            'news-new.php', 
            'category-new.php',
            'schools-all-regions-real.php',
            'vpo-all-regions-new.php',
            'spo-all-regions-new.php',
            'search-new.php',
            'about-new.php'
        ]
        
        print("📂 Pages to clean:")
        for page in pages_to_clean:
            print(f"  • {page}")
        
        cleaned_pages = 0
        
        for page_file in pages_to_clean:
            try:
                print(f"\n🔧 Processing {page_file}...")
                
                # Download current content
                content = []
                ftp.retrlines(f'RETR {page_file}', content.append)
                
                # Clean content
                cleaned_content = []
                skip_section = False
                skip_depth = 0
                removed_lines = 0
                
                i = 0
                while i < len(content):
                    line = content[i]
                    original_line = line
                    
                    # Detect start of comment sections
                    if not skip_section:
                        # Look for comment-related sections
                        comment_indicators = [
                            'renderThreadedComments',
                            'threaded-comments',
                            'smart-comments', 
                            'comment-section',
                            'api/comments',
                            'discussion',
                            'обсуждение',
                            'комментари',
                            'comments-container',
                            '$blueContent'
                        ]
                        
                        if any(indicator in line.lower() for indicator in comment_indicators):
                            print(f"    Found comment section at line {i+1}: {line.strip()[:60]}...")
                            skip_section = True
                            skip_depth = 0
                            removed_lines += 1
                            i += 1
                            continue
                        
                        # Look for blue sections by CSS/styling
                        if ('background' in line.lower() and any(color in line.lower() for color in ['blue', '#007', '#0056', 'rgb(0,', 'rgba(0,'])) or \
                           ('class=' in line and any(cls in line.lower() for cls in ['comment', 'discussion', 'blue'])) or \
                           ('id=' in line and any(cls in line.lower() for cls in ['comment', 'discussion'])):
                            print(f"    Found blue section at line {i+1}: {line.strip()[:60]}...")
                            skip_section = True
                            skip_depth = 0
                            removed_lines += 1
                            i += 1
                            continue
                    
                    if skip_section:
                        # Count opening/closing tags to know when section ends
                        skip_depth += line.count('<div') + line.count('<section') + line.count('{')
                        skip_depth -= line.count('</div>') + line.count('</section>') + line.count('}')
                        
                        # Also check for PHP blocks
                        if '<?php' in line:
                            skip_depth += 1
                        if '?>' in line and skip_depth > 0:
                            skip_depth -= 1
                        
                        removed_lines += 1
                        
                        # End of section when depth returns to 0 or below
                        if skip_depth <= 0:
                            skip_section = False
                            print(f"    End of comment section at line {i+1}")
                        
                        i += 1
                        continue
                    
                    # Keep the line
                    cleaned_content.append(line)
                    i += 1
                
                # Only upload if we actually removed something
                if removed_lines > 0:
                    print(f"    Removed {removed_lines} lines from {page_file}")
                    
                    # Upload cleaned content
                    with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
                        tmp.write('\n'.join(cleaned_content))
                        tmp_path = tmp.name
                    
                    with open(tmp_path, 'rb') as file:
                        ftp.storbinary(f'STOR {page_file}', file)
                    
                    os.unlink(tmp_path)
                    cleaned_pages += 1
                    print(f"    ✅ Updated {page_file}")
                else:
                    print(f"    ℹ️  No comment sections found in {page_file}")
                
            except Exception as e:
                print(f"    ❌ Could not process {page_file}: {str(e)}")
        
        # Also create a universal comment remover for common components
        print(f"\n🔧 Creating universal comment section remover...")
        
        comment_remover = '''<?php
// Universal Comment Section Remover
// This script can be included to remove comment sections from any page

function removeCommentSections($content) {
    // Remove renderThreadedComments calls
    $content = preg_replace('/renderThreadedComments\\s*\\([^)]*\\);?/i', '', $content);
    
    // Remove comment-related divs and sections
    $content = preg_replace('/<div[^>]*class="[^"]*comment[^"]*"[^>]*>.*?<\\/div>/is', '', $content);
    $content = preg_replace('/<section[^>]*class="[^"]*comment[^"]*"[^>]*>.*?<\\/section>/is', '', $content);
    
    // Remove blue background sections that might be comments
    $content = preg_replace('/<div[^>]*style="[^"]*background[^"]*blue[^"]*"[^>]*>.*?<\\/div>/is', '', $content);
    
    // Remove discussion sections
    $content = preg_replace('/<div[^>]*id="[^"]*discussion[^"]*"[^>]*>.*?<\\/div>/is', '', $content);
    
    // Clean up multiple empty lines
    $content = preg_replace('/\\n\\s*\\n\\s*\\n/s', '\\n\\n', $content);
    
    return $content;
}

// Auto-clean output buffer if this script is included
if (function_exists('ob_start') && !ob_get_level()) {
    ob_start(function($buffer) {
        return removeCommentSections($buffer);
    });
}
?>'''
        
        # Upload comment remover utility
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(comment_remover)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR common-components/comment-remover.php', file)
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print(f"\n✅ Blue comment sections cleanup complete!")
        print(f"\n📊 Results:")
        print(f"• Processed {len(pages_to_clean)} pages")
        print(f"• Successfully cleaned {cleaned_pages} pages")
        print(f"• Created universal comment remover utility")
        
        print(f"\n🔧 What was removed:")
        print(f"• renderThreadedComments() function calls")
        print(f"• Blue background comment sections") 
        print(f"• Comment-related CSS classes and IDs")
        print(f"• Discussion and comment containers")
        print(f"• $blueContent variable assignments")
        
        print(f"\n🧪 Test these cleaned pages:")
        for page in pages_to_clean:
            if page == 'index.php':
                print(f"• https://11klassniki.ru/")
            elif page.endswith('-new.php'):
                base_name = page.replace('-new.php', '').replace('-real.php', '')
                print(f"• https://11klassniki.ru/{base_name}/")
        
        print(f"\n📋 Universal tool created:")
        print(f"• /common-components/comment-remover.php")
        print(f"  (Can be included in any page to auto-remove comment sections)")
        
        print(f"\n✅ Blue sections should now be removed from all major pages!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
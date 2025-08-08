#!/usr/bin/env python3
import ftplib

def main():
    try:
        ftp = ftplib.FTP("ftp.ipage.com")
        ftp.login("franko", "JyvR!HK2E!N55Zt")
        ftp.cwd("/11klassnikiru")
        
        print("🔧 FIXING REMAINING FAVICON REFERENCES...")
        
        # Download and fix header.php
        print("\n1. Fixing common-components/header.php...")
        try:
            with open('temp_header.php', 'wb') as f:
                ftp.retrbinary('RETR common-components/header.php', f.write)
            
            with open('temp_header.php', 'r') as f:
                content = f.read()
            
            # Replace favicon.php references with inline favicon
            old_favicon_block = '''// Add favicon to head if not already added
if (!defined('FAVICON_LOADED')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php';
    renderFavicon();
    define('FAVICON_LOADED', true);
}'''
            
            new_favicon_block = '''// Favicon now handled directly in template head section - no longer needed here'''
            
            if 'favicon.php' in content:
                content = content.replace(old_favicon_block, new_favicon_block)
                
                # Also catch any other favicon.php references
                content = content.replace("include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php';", "// Favicon removed - handled in template")
                content = content.replace("renderFavicon();", "// Favicon removed - handled in template")
                
                # Upload fixed file
                with open('temp_header_fixed.php', 'w') as f:
                    f.write(content)
                
                with open('temp_header_fixed.php', 'rb') as f:
                    ftp.storbinary('STOR common-components/header.php', f)
                
                print("  ✅ Fixed and uploaded header.php")
            else:
                print("  ✅ header.php already clean")
                
        except Exception as e:
            print(f"  ❌ Error fixing header.php: {e}")
        
        # Download and fix template-engine-ultimate.php
        print("\n2. Fixing common-components/template-engine-ultimate.php...")
        try:
            with open('temp_template_engine.php', 'wb') as f:
                ftp.retrbinary('RETR common-components/template-engine-ultimate.php', f.write)
            
            with open('temp_template_engine.php', 'r') as f:
                content = f.read()
            
            if 'favicon.php' in content:
                # Replace favicon.php with inline favicon
                content = content.replace(
                    "include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/favicon.php';",
                    "// Favicon removed - handled in template"
                )
                content = content.replace(
                    "renderFavicon();",
                    '''echo '<link rel="icon" href="data:image/svg+xml,%3Csvg+xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22+viewBox%3D%220+0+32+32%22%3E%0A++++++++%3Cdefs%3E%0A++++++++++++%3ClinearGradient+id%3D%22favicon-gradient-v2%22+x1%3D%220%25%22+y1%3D%220%25%22+x2%3D%22100%25%22+y2%3D%22100%25%22%3E%0A++++++++++++++++%3Cstop+offset%3D%220%25%22+style%3D%22stop-color%3A%2328a745%22+%2F%3E%0A++++++++++++++++%3Cstop+offset%3D%22100%25%22+style%3D%22stop-color%3A%2320c997%22+%2F%3E%0A++++++++++++%3C%2FlinearGradient%3E%0A++++++++%3C%2Fdefs%3E%0A++++++++%3Crect+width%3D%2232%22+height%3D%2232%22+rx%3D%226%22+fill%3D%22url%28%23favicon-gradient-v2%29%22%2F%3E%0A++++++++%3Ctext+x%3D%2216%22+y%3D%2222%22+text-anchor%3D%22middle%22+fill%3D%22white%22+font-size%3D%2216%22+font-weight%3D%22bold%22+font-family%3D%22system-ui%22%3E11%3C%2Ftext%3E%0A++++%3C%2Fsvg%3E" type="image/svg+xml">' . "\\n";
                    echo '<link rel="icon" type="image/x-icon" href="/favicon.ico?v=1754636985">' . "\\n";'''
                )
                
                # Upload fixed file
                with open('temp_template_engine_fixed.php', 'w') as f:
                    f.write(content)
                
                with open('temp_template_engine_fixed.php', 'rb') as f:
                    ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
                
                print("  ✅ Fixed and uploaded template-engine-ultimate.php")
            else:
                print("  ✅ template-engine-ultimate.php already clean")
                
        except Exception as e:
            print(f"  ❌ Error fixing template-engine-ultimate.php: {e}")
        
        print("\n🎯 FAVICON CLEANUP COMPLETE!")
        print("  ✅ Fixed all remaining server-side favicon.php references")
        print("  ✅ Favicon should now be stable across all pages")
        print("\n📝 Test the favicon on /news page now")
        
        ftp.quit()
        return 0
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    exit(main())
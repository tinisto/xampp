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
    print("🎨 ADDING DEBUG COLORS DIRECTLY TO CATEGORY PAGE")
    print("Since category page is self-contained, add colors directly to it")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download current category page
        category_content = []
        ftp.retrlines('RETR category-new.php', category_content.append)
        print(f"Downloaded category page: {len(category_content)} lines")
        
        # Find where to inject debug colors (in the <style> section)
        updated_content = []
        colors_added = False
        
        for i, line in enumerate(category_content):
            updated_content.append(line)
            
            # Add debug colors after existing styles
            if '</style>' in line and not colors_added:
                # Insert debug colors before </style>
                debug_css = [
                    '',
                    '        /* DEBUG COLORS - CATEGORY PAGE */',
                    '        nav, .navbar {',
                    '            background-color: #ff0000 !important; /* RED HEADER */',
                    '            border-bottom: 3px solid #cc0000;',
                    '        }',
                    '',
                    '        .navbar-brand, .nav-link, .navbar-nav a {',
                    '            color: white !important;',
                    '            font-weight: bold !important;',
                    '        }',
                    '',
                    '        .container {',
                    '            background-color: #00ff00 !important; /* GREEN MAIN */',
                    '            border: 2px solid #00cc00;',
                    '            margin: 10px auto;',
                    '            padding: 20px;',
                    '        }',
                    '',
                    '        .card {',
                    '            background-color: #66ff66 !important;',
                    '            border: 1px solid #00cc00 !important;',
                    '        }',
                    '',
                    '        h1, h2, h3, p {',
                    '            background-color: rgba(255,255,0,0.7) !important;',
                    '            display: inline-block;',
                    '            padding: 3px 8px;',
                    '            margin: 2px 0;',
                    '        }',
                    '',
                    '        footer {',
                    '            background-color: #ffff00 !important; /* YELLOW FOOTER */',
                    '            border: 3px solid #cccc00;',
                    '            color: black !important;',
                    '        }',
                    '',
                    '        footer h5, footer p, footer a {',
                    '            color: black !important;',
                    '        }',
                    '',
                    '        .breadcrumb {',
                    '            background-color: #ccffcc !important;',
                    '        }',
                    ''
                ]
                
                # Remove the current </style> line
                updated_content.pop()
                
                # Add debug CSS
                for debug_line in debug_css:
                    updated_content.append(debug_line)
                
                # Add back the </style>
                updated_content.append('    </style>')
                colors_added = True
        
        if not colors_added:
            print("   ⚠️  Could not find <style> section to modify")
        
        # Upload the modified category page
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write('\\n'.join(updated_content))
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR category-new.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Added debug colors directly to category page")
        
        ftp.quit()
        
        print("\\n🎨 DEBUG COLORS ADDED TO CATEGORY PAGE!")
        
        print("\\n🔧 Colors added:")
        print("🔴 Navigation: RED background") 
        print("🟢 Main content: GREEN background")
        print("🟡 Footer: YELLOW background")
        print("💛 Text: Yellow highlights")
        
        print("\\n🧪 Test both pages now:")
        print("• https://11klassniki.ru/news (RED header, template system)")
        print("• https://11klassniki.ru/category/abiturientam (RED header, self-contained)")
        
        print("\\n🎯 Now you should see:")
        print("✅ Both pages have RED headers")
        print("✅ Both pages have GREEN main content")  
        print("✅ Both pages have YELLOW footers")
        print("❓ But they might still have different layouts/structure")
        
        print("\\n💡 Clear cache (Ctrl+Shift+R) and compare!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
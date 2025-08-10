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
    print("🔍 CHECKING WHICH TEMPLATE IS BEING USED")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Download the test page we just created
        print("\n1️⃣ Checking test-ipage-connection.php...")
        
        with tempfile.NamedTemporaryFile(delete=False) as tmp:
            tmp_path = tmp.name
        
        ftp.retrbinary('RETR test-ipage-connection.php', open(tmp_path, 'wb').write)
        
        with open(tmp_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        print("   File content preview:")
        if 'include' in content:
            for line in content.split('\n'):
                if 'include' in line or 'require' in line:
                    print(f"   Found: {line.strip()}")
        
        os.unlink(tmp_path)
        
        # Check what favicon files exist
        print("\n2️⃣ Checking favicon files...")
        
        try:
            files = ftp.nlst()
            favicon_files = [f for f in files if 'favicon' in f.lower() or f.endswith('.ico')]
            for f in favicon_files:
                print(f"   Found: {f}")
        except:
            print("   Could not list files")
        
        # Check if the test page is using a different template
        print("\n3️⃣ Creating template check page...")
        
        check_content = '''<?php
echo "<!DOCTYPE html><html><head><title>Template Check</title></head><body>";
echo "<h1>Template System Check</h1>";
echo "<pre>";

// Check which files this page would include
echo "1. Current file: " . __FILE__ . "\n";
echo "2. Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n\n";

// Check if real_template.php exists
$template_file = $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
if (file_exists($template_file)) {
    echo "✓ real_template.php exists\n";
} else {
    echo "✗ real_template.php NOT found\n";
}

// Check header/footer
$header = $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_header.php';
$footer = $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_footer.php';

echo "\nTemplate files:\n";
echo "Header: " . (file_exists($header) ? "✓ exists" : "✗ missing") . "\n";
echo "Footer: " . (file_exists($footer) ? "✓ exists" : "✗ missing") . "\n";

// Show what favicon is in the header
if (file_exists($header)) {
    $header_content = file_get_contents($header);
    if (preg_match('/<link[^>]*favicon[^>]*>/i', $header_content, $matches)) {
        echo "\nFavicon in header:\n";
        echo $matches[0] . "\n";
    }
}

echo "</pre>";

// Now include the template to see the favicon
echo "<h2>Testing template inclusion:</h2>";

$greyContent1 = '<div>Test content 1</div>';
$greyContent2 = '<div>Test content 2</div>';
$greyContent3 = '<div>Test content 3</div>';
$greyContent4 = '<div>Test content 4</div>';
$greyContent5 = '<div>Test content 5</div>';
$greyContent6 = '<div>Test content 6</div>';

if (file_exists($template_file)) {
    include $template_file;
} else {
    echo "<p>Cannot include template - file not found</p>";
}
?>'''
        
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(check_content)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR check-template-system.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Created template check page")
        
        ftp.quit()
        
        print("\n✅ Template check created!")
        print("\n🎯 Visit: https://11klassniki.ru/check-template-system.php")
        print("\nThis will show:")
        print("• Which template files exist")
        print("• What favicon is being used")
        print("• Whether pages are using the template system")
        
        print("\n⚠️  The different favicon means:")
        print("• Some pages are NOT using the template system")
        print("• Or there are multiple favicon definitions")
        print("• We need to track down where this other favicon is coming from!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()
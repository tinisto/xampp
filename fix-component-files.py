#!/usr/bin/env python3
"""Fix component compatibility files"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Fixed component files that avoid circular includes
component_files = {
    'common-components/search-inline.php': '''<?php
/**
 * Inline Search Component
 * Wrapper for renderSearchInline function
 */

if (!function_exists('renderSearchInline')) {
    function renderSearchInline($options = []) {
        // Load the function from real_components.php if needed
        static $loaded = false;
        if (!$loaded) {
            $file = $_SERVER['DOCUMENT_ROOT'] . '/real_components.php';
            if (file_exists($file)) {
                // Include the file but prevent it from executing as a page
                $_SERVER['SCRIPT_NAME_BACKUP'] = $_SERVER['SCRIPT_NAME'];
                $_SERVER['SCRIPT_NAME'] = '/dummy.php';
                include_once $file;
                $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME_BACKUP'];
                $loaded = true;
            }
        }
        
        // If function still doesn't exist, provide fallback
        if (!function_exists('renderSearchInline_real') && function_exists('renderSearchInline')) {
            // Function was loaded, call it
            return;
        }
        
        // Fallback implementation
        $placeholder = $options['placeholder'] ?? 'Search...';
        $buttonText = $options['buttonText'] ?? 'Search';
        $width = $options['width'] ?? '300px';
        ?>
        <form action="/search" method="get" style="display: inline-flex; gap: 10px;">
            <input type="text" name="q" placeholder="<?= htmlspecialchars($placeholder) ?>" 
                   style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: <?= $width ?>;">
            <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                <?= htmlspecialchars($buttonText) ?>
            </button>
        </form>
        <?php
    }
}
?>''',
    
    'common-components/cards-grid.php': '''<?php
/**
 * Cards Grid Component
 * Wrapper for renderCardsGrid function
 */

if (!function_exists('renderCardsGrid')) {
    function renderCardsGrid($items = [], $type = 'news', $options = []) {
        // Simple fallback implementation
        $columns = $options['columns'] ?? 3;
        $gap = $options['gap'] ?? 20;
        ?>
        <div style="display: grid; grid-template-columns: repeat(<?= $columns ?>, 1fr); gap: <?= $gap ?>px;">
            <?php foreach ($items as $item): ?>
                <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                    <h3><?= htmlspecialchars($item['title_news'] ?? 'No title') ?></h3>
                    <p>Card content here</p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
?>''',
    
    'common-components/filters-dropdown.php': '''<?php
/**
 * Filters Dropdown Component
 * Wrapper for renderFiltersDropdown function
 */

if (!function_exists('renderFiltersDropdown')) {
    function renderFiltersDropdown($options = []) {
        // Simple fallback implementation
        ?>
        <select style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <option>Sort by date</option>
            <option>Sort by popularity</option>
        </select>
        <?php
    }
}
?>'''
}

try:
    print("Fixing component files...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    for filepath, content in component_files.items():
        # Save locally
        filename = filepath.split('/')[-1]
        with open(filename, 'w') as f:
            f.write(content)
        
        # Upload
        with open(filename, 'rb') as f:
            ftp.storbinary(f'STOR {filepath}', f)
        print(f"✓ Fixed {filepath}")
    
    ftp.quit()
    
    print("\n✅ Component files fixed!")
    print("\nNow test again:")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/news-working.php")
    
except Exception as e:
    print(f"Error: {e}")
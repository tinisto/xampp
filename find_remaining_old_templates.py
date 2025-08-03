#!/usr/bin/env python3
"""
Find all files still using old template engines
"""

import os
from pathlib import Path

def check_file(file_path):
    """Check if a file uses old templates"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Skip if using ultimate template
        if 'template-engine-ultimate.php' in content:
            return None
            
        # Check if it has renderTemplate
        if 'renderTemplate(' not in content:
            return None
            
        # Check which old template it uses
        old_templates = {
            'template-engine.php': 'default',
            'template-engine-no-header.php': 'no-header',
            'template-engine-modern.php': 'modern',
            'template-engine-dashboard.php': 'dashboard',
            'template-engine-no-bootstrap.php': 'no-bootstrap',
            'template-engine-unified.php': 'unified',
            'template-engine-old.php': 'old',
            'template-engine-vpo-spo.php': 'vpo-spo',
            'template-engine-search.php': 'search',
            'template-engine-nofollow.php': 'nofollow'
        }
        
        for template, template_type in old_templates.items():
            if template in content and 'common-components/' in content:
                return template_type
                
        return None
        
    except Exception as e:
        return None

def main():
    root_dir = Path('/Applications/XAMPP/xamppfiles/htdocs')
    
    old_template_files = []
    
    # Skip patterns
    skip_patterns = [
        'template-engine-',  # Template files themselves
        'ftp_',
        'migrate_',
        '.py',
        'vendor/',
        'node_modules/',
        'backup',
        'test-',
        'debug',
        'trace'
    ]
    
    for php_file in root_dir.rglob('*.php'):
        file_str = str(php_file)
        
        # Skip certain files
        if any(pattern in file_str for pattern in skip_patterns):
            continue
            
        template_type = check_file(php_file)
        if template_type:
            old_template_files.append((php_file.relative_to(root_dir), template_type))
    
    print("📋 Files still using OLD template engines:\n")
    
    # Group by template type
    by_type = {}
    for file_path, template_type in old_template_files:
        if template_type not in by_type:
            by_type[template_type] = []
        by_type[template_type].append(file_path)
    
    for template_type, files in by_type.items():
        print(f"\n🔸 Using template-engine-{template_type}.php ({len(files)} files):")
        for f in files[:10]:  # Show first 10
            print(f"   - {f}")
        if len(files) > 10:
            print(f"   ... and {len(files) - 10} more")
    
    print(f"\n📊 Total files still using old templates: {len(old_template_files)}")
    
    # Save list to file for migration
    with open('files_to_migrate.txt', 'w') as f:
        for file_path, _ in old_template_files:
            f.write(f"{file_path}\n")
    print("\n✅ Saved list to files_to_migrate.txt")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
"""
Script to automatically migrate remaining pages to template-engine-ultimate.php
Focus on production pages, skip test/debug files
"""

import os
import re
from pathlib import Path

# Templates to replace
OLD_TEMPLATES = [
    'template-engine.php',
    'template-engine-no-header.php', 
    'template-engine-modern.php',
    'template-engine-dashboard.php'
]

# Pages to prioritize for migration (production critical)
PRIORITY_PAGES = [
    'pages/dashboard/',
    'pages/common/educational-institutions-in-town/',
    'pages/common/news/',
    'pages/vpo/edit/',
    'pages/spo/edit/',
    'pages/school/edit/'
]

def should_migrate_file(file_path):
    """Determine if a file should be migrated"""
    file_str = str(file_path)
    
    # Skip test files and backups
    skip_patterns = [
        'test-', 'debug-', '-test.php', '-debug.php', '-backup.php',
        'check-', 'trace', 'minimal.php', 'working.php', 'unified.php',
        'old.php', 'modern.php'
    ]
    
    for pattern in skip_patterns:
        if pattern in file_str:
            return False
    
    # Check if it's a priority production page
    for priority in PRIORITY_PAGES:
        if priority in file_str:
            return True
    
    return False

def migrate_file(file_path):
    """Migrate a single file to use template-engine-ultimate.php"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Common migration patterns
        migrations = [
            # Basic template engine includes
            (r'include\s+\$_SERVER\[\'DOCUMENT_ROOT\'\]\s*\.\s*\'\/common-components\/template-engine\.php\';',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
            
            (r'include\s+\$_SERVER\[\'DOCUMENT_ROOT\'\]\s*\.\s*\'\/common-components\/template-engine-dashboard\.php\';',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
             
            (r'include\s+\$_SERVER\[\'DOCUMENT_ROOT\'\]\s*\.\s*\'\/common-components\/template-engine-modern\.php\';',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
             
            (r'include\s+\$_SERVER\[\'DOCUMENT_ROOT\'\]\s*\.\s*\'\/common-components\/template-engine-no-header\.php\';',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
        ]
        
        # Apply migrations
        for old_pattern, new_pattern in migrations:
            content = re.sub(old_pattern, new_pattern, content)
        
        # Add template configuration if missing and renderTemplate exists
        if 'renderTemplate(' in content and 'templateConfig' not in content:
            # Insert template config before renderTemplate call
            render_match = re.search(r'renderTemplate\s*\([^)]+\);', content)
            if render_match:
                render_call = render_match.group(0)
                
                # Determine appropriate config based on file path
                if 'dashboard' in str(file_path):
                    layout_type = 'dashboard'
                elif 'edit-form' in str(file_path) or 'create' in str(file_path):
                    layout_type = 'auth'
                else:
                    layout_type = 'default'
                
                template_config = f'''
// Template configuration
$templateConfig = [
    'layoutType' => '{layout_type}',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

'''
                # Insert before renderTemplate
                content = content.replace(render_call, template_config + render_call)
                
                # Update renderTemplate call to use config
                if '$templateConfig' not in render_call:
                    # Extract parameters from existing call
                    params = re.search(r'renderTemplate\s*\(([^)]+)\)', render_call)
                    if params:
                        param_str = params.group(1)
                        if param_str.count(',') == 1:  # Two parameters
                            content = content.replace(render_call, 
                                render_call.replace(param_str, param_str + ', $templateConfig'))
        
        # Only write if content changed
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True
        
        return False
        
    except Exception as e:
        print(f"Error migrating {file_path}: {e}")
        return False

def main():
    """Main migration function"""
    print("🚀 Starting automatic template migration...")
    
    root_dir = Path('/Applications/XAMPP/xamppfiles/htdocs')
    migrated_count = 0
    total_checked = 0
    
    # Find all PHP files
    for php_file in root_dir.rglob('*.php'):
        # Skip the template engine files themselves
        if 'template-engine' in php_file.name:
            continue
            
        # Check if file uses old templates
        try:
            with open(php_file, 'r', encoding='utf-8') as f:
                content = f.read()
                
            uses_old_template = any(template in content for template in OLD_TEMPLATES)
            
            if uses_old_template and should_migrate_file(php_file):
                total_checked += 1
                print(f"📝 Migrating: {php_file.relative_to(root_dir)}")
                
                if migrate_file(php_file):
                    migrated_count += 1
                    print(f"   ✅ Successfully migrated")
                else:
                    print(f"   ⚠️  No changes needed")
                    
        except Exception as e:
            print(f"❌ Error checking {php_file}: {e}")
    
    print(f"\n📊 Migration Summary:")
    print(f"   📋 Files checked: {total_checked}")
    print(f"   ✅ Successfully migrated: {migrated_count}")
    print(f"   ⏭️  Skipped (test/debug files): {len(list(root_dir.rglob('*.php'))) - total_checked}")
    
    if migrated_count > 0:
        print(f"\n🎉 Migrated {migrated_count} production pages to template-engine-ultimate.php!")
        print("   The site is now much closer to using ONE TEMPLATE system.")
    else:
        print("\n✨ All priority production pages already use the ultimate template!")

if __name__ == "__main__":
    main()
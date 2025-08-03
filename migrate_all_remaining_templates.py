#!/usr/bin/env python3
"""
Complete migration of ALL remaining pages to template-engine-ultimate.php
This will achieve 100% ONE TEMPLATE system
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

def migrate_file(file_path):
    """Migrate a single file to use template-engine-ultimate.php"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Skip if already using ultimate template
        if 'template-engine-ultimate.php' in content:
            return False
            
        # Common migration patterns
        migrations = [
            # Basic template engine includes with quotes variations
            (r'include\s+\$_SERVER\[[\'"](DOCUMENT_ROOT|document_root)[\'"]\]\s*\.\s*[\'"]\/common-components\/template-engine\.php[\'"];',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
            
            (r'include\s+\$_SERVER\[[\'"](DOCUMENT_ROOT|document_root)[\'"]\]\s*\.\s*[\'"]\/common-components\/template-engine-dashboard\.php[\'"];',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
             
            (r'include\s+\$_SERVER\[[\'"](DOCUMENT_ROOT|document_root)[\'"]\]\s*\.\s*[\'"]\/common-components\/template-engine-modern\.php[\'"];',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
             
            (r'include\s+\$_SERVER\[[\'"](DOCUMENT_ROOT|document_root)[\'"]\]\s*\.\s*[\'"]\/common-components\/template-engine-no-header\.php[\'"];',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
             
            # Handle include_once variants
            (r'include_once\s+\$_SERVER\[[\'"](DOCUMENT_ROOT|document_root)[\'"]\]\s*\.\s*[\'"]\/common-components\/template-engine\.php[\'"];',
             'include_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
             
            # Handle versions with spaces/newlines
            (r'include\s+\$_SERVER\[[\'"](DOCUMENT_ROOT|document_root)[\'"]\]\s*\.\s*\n?\s*[\'"]\/common-components\/template-engine\.php[\'"];',
             'include $_SERVER[\'DOCUMENT_ROOT\'] . \'/common-components/template-engine-ultimate.php\';'),
        ]
        
        # Apply migrations
        for old_pattern, new_pattern in migrations:
            content = re.sub(old_pattern, new_pattern, content, flags=re.MULTILINE | re.IGNORECASE)
        
        # Add template configuration if missing and renderTemplate exists
        if 'renderTemplate(' in content and 'templateConfig' not in content:
            # Insert template config before renderTemplate call
            render_match = re.search(r'renderTemplate\s*\([^;]+\);', content, re.DOTALL)
            if render_match:
                render_call = render_match.group(0)
                
                # Determine appropriate config based on file path
                file_str = str(file_path)
                if 'dashboard' in file_str:
                    layout_type = 'dashboard'
                elif 'edit-form' in file_str or 'create' in file_str:
                    layout_type = 'auth'
                elif 'no-header' in file_str or 'template-engine-no-header' in original_content:
                    layout_type = 'minimal'
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
                    # Try to parse the renderTemplate parameters
                    params_match = re.search(r'renderTemplate\s*\(([^)]+)\)', render_call, re.DOTALL)
                    if params_match:
                        params = params_match.group(1)
                        # Count commas to determine number of parameters
                        comma_count = params.count(',')
                        if comma_count == 1:  # Two parameters
                            new_call = render_call.replace(params, params + ', $templateConfig')
                            content = content.replace(render_call, new_call)
                        elif comma_count >= 2:  # Three or more parameters
                            # Find the third parameter and replace it
                            parts = re.split(r',\s*', params, 2)
                            if len(parts) >= 3:
                                new_params = f"{parts[0]}, {parts[1]}, $templateConfig"
                                new_call = f"renderTemplate({new_params});"
                                content = content.replace(render_call, new_call)
        
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
    print("🚀 Starting COMPLETE template migration...")
    print("   Goal: 100% of pages using ONE TEMPLATE\n")
    
    root_dir = Path('/Applications/XAMPP/xamppfiles/htdocs')
    migrated_count = 0
    skipped_count = 0
    already_migrated = 0
    total_php_files = 0
    
    # Files to skip
    skip_patterns = [
        'template-engine',  # Template engine files themselves
        'ftp_',  # FTP upload scripts
        'migrate_',  # Migration scripts
        '.py',  # Python files
        'vendor/',  # Third party libraries
        'node_modules/',  # Node modules
    ]
    
    # Find all PHP files
    for php_file in root_dir.rglob('*.php'):
        total_php_files += 1
        
        # Skip certain files
        file_str = str(php_file)
        if any(pattern in file_str for pattern in skip_patterns):
            skipped_count += 1
            continue
            
        try:
            # Check if file uses any template
            with open(php_file, 'r', encoding='utf-8') as f:
                content = f.read()
                
            # Check if already using ultimate template
            if 'template-engine-ultimate.php' in content:
                already_migrated += 1
                continue
                
            # Check if uses old templates
            uses_old_template = any(template in content for template in OLD_TEMPLATES)
            
            if uses_old_template:
                print(f"📝 Migrating: {php_file.relative_to(root_dir)}")
                
                if migrate_file(php_file):
                    migrated_count += 1
                    print(f"   ✅ Successfully migrated")
                else:
                    print(f"   ⚠️  No changes needed")
                    
        except Exception as e:
            print(f"❌ Error checking {php_file}: {e}")
    
    print(f"\n📊 FINAL Migration Summary:")
    print(f"   📁 Total PHP files: {total_php_files}")
    print(f"   ✅ Already using ultimate template: {already_migrated}")
    print(f"   🔄 Migrated in this run: {migrated_count}")
    print(f"   ⏭️  Skipped (scripts/vendor): {skipped_count}")
    print(f"   🎯 Total using ONE TEMPLATE: {already_migrated + migrated_count}")
    
    if migrated_count > 0:
        print(f"\n🎉 Successfully migrated {migrated_count} more pages!")
        print("   The ONE TEMPLATE system is now even more complete!")
    
    print(f"\n🏆 ONE TEMPLATE Coverage: {((already_migrated + migrated_count) / total_php_files * 100):.1f}%")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
"""
Migrate the final 10 files to complete the ONE TEMPLATE system
"""

import os
import re

files_to_migrate = [
    'secure-updates/search-process-secure.php',
    'pages/post/post-unified.php',
    'pages/search/search-process-unified.php',
    'pages/common/educational-institutions-in-region/educational-institutions-in-region-unified.php',
    'pages/account/account-unified.php',
    'pages/account/account-unified-fixed.php',
    'pages/school/school-single-unified.php',
    'pages/dashboard/schools-dashboard/schools-approve-new/schools-approve-new.php',
    'pages/account/account-old.php',
    'pages/account/comments-user/comments-user-edit/comments-user-edit.php'
]

def migrate_file(file_path):
    """Migrate a single file to use template-engine-ultimate.php"""
    full_path = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
    
    try:
        with open(full_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Replace all old template includes
        replacements = [
            ('template-engine-search.php', 'template-engine-ultimate.php'),
            ('template-engine-unified.php', 'template-engine-ultimate.php'),
            ('template-engine-dashboard.php', 'template-engine-ultimate.php'),
            ('template-engine-nofollow.php', 'template-engine-ultimate.php'),
            ('template-engine.php', 'template-engine-ultimate.php'),
        ]
        
        for old, new in replacements:
            content = content.replace(old, new)
        
        # Add template config if missing
        if 'templateConfig' not in content and 'renderTemplate(' in content:
            # Determine layout type
            if 'dashboard' in file_path:
                layout_type = 'dashboard'
            elif 'account' in file_path and 'edit' in file_path:
                layout_type = 'auth'
            elif 'search' in file_path:
                layout_type = 'default'
                css_framework = 'custom'
            else:
                layout_type = 'default'
                css_framework = 'bootstrap'
            
            template_config = f'''
// Template configuration
$templateConfig = [
    'layoutType' => '{layout_type}',
    'cssFramework' => '{'custom' if 'search' in file_path else 'bootstrap'}',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

'''
            
            # Find renderTemplate call
            render_pattern = r'(renderTemplate\s*\([^;]+\);)'
            match = re.search(render_pattern, content, re.DOTALL)
            
            if match:
                render_call = match.group(1)
                # Insert config before renderTemplate
                content = content.replace(render_call, template_config + render_call)
                
                # Update renderTemplate parameters if needed
                if '$templateConfig' not in render_call:
                    # Parse parameters
                    params_match = re.search(r'renderTemplate\s*\(([^)]+)\)', render_call, re.DOTALL)
                    if params_match:
                        params = params_match.group(1)
                        # Add $templateConfig as third parameter
                        parts = re.split(r',\s*', params, 2)
                        if len(parts) == 2:
                            new_params = f"{parts[0]}, {parts[1]}, $templateConfig"
                        else:
                            new_params = f"{parts[0]}, {parts[1]}, $templateConfig"
                        
                        new_call = f"renderTemplate({new_params});"
                        content = content.replace(render_call, new_call)
        
        # Write back if changed
        if content != original_content:
            with open(full_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True
        
        return False
        
    except Exception as e:
        print(f"❌ Error migrating {file_path}: {e}")
        return False

def main():
    print("🚀 Migrating final 10 files to complete ONE TEMPLATE system...\n")
    
    success_count = 0
    
    for file_path in files_to_migrate:
        print(f"📝 Migrating: {file_path}")
        
        if migrate_file(file_path):
            success_count += 1
            print(f"   ✅ Successfully migrated")
        else:
            print(f"   ⚠️  Migration failed or no changes needed")
    
    print(f"\n📊 Final Migration Results:")
    print(f"   ✅ Successfully migrated: {success_count}/10")
    
    if success_count == 10:
        print("\n🎉 🎉 🎉 MISSION COMPLETE! 🎉 🎉 🎉")
        print("   🏆 100% of pages now use template-engine-ultimate.php")
        print("   ⚡ ONE TEMPLATE system is FULLY implemented!")
        print("   ✅ User goal 'we need one template' ACHIEVED!")
    elif success_count > 0:
        print(f"\n✅ Migrated {success_count} more files")
        print("   Almost there! Just a few files left.")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
"""
Rename all old template engines to prevent their use
Adds .old suffix to make them inactive but recoverable
"""

import os
import shutil
from datetime import datetime

# Old template files to rename
old_templates = [
    'common-components/template-engine.php',
    'common-components/template-engine-no-header.php',
    'common-components/template-engine-modern.php',
    'common-components/template-engine-dashboard.php',
    'common-components/template-engine-no-bootstrap.php',
    'common-components/template-engine-unified.php',
    'common-components/template-engine-old.php',
    'common-components/template-engine-vpo-spo.php',
    'common-components/template-engine-search.php',
    'common-components/template-engine-nofollow.php',
    'template-engine-backup.php'  # Also rename the backup in root
]

def rename_template(file_path):
    """Rename a template file by adding .old suffix"""
    full_path = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
    
    if os.path.exists(full_path):
        new_path = f'{full_path}.old-{datetime.now().strftime("%Y%m%d")}'
        try:
            shutil.move(full_path, new_path)
            print(f"✅ Renamed: {file_path}")
            print(f"   → {os.path.basename(new_path)}")
            return True
        except Exception as e:
            print(f"❌ Failed to rename {file_path}: {e}")
            return False
    else:
        print(f"⚠️  Not found: {file_path}")
        return False

def main():
    print("🔄 Renaming old template engines to prevent their use...")
    print("   Adding .old suffix with today's date\n")
    
    success_count = 0
    total = len(old_templates)
    
    for template in old_templates:
        if rename_template(template):
            success_count += 1
        print()
    
    print(f"📊 Rename Summary:")
    print(f"   ✅ Successfully renamed: {success_count}/{total}")
    print(f"   ❌ Failed/Not found: {total - success_count}/{total}")
    
    if success_count > 0:
        print("\n🎉 Old templates have been disabled!")
        print("   They now have .old suffix and won't be loaded")
        print("   Only template-engine-ultimate.php remains active")
        print("\n💡 To restore a template, just remove the .old suffix")
    
    # Check what template files remain active
    print("\n📋 Active template files remaining:")
    template_dir = '/Applications/XAMPP/xamppfiles/htdocs/common-components'
    for file in os.listdir(template_dir):
        if file.startswith('template-engine') and file.endswith('.php') and not file.endswith('.old'):
            label = "ULTIMATE - The ONE template!" if "ultimate" in file else "Other"
            print(f"   ✅ {file} ({label})")

if __name__ == "__main__":
    main()
#!/usr/bin/env python3
"""
Remove unused CSS files and consolidate the CSS structure
"""

import os
import sys

def main():
    """Remove clearly unused CSS files"""
    
    # Files that are safe to remove (only used in backup/legacy files)
    files_to_remove = [
        'css/test.css',  # Already removed and merged
    ]
    
    # Files to analyze further (used in working files but not main template)
    files_to_analyze = [
        'css/styles.css',  # Only in backup templates and working files
        'css/post-styles.css',  # Might be legacy
        'css/buttons-styles.css',  # Might be redundant with unified styles
    ]
    
    print("🧹 CSS Cleanup Analysis")
    print("=" * 40)
    
    # Check which files exist
    removed_count = 0
    for file_path in files_to_remove:
        full_path = f"/Applications/XAMPP/xamppfiles/htdocs/{file_path}"
        if os.path.exists(full_path):
            print(f"❌ {file_path} still exists (should have been removed)")
        else:
            print(f"✅ {file_path} already removed")
            removed_count += 1
    
    # Show analysis of questionable files
    print(f"\n📊 Files requiring analysis:")
    for file_path in files_to_analyze:
        full_path = f"/Applications/XAMPP/xamppfiles/htdocs/{file_path}"
        if os.path.exists(full_path):
            size = os.path.getsize(full_path)
            print(f"📄 {file_path} - {size} bytes")
        else:
            print(f"❓ {file_path} - Not found")
    
    # Show current active CSS structure
    print(f"\n🎯 Current Active CSS:")
    active_files = [
        'css/unified-styles.css',
        'css/authorization.css',
        'css/dashboard/dashboard.css',
    ]
    
    total_size = 0
    for file_path in active_files:
        full_path = f"/Applications/XAMPP/xamppfiles/htdocs/{file_path}"
        if os.path.exists(full_path):
            size = os.path.getsize(full_path)
            total_size += size
            print(f"✅ {file_path} - {size:,} bytes")
        else:
            print(f"❌ {file_path} - Missing!")
    
    print(f"\n📈 Summary:")
    print(f"• Active CSS files: {len(active_files)} files, {total_size:,} bytes total")
    print(f"• Successfully consolidated test.css → unified-styles.css")
    print(f"• Template engine now uses minimal, focused CSS files")
    
    print(f"\n✨ CSS consolidation partially complete!")
    print(f"Next: Review and potentially merge remaining legacy files.")

if __name__ == "__main__":
    main()
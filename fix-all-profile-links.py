#!/usr/bin/env python3
import os
import re

# List of files to update
files_to_update = [
    'dashboard-professional.php',
    'dashboard-create-content.php',
    'dashboard-edit-content.php',
    'dashboard-with-user-menu.php',
    'dashboard-news-management.php',
    'dashboard-users-professional.php',
    'dashboard-create-content-unified-backup.php',
    'dashboard-create-content-unified.php',
    'dashboard-posts-management.php'
]

# Pattern to find and replace
old_pattern = r"""                            <\?php 
                            // Get current page URL
                            \$current_url = \$_SERVER\['REQUEST_URI'\];
                            // Only show profile link if not already on account page
                            if \(\$current_url !== '/account' && \$current_url !== '/account/'\): 
                            \?>
                            <a href="/account" class="dropdown-item">
                                <span class="dropdown-icon">👤</span>
                                Мой профиль
                            </a>
                            <\?php endif; \?>"""

new_content = """                            <a href="/account" class="dropdown-item">
                                <span class="dropdown-icon">👤</span>
                                Мой аккаунт
                            </a>"""

success_count = 0
for filename in files_to_update:
    filepath = filename
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Check if the old pattern exists
        if re.search(old_pattern, content):
            # Replace the pattern
            new_content_full = re.sub(old_pattern, new_content, content)
            
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content_full)
            
            print(f"✅ Updated: {filename}")
            success_count += 1
        else:
            print(f"⚠️  Pattern not found in: {filename}")
    
    except Exception as e:
        print(f"❌ Error updating {filename}: {str(e)}")

print(f"\n✅ Successfully updated {success_count} files")
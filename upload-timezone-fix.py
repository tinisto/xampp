#!/usr/bin/env python3
"""
Upload timezone fix files to the server
This fixes the issue where comments show wrong time (hardcoded Moscow timezone)
"""

import os
import sys

print("=== Timezone Fix Upload Instructions ===")
print("\nThis fix allows comments to display in each user's actual timezone instead of hardcoded Moscow time.")
print("\nFiles to upload:")

files_to_upload = [
    # New timezone handler
    {
        'local': '/Applications/XAMPP/xamppfiles/htdocs/comments/timezone-handler.php',
        'remote': '/home/host1852849/11klassniki.ru/htdocs/www/comments/timezone-handler.php',
        'description': 'New timezone detection and conversion handler'
    },
    # Updated comment loaders
    {
        'local': '/Applications/XAMPP/xamppfiles/htdocs/comments/load_comments_simple.php',
        'remote': '/home/host1852849/11klassniki.ru/htdocs/www/comments/load_comments_simple.php',
        'description': 'Updated simple comments loader with timezone support'
    },
    {
        'local': '/Applications/XAMPP/xamppfiles/htdocs/comments/load_comments_modern.php',
        'remote': '/home/host1852849/11klassniki.ru/htdocs/www/comments/load_comments_modern.php',
        'description': 'Updated modern comments loader with timezone support'
    },
    {
        'local': '/Applications/XAMPP/xamppfiles/htdocs/comments/comment_functions.php',
        'remote': '/home/host1852849/11klassniki.ru/htdocs/www/comments/comment_functions.php',
        'description': 'Updated comment functions with timezone support'
    },
    {
        'local': '/Applications/XAMPP/xamppfiles/htdocs/comments/modern-comments-component.php',
        'remote': '/home/host1852849/11klassniki.ru/htdocs/www/comments/modern-comments-component.php',
        'description': 'Updated modern comments component with timezone detection'
    },
    # Test file (optional)
    {
        'local': '/Applications/XAMPP/xamppfiles/htdocs/test-timezone-comments.php',
        'remote': '/home/host1852849/11klassniki.ru/htdocs/www/test-timezone-comments.php',
        'description': 'Test page for timezone functionality (optional)'
    }
]

print("\n1. Upload these files via FileZilla or your FTP client:")
for i, file in enumerate(files_to_upload, 1):
    local_path = file['local']
    if os.path.exists(local_path):
        size = os.path.getsize(local_path)
        print(f"\n   {i}. {os.path.basename(local_path)} ({size} bytes)")
        print(f"      Description: {file['description']}")
        print(f"      From: {local_path}")
        print(f"      To: {file['remote']}")
    else:
        print(f"\n   {i}. WARNING: {local_path} not found!")

print("\n2. The fix works as follows:")
print("   - JavaScript detects user's timezone on first visit")
print("   - Timezone is stored in PHP session")
print("   - All comment times are converted to user's timezone")
print("   - Times now show correctly (e.g., 'just now' instead of '7 hours ago')")

print("\n3. To test after upload:")
print("   - Visit any page with comments")
print("   - Add a new comment")
print("   - It should show 'только что' (just now)")
print("   - Optional: Visit /test-timezone-comments.php to see timezone info")

print("\n4. Features:")
print("   - Automatic timezone detection")
print("   - Fallback to UTC if detection fails")
print("   - Proper Russian pluralization for time units")
print("   - Works with both simple and modern comment components")

print("\n=== Important Notes ===")
print("- The fix is backwards compatible")
print("- Existing comments will display correctly in user's timezone")
print("- No database changes required")
print("- Users don't need to do anything - it's automatic")

print("\nPress Enter to continue...")
input()
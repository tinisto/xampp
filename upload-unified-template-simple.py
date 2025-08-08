#!/usr/bin/env python3
"""
Simple upload script for unified template files
"""

import subprocess
import os

# Files to upload - only the new/modified ones
files = [
    # Core modified files
    ".htaccess",
    "index.php",
    
    # New unified template files
    "school-single-new.php",
    "schools-all-regions-real.php", 
    "schools-in-region-real.php",
    "spo-single-new.php",
    "spo-all-regions-new.php",
    "spo-in-region-new.php",
    "vpo-single-new.php",
    "vpo-all-regions-new.php",
    "vpo-in-region-new.php",
    "test-single-new.php",
    "tests-new.php",
    "search-results-new.php",
    "edu-single-new.php"
]

print(f"Uploading {len(files)} files to server...")

# Check which files exist
existing_files = []
for f in files:
    if os.path.exists(f):
        existing_files.append(f)
        print(f"✓ Found: {f}")
    else:
        print(f"✗ Missing: {f}")

print(f"\nReady to upload {len(existing_files)} files.")
print("\nPlease use FileZilla to upload these files:")
print("-" * 50)
for f in existing_files:
    print(f)
print("-" * 50)
print("\nFTP Connection Details:")
print("Host: ftp.ipage.com")
print("Username: u2709849")
print("Port: 21")
print("\nDrag files from local to remote directory.")